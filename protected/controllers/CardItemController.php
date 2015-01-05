<?php

class CardItemController extends Controller
{

    /**
     * Item列表
     * @author gentle
     */
    public function actionIndex($id)
    {
        set_time_limit(0); //防止执行超时
        //$itemModel = $this->loadModel((int)$id, 'item', 'dataset_id', true);
        $dsModel = $this->loadModel((int)$id, 'ds');					//获取表模型
        $dbModel = $this->loadModel((int)$dsModel->database_id, 'db');	//获取库模型
		$dsModel = $dsModel->sortField();
		 
        $criteria = new EMongoCriteria();
        $criteria->dataset_id = (int)$id;
        
        //添加查询条件
        if(isset($_GET['sub'])){
	        $criteria = $this->fillCond($criteria, $dsModel['fields']);
	       	//var_dump($criteria->getConditions());
    	}
    	
        $count = CardItem::model()->count($criteria);
        $pages = new CPagination($count);
        
        $perPage = 20;
        $pages->pageSize = $perPage;
        //$pages->applyLimit($criteria);
        $offset = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($offset - 1) * $perPage;
        $criteria->limit($perPage)->offset($offset)->sort('id', EMongoCriteria::SORT_DESC);
        $itemModel = CardItem::model()->findAll($criteria);

       
        $data = array();
        $data['itemModel'] = $itemModel;
        $data['dbModel'] = $dbModel;
        $data['dsModel'] = $dsModel;
        $data['datasetId'] = $dsModel->id;
        $data['dataTree'] = $this->dataTree($dbModel->id);
        $data['info'] = $this->promptInfo();
        $data['pages'] = $pages;
        $this->render('index', $data);
    }

    /**
     * 导入数据###后续重点改造对象
     * 目前导入数据，需要开发帮编辑新建xls文件，导入时还需要执行以下操作，非常不便，而且有些格式的字段处理有误
     * 会将所有字段都以文本形式入库
     * 		1 修改图片地址为本地绝对路径
	 *		2 xls文件另存为 逗号分隔的csv 文件
	 *		3 第一行空
	 *		4 内容转utf-8
	 *		5 执行导入
     * @author gentle
     */
    public function actionImport($id)
    {
        set_time_limit(0); //防止执行超时
        $itemModel = new CardItem;
        $dsModel = $this->loadModel($id, 'ds');
        $dbModel = $this->loadModel((int)$dsModel->database_id, 'db');
        $mcss = Yii::app()->mcss;

    	//获取字段和中文的对应关系
        $fields_kv = array();
       	foreach($dsModel->fields as $field_key=>$field_info){
         	$fields_kv[$field_key] =  $field_info['name'];
         	//预处理单选和复选的候选项
         	if(isset($field_info['extra']) && in_array($field_info['extra']['field_info']['addition_type'], array('select', 'multiselect'))){
         		foreach($field_info['extra']['field_info']['select_value'] as $fikey=>$fival){
         			$dsModel->fields[$field_key]['extra']['field_info']['select_value'][$fikey] = $fival['value'];
         		}
         	}
      	}
        
        if (isset($_FILES['CardItem'])) {
            $file = fopen($_FILES['CardItem']['tmp_name'], 'r');
            $csvHeader = array();
            //往卡牌库Item表导入记录
            //1条1条导入
            for ($i = 0; ; $i++) {
            	//跳过第一行中文表头
            	if(empty($i)){
            		fgetcsv($file);
            		continue;
            	//获取对应字段名
            	}if ($i == 1) {
                    $csvHeader = fgetcsv($file);
               	//获取值
                } elseif ($i >= 2) {
                    $itemData = array();
                    $csvData = fgetcsv($file);
                    if (empty($csvData)) {
                        break;
                    }
                    
                    //组字段数据临时存放点
                    $group_key = '';		//组键值
                    $group_info = array();	//字段信息
                    $group_arr = array();	//组数据
                    $group_one = array();	//一个组
                    $field_count = count($csvHeader);	//字段总数
                    $group_set = false;		//是否要进行值设置
                    
                    //逐一处理每个字段
                    foreach ($csvData as $key => $value) {
                    	//多余的字段的数据不作处理
                    	if($key>=$field_count){
                    		continue;
                    	}
                    	$value = trim(iconv('gbk','utf-8',$value));
                    	$field_key = $field_real = $csvHeader[$key];	//字段名称
                        
                    	//如果是字段组，获取组名
                    	if(strpos($field_key, '-')){
                    		$group_info = explode('-', $field_key, 2);
                    		if(isset($group_info[0])){
                    			$field_real = $group_info[0];
                    		}
                        }
                        //只导入元素集有的字段
                        if (isset($dsModel->fields[$field_real])) {
                            $tmpField = $dsModel->fields[$field_real];		//字段定义
                            //普通字段
                            if($tmpField['type']=='field'){
                            	$itemData[$field_real] = $this->formatFirldData($value, $tmpField, $mcss);	//格式化内容
                            //字段组
                            }else if($tmpField['type']=='group'){
                            	
                            	//如果有传入组内字段名才处理
                            	if(isset($group_info[1])){
	                            	$groupField = $tmpField['fields'][$group_info[1]];				//组字段定义
	                            	$groupCount = count($tmpField['fields']);						//组内字段数量
	                            	$value = $this->formatFirldData($value, $groupField, $mcss, false);	//格式化内容
	                            	
	                            	//是否是同组的，若是保存到当前组内，不是则保存之前的数据并起一个新组
	                            	if(empty($group_key) || $group_key === $field_real){
	                            		if(empty($group_key)){
	                            			$group_key = $field_real;
	                            		}
	                            		$group_one[$group_info[1]] = $value;
	                            		
	                            		//检查单组是否填满了
	                            		if(count($group_one)==$groupCount){
	                            			$group_arr[] = $group_one;
	                            			$group_one = array();	//清空单个元素
	                            		}
	                            		
	                            		//循环到了最后一个元素
	                            		if($key+1>=$field_count){
	                            			$group_set = true;
	                            		}
	                            	}else{
	                            		$group_set = true;
	                            	}
                            	}
                            	
                            	//保存并初始化状态
                            	if($group_set){
                            		if($group_one){
                            			$group_arr[] = $group_one;
                            			$group_one = array();
                            		}
                            		if($group_arr){
                            			$itemData[$field_real] = $group_arr;		//存入值
                            		}
                            		$group_key = $field_real;					//设定新组名
                            		$group_arr = array($value);					//设置第一个元素
                            		$group_set = false;
                            	}
                            	
                            }
                            
                        }
                    }
                    //echo '<pre>';
                    //print_r($itemData);exit();

                    $itemModel = new CardItem;
                    $saveData['dataset_id'] = (int)$id;
                    $saveData['data'] = $itemData;
                    $itemModel->attributes = $saveData;
                    if (!$itemModel->save()) {
                        Yii::app()->user->setFlash("error", "导入数据失败! 停止行数：" . $i);
                        $this->redirect(array('CardItem/index/id/' . $id));
                    }
                }
            }
            fclose($file);
            Yii::app()->user->setFlash("success", "导入数据成功!");
            $this->redirect(array('CardItem/index/id/' . $id));
        }

        $data = array();
        $data['model'] = $itemModel;
        $data['dsModel'] = $dsModel;
        $data['dbModel'] = $dbModel;
        $data['datasetId'] = $id;
        $this->renderPartial('_form_import', $data);
    }
    
    /**
     * 导出模板
     * @param $id
     * @return unknown_type
     */
    public function actionExportTpl($id){
    	$itemModel = new CardItem;
        $dsModel = $this->loadModel($id, 'ds');
        $dbModel = $this->loadModel((int)$dsModel->database_id, 'db');
    	
        //拼接文件名
        $file_name = $dbModel->name.'-'.$dsModel->name.'-'.date('YmdHis').'.csv';
        
    	//获取字段和中文的对应关系
    	$fields_name = array();		//中文名称
        $fields_keys = array();		//字段名
       	foreach($dsModel->fields as $field_key=>$field_info){
       		if($field_info['type'] == 'field'){
       			$fields_name[] = $field_info['name'];
       			$fields_keys[] = $field_key;
       		}else if($field_info['type'] == 'group'){
       			foreach($field_info['fields'] as $fg_key=>$fg_info){
       				$fields_name[] = $field_info['name'].'-'.$fg_info['name'];
       				$fields_keys[] = $field_key.'-'.$fg_key;
       			}
       		}
      	}
      	$fields_name = implode(',', $fields_name);
      	$fields_keys = implode(',', $fields_keys);
      	
      	$str = $fields_name."\n".$fields_keys;
    	
	    header("Content-type:text/csv");   
	    header("Content-Disposition:attachment;filename=".iconv('utf-8','gbk',$file_name));   
	    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');   
	    header('Expires:0');   
	    header('Pragma:public');   
	    echo iconv('utf-8','gbk',$str);   
	    exit();
    }
    
	/**
     * 根据字段定义格式化导入字段数据
     * @param $value		传入值
     * @param $tmpField		字段定义
     * @param $mcss			上传对象
     * @param $mcss			检查单复选的选项是否是为预定义
     * @return maxd			处理后的字段值
     */
    protected function formatFirldData($value, $tmpField, $mcss, $check_option=true){
    	//$tmpValue = mb_convert_encoding($value, 'UTF-8', 'GBK,GB2312,UTF-8');
		$tmpValue = trim($value);
        $field_type = $tmpField['extra']['field_info']['field_type'];			//字段类型
     	$addition_type = $tmpField['extra']['field_info']['addition_type'];		//附加类型
							
  		//图片类型处理
     	if( $addition_type == "image"){
        	//有地址且文件存在则尝试上传
          	if($tmpValue != '' && is_file($tmpValue)){
             	$resData = $mcss->uploadImage($tmpValue);
              	//如果返回值是数组类型，则发生了错误
               	if(is_array($resData)){
               		$tmpValue = "";
              	}else{
                   	$tmpValue = $resData;	//成功后替换原有地址
              	}
          	}else{
        		$tmpValue = "";
           	}

      	//选择框处理
       	} else if ($field_type == 'normal' && in_array($addition_type, array('select', 'multiselect'))) {
        	$tmpSelectValue = $tmpField['extra']['field_info']['select_value'];
     		if($tmpValue){
             	//多选框
            	if($addition_type=='multiselect'){
                 	$tmpValue = explode('、', $tmpValue);
                  	//检查是否为候选项
                  	
                  	foreach($tmpValue as $tkey=>$tval){
                  		$tval = trim($tval);
                   		//若不是预定义的选项则清除该项
                    	if($check_option && !in_array($tval, $tmpSelectValue)){
                        	unset($tmpValue[$tkey]);
                      	}
                   	}
               		ksort($tmpValue);	//重排索引
              		
             		//单选框
            	}else{
                	//不是预定义的选项则情况值
               		if($check_option && !in_array($tmpValue, $tmpSelectValue)){
                       	$tmpValue = '';
                  	}
              	}
      		}else {
      			if($addition_type=='multiselect'){
      				$tmpValue = array();
      			}
      		}
                          
   		//关联类型
 		}elseif ($field_type == 'reference' && $addition_type) {
            if($tmpValue){
             	$tmpValue = explode('、', $tmpValue);
               	$tmpNew = array();
                   foreach($tmpValue as $tkey=>$tval){
                     $tval = trim($tval);
                     if($tval){
                    	$tmpNew[] = $tval;
               		}
              	}
                $tmpValue = $tmpNew;
       		}else{
       			$tmpValue = array();
       		}
    	}
    	
    	return $tmpValue;                     
    }


    /**
     * 发布数据
     * @author gentle
     */
    public function actionCreate($id, $preview = false)
    {
        $dsModel = $this->loadModel($id, 'ds');
        if (isset($_POST['CardItem'])) {
            $itemModel = new CardItem;
            $itemModel->dataset_id = (int)$id;
            $itemModel->attributes = $_POST['CardItem'];
            foreach ($dsModel->fields as $key => $value) {
                if ((isset($value['must']) && $value['must'] == 1) &&
                    (!isset($itemModel->attributes['data'][$key]) ||
                        (is_string($itemModel->attributes['data'][$key]) && trim($itemModel->attributes['data'][$key]) == '') ||
                        (is_array($itemModel->attributes['data'][$key]) && empty($itemModel->attributes['data'][$key])))
                ) {
                    Yii::app()->user->setFlash("error", "必填项 {$value['name']} 无数据!");
                    $this->redirect(array('CardItem/index/id/' . $id));
                }
            }

            if ($itemModel->save()) {
                Yii::app()->user->setFlash("success", "发布数据成功!");
            } else {
                Yii::app()->user->setFlash("error", "发布数据失败!");
            }
            $this->redirect(array('CardItem/index/id/' . $id));
        }

        //构造字段Html
        $dsModel = $dsModel->sortField();		//排序字段
        $fieldHtml = '';
        foreach ($dsModel->fields as $key => $value) {
        	//普通字段
            if ($value['type'] == 'field') {
                $fieldHtml .= $this->fieldItemHtml($key, $value);
          	//字段组
            } elseif ($value['type'] == 'group') {
                $groupData = array();
                $groupData['datasetId'] = $id;
                $groupData['enName'] = $key;
                $groupData['data'] = $value;
                $groupData['html'] = $this->groupItemHtml($id, $key);

                $fieldHtml .= $this->renderPartial('_form_item_group', $groupData, true);
            }
        }

        $data = array();
        $data['model'] = $dsModel;
        $data['datasetId'] = $id;
        $data['fieldHtml'] = $fieldHtml;
        $data['preview'] = $preview;
        //$this->renderPartial('_form_item', $data);
        $this->render('_form_item', $data);
    }

    public function actionGroupItemHtml($id, $group, $index = '[key]', $itemData = array())
    {
        return $this->groupItemHtml($id, $group, $index, $itemData, true);
    }

    /**
     * 构造字段组的显示html
     * @param $id			库id
     * @param $group		字段组的英文名
     * @param $index
     * @param $itemData
     * @param $output
     * @return unknown_type
     */
    public function groupItemHtml($id, $group, $index = '[key]', $itemData = array(), $output = false)
    {
        $dsModel = $this->loadModel($id, 'ds');
        $dsModel = $dsModel->sortField();
        $groupInfo = $dsModel->fields[$group];

        $html = '';
        if (!empty($groupInfo['fields'])) {
            foreach ($groupInfo['fields'] as $key => $value) {
                $html .= $this->fieldItemHtml($key, $value, $group, $index, $itemData);
            }
        }
        if ($output) {
            echo $html;
        } else {
            return $html;
        }
    }

    /**
     * 生成字段的展示HTML代码
     * @param $key		string	字段英文名
     * @param $value	array	字段属性
     * @param $group	string	组字段英文名
     * @param $index	string	貌似无用
     * @param $itemData
     * @return unknown_type
     */
    private function fieldItemHtml($key, $value, $group = '', $index = 'key', $itemData = '')
    {
        $fieldData = array();			//初始化字段信息
        $fieldData['enName'] = $key;
        $fieldData['data'] = $value;
        
        //组需要加入的额外属性
        if ($group) {
            $fieldData['group'] = $group;
            $fieldData['key'] = $index;
        }
        if ($itemData) {
            $fieldData['itemData'] = $itemData;
        }
        
		//关联字段
        if ($value['extra']['field_info']['field_type'] == 'reference') {
            $fieldType = $value['extra']['field_info']['field_type'];			//字段类型,此处只可能为reference
            $additionType = $value['extra']['field_info']['addition_type'];		//关联表名
            $dataset = CardDs::model()->findByAttributes(array('en_name' => $additionType));	//获取指定的表模型
            $dsId = $dataset['id'];		//获取表id
            $fieldData['referenceItems'] = $this->loadModel($dsId, 'item', 'dataset_id', true);	//获取 item表中dataset_id=$dsId的数据
            
            $tplName = '_form_item_' . $fieldType;
        } else {
            $fieldType = $value['extra']['field_info']['field_type'];
            $additionType = $value['extra']['field_info']['addition_type'];
            $tplName = '_form_item_' . $fieldType . '_' . $additionType;
        }

        $html = $this->renderPartial($tplName, $fieldData, true);
        return $html;
    }

    /**
     * 修改数据
     * @author gentle
     */
    public function actionUpdate($id)
    {

        $itemModel = $this->loadModel($id, 'item');
        $dsId = (int)$itemModel->dataset_id;
        $dsModel = $this->loadModel($dsId, 'ds');
        if (isset($_POST['CardItem'])) {
            $itemModel->attributes = $_POST['CardItem'];
            if ($itemModel->save()) {
                Yii::app()->user->setFlash("success", "修改数据成功!");
                $this->redirect(array('CardItem/index/id/' . $dsId));
            } else {
                Yii::app()->user->setFlash("error", "修改数据失败!");
            }
        }

        //构造字段Html
        $dsModel = $dsModel->sortField();
        $fieldHtml = '';
        foreach ($dsModel->fields as $key => $value) {
            if ($value['type'] == 'field') {
                $fieldType = $value['extra']['field_info']['field_type'];
                $fieldHtml .= $this->fieldItemHtml($key, $value, '', '', $itemModel->data);
            } elseif ($value['type'] == 'group') {
                $groupData = array();
                $groupData['datasetId'] = $dsId;
                $groupData['enName'] = $key;
                $groupData['data'] = $value;
                if (!empty($itemModel->data[$key])) {
                    $groupData['dataHtml'] = array();
                    foreach ($itemModel->data[$key] as $k => $v) {
                        $groupData['dataHtml'][] = $this->groupItemHtml($dsId, $key, $k, $v);
                    }
                }
                $groupData['html'] = $this->groupItemHtml($dsId, $key);

                $fieldHtml .= $this->renderPartial('_form_item_group', $groupData, true);
            }
        }

        $data = array();
        $data['model'] = $itemModel;
        $data['datasetId'] = $dsId;
        $data['fieldHtml'] = $fieldHtml;
        $data['update'] = true;
        $data['preview'] = false;
        $this->render('_form_item', $data);
    }

    /**
     * 删除内容
     * @param integer $id 数据id
     * @author gentle
     */
    public function actionDelete()
    {

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $model = $this->loadModel($id, 'item');
            if ($model->delete()) {
                Yii::app()->user->setFlash("success", "删除数据成功!");
            } else {
                Yii::app()->user->setFlash("error", "删除数据失败!");
            }
        } else {
            $ids = $_POST['CardItem']['id'];
            foreach ($ids as $value) {
                $model = $this->loadModel($value, 'item');
                if (!$model->delete()) {
                    Yii::app()->user->setFlash("error", "删除数据失败!");
                }
            }
            Yii::app()->user->setFlash("success", "删除数据成功!");
        }
        $this->redirect(array('CardItem/index/id/' . $model->dataset_id));
    }

    public function actionUploadImage($name)
    {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $url = Yii::app()->mcss->uploadImage($_FILES['image']['tmp_name']);
            if ($url) {
                RestHelper::success(array('url' => $url));
            } else {
                RestHelper::error('图片上传到云存储失败');
            }
        } else {
            RestHelper::error('图片上传失败');
        }
    }
    
    /**
     * 填充提交过来的筛选条件
     * @param $criteria	EMongoCriteria	填充前的查询器
     * @param $fields	array	字段定义
     * @return EMongoCriteria	填充后的查询器
     */
    private function fillCond($criteria, $fields){
    	$kfield = isset($_GET['kfield'])?trim($_GET['kfield']):'';			//字段名
    	$koperator = isset($_GET['koperator'])?trim($_GET['koperator']):'';	//操作符
    	$kword = isset($_GET['kword'])?trim($_GET['kword']):'';				//值
    	//检验-字段名和操作符必选
    	if(empty($kfield) || empty($koperator)){
    		return $criteria;
    	}
    	//检查-字段有定义，且不是字段组
    	if($kfield!='id' && (!isset($fields[$kfield]) || $fields[$kfield]['type']=='group')){
    		return $criteria;
    	}
    	
    	//字段名处理
    	if($kfield!='id'){
    		$kfield = 'data.'.$kfield;
    	}
    	
    	//字段定义
    	$field_info = $fields[$kfield]['extra']['field_info'];

    	//操作符处理
    	switch($koperator){
	    	case '==':
		    case '!=':
		    	if($kfield=='id'){
		    		$kword = intval($kword);
		    	}
		    	$criteria->addCond($kfield, $koperator, $kword);
		        break;
		    case '>':
			case '<':
				//数字比较需要先转换类型
				if($field_info['addition_type']=='number' || $kfield=='id'){
					$kword = intval($kword);
				}
				$criteria->addCond($kfield, $koperator, $kword);
		        break;
		  	case 'regex':
		  		$criteria->$kfield = new MongoRegex('/'.$kword.'/i');
		        break;
		   	case 'in':
		   	case 'notin':
		   	case 'all':
		   		echo $kfield;
		   		//'data.fglx'=>array('$nin'=>array('保暖')
		   		$kword = explode(',', $kword);
		   		$criteria->addCond($kfield, $koperator, $kword);
		        break;
		    default:
		        return $criteria;
    	}
    	
    	
    	return $criteria;
    }

}
