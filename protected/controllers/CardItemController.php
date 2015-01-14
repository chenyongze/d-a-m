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
        
        //获取一个可用id
        if(empty($id)){
        	$def_ds = CardDs::model()->find();
        	if($def_ds->id){
        		$id = $def_ds->id;
        	}
        }
        
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
    	$this->actCheck('item-import', false);
        set_time_limit(0); //防止执行超时
        $dsModel = $this->loadModel($id, 'ds');
        $dbModel = $this->loadModel((int)$dsModel->database_id, 'db');
        $mcss = Yii::app()->mcss;

    	//获取字段和中文的对应关系
        $fields_kv = array();
       	foreach($dsModel->fields as $field_key=>$field_info){
         	$fields_kv[$field_key] =  $field_info['name'];
         	//预处理单选和复选的候选项，使其方便判定是否为候选项
         	if(isset($field_info['extra']) && in_array($field_info['extra']['field_info']['addition_type'], array('select', 'multiselect'))){
         		foreach($field_info['extra']['field_info']['select_value'] as $fikey=>$fival){
         			$dsModel->fields[$field_key]['extra']['field_info']['select_value'][$fikey] = $fival['value'];
         		}
         	}
      	}
        
        if (isset($_FILES['CardItem'])) {
        	$this->addLog('ds', $dsModel->id, '批量导入了“'.$dbModel->name.'”中“'.$dsModel->name.'”表的数据');
        	
        	//获取数据
        	//$file_data = $this->inputCsv($_FILES['CardItem']['tmp_name']);
        	$file_data = $this->inputXls($_FILES['CardItem']['tmp_name']);
            $rsHeader = array();
            //往卡牌库Item表导入记录
            //1条1条导入
            foreach($file_data as $i=>$lineData){
            	if(empty($i)){
            		continue;
            	//获取对应字段名
            	}if ($i == 2) {
            		$rsHeader = $lineData;
            	//获取值
                } elseif ($i >= 3) {
                    $itemData = array();
                    if (empty($lineData)) {
                        continue;
                    }
                    
                    //组字段数据临时存放点
                    $group_info = array();	//字段信息
                    $field_count = count($rsHeader);	//字段总数
                    $saveData = array();	//保存数据集
                    
                    //逐一处理每个字段
                    foreach ($lineData as $key => $value) {
                    	//临时：字段位置的编号，若结果集键值不是数字则转ascii后减掉65
                    	$key_no = $key;	
                    	if(!is_numeric($key)){
                    		$key_no = ord($key)-65;
                    	}
                    	
                    	//多余的字段的数据不作处理
                    	if($key_no>=$field_count){
                    		continue;
                    	}
                    	
                    	//$value = trim(iconv('gbk','utf-8',$value));
                    	$value = trim($value);
                    	$field_key = $field_real = $rsHeader[$key];	//字段名称
                        
                    	//如果是字段组，获取组名
                    	if(strpos($field_key, '-')){
                    		$group_info = explode('-', $field_key, 3);
                    		if(isset($group_info[0])){
                    			$field_real = $group_info[0];
                    		}
                        }
                        
                        //只导入元素集有的字段
                        if($field_real=='id'){
                        	$saveData['id'] = intval($value);
                        }else if (isset($dsModel->fields[$field_real])) {
                            $tmpField = $dsModel->fields[$field_real];		//字段定义
                            //普通字段
                            if($tmpField['type']=='field'){
                            	$itemData[$field_real] = $this->formatFirldData($value, $tmpField, $mcss);	//格式化内容
                            //字段组
                            }else if($tmpField['type']=='group'){
                            	
                            	//如果有传入组内字段名才处理
                            	if(isset($group_info[2])){
                            		if(empty($value)){
                            			continue;
                            		}
	                            	$groupField = $tmpField['fields'][$group_info[1]];				//组字段定义
	                            	$value = $this->formatFirldData($value, $groupField, $mcss, false);	//格式化内容
	                            	
	                            	//直接根据索引进行赋值，避免异常的自动组合
	                            	$itemData[$field_real][$group_info[1]][$group_info[2]] = $value;
                            	}
                            }
                            
                        }
                    }
					
                    //若存在id则更新，否则新加一条记录
                    $is_update = false;
                    if(isset($saveData['id']) && $saveData['id']){
                    	$itemModel = $this->loadModel($saveData['id'], 'item');
                    	$is_update = true;
                    }else{
                   	 	$itemModel = new CardItem;
                   	 	$saveData['dataset_id'] = (int)$id;	//设置表类型
                    }
                    
                    //填入数据
                    $saveData['data'] = $itemData;
                    $itemModel->attributes = $saveData;
                    if (!$itemModel->save()) {
                        Yii::app()->user->setFlash("error", "导入数据失败! 停止行数：" . $i);
                        $this->redirect(array('CardItem/index/id/' . $id));
                    }else{
                    	if($is_update){
                    		$this->addLog('item', $itemModel->id, '修改了“'.$dsModel->name.'”中的一条数据(xls)');
                    	}else{
                    		$this->addLog('item', $itemModel->id, '发布了“'.$dsModel->name.'”的新数据(xls)');
                    	}
                    }
                }
            }
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
    	$this->actCheck('item-import', false);
    	$itemModel = new CardItem;
        $dsModel = $this->loadModel($id, 'ds');
        $dbModel = $this->loadModel((int)$dsModel->database_id, 'db');
        
        $this->addLog('ds', $dsModel->id, '导出了“'.$dbModel->name.'”中“'.$dsModel->name.'”表的模板');
    	
        //获取表头
        $dsmap = $dsModel->getFieldNameMap();
        $rs = array(array_values($dsmap), array_keys($dsmap));
        
        //拼接文件名
        $file_name = $dbModel->name.'-'.$dsModel->name.'-'.date('YmdHis').'tpl';
        
      	//输出csv文件
      	$this->outputXls($rs, $file_name);
    }
    
	/**
     * 导出数据
     * @param $id	表id
     * @return unknown_type
     */
    public function actionExport($id){
    	$this->actCheck('item-export', false);
    	$itemModel = new CardItem;
        $dsModel = $this->loadModel($id, 'ds');
        $dbModel = $this->loadModel((int)$dsModel->database_id, 'db');
        
        $this->addLog('ds', $dsModel->id, '导出了“'.$dbModel->name.'”中“'.$dsModel->name.'”中所有的数据');
        
    	//获取表头
        $dsmap = $dsModel->getFieldNameMap(false);
        $rs = array(array_values($dsmap), array_keys($dsmap));
        
        //获取表数据
        $criteria = new EMongoCriteria();
        $criteria->dataset_id = (int)$id;
        
        //添加查询条件
        if(isset($_GET['sub'])){
	        $criteria = $this->fillCond($criteria, $dsModel['fields']);
    	}
        $itemModel = CardItem::model()->findAll($criteria);
        foreach($itemModel as $ik=>$io){
        	$io_info = array();
       		foreach($rs[1] as $dk=>$do){
       			if(strpos($do, '-')!==false){
       				$group_do = explode('-', $do);
       				$io_info[$dk] = $io['data'][$group_do[0]][$group_do[1]][$group_do[2]];
       			}else{
       				if(isset($io[$do])){
       					$io_info[$dk] = $io[$do];
       				}else{
       					$io_info[$dk] = $io['data'][$do];
       				}
       			}
       		} 
       		$rs[] = $io_info;
        }
        
        //拼接文件名
        $file_name = $dbModel->name.'-'.$dsModel->name.'-'.date('YmdHis');
    	
      	//输出csv文件
      	$this->outputXls($rs, $file_name);
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
    	$this->actCheck('item-add', false);
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
            	$this->addLog('item', $itemModel->id, '发布了“'.$dsModel->name.'”的新数据');
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
		$this->actCheck('item-add', false);
        $itemModel = $this->loadModel($id, 'item');
        $dsId = (int)$itemModel->dataset_id;
        $dsModel = $this->loadModel($dsId, 'ds');
        if (isset($_POST['CardItem'])) {
            $itemModel->attributes = $_POST['CardItem'];
            if ($itemModel->save()) {
            	$this->addLog('item', $itemModel->id, '修改了“'.$dsModel->name.'”中的一条数据');
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
		$this->actCheck('item-del', false);
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $model = $this->loadModel($id, 'item');
            $dsModel = $this->loadModel($model->dataset_id, 'ds');
            $old_id = $model->id;
            if ($model->delete()) {
            	$this->addLog('item', $old_id, '清理了“'.$dsModel->name.'”中的一条数据');
                Yii::app()->user->setFlash("success", "删除数据成功!");
            } else {
                Yii::app()->user->setFlash("error", "删除数据失败!");
            }
        } else {
            $ids = $_POST['CardItem']['id'];
            $dsModel = '';
            foreach ($ids as $value) {
                $model = $this->loadModel($value, 'item');
                if(empty($dsModel)){
                	$dsModel = $this->loadModel($model->dataset_id, 'ds');
                }
                $old_id = $model->id;
                if (!$model->delete()) {
                    Yii::app()->user->setFlash("error", "删除数据失败!");
                }else{
                	$this->addLog('item', $old_id, '清理了“'.$dsModel->name.'”中的一条数据');
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
		$type = $field_info['addition_type'];
    	
		//根据提交参数添加条件    	
    	return $this->makeCond($criteria, $type, $kfield, $koperator, $kword);
    }
    
	

}
