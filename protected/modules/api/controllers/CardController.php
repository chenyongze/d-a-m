<?php

/**
 * @info: api提供前台数据【卡牌】
 * @info: 数据来源后台卡牌库mfcart【dataset】
 * @author yongze
 * <pre>
 * 错误码
 * -91xx  -9100  区间
 * </pre>
 */

class CardController extends Controller {

    private $_obj = null;//yii app
    private $_setid =null;//表id
    
    public function init(){
        $this->_obj = Yii::app();
        $this->_setid =1;
    }
    
    /**
     * 执行前预处理
     */
	public function beforeAction($action) {
		return true;
	}
	

	/**
	 * 获取数据库列表
	 */
	public function actionGetDbs() {
		$return['code'] = 0;
		$return['data'] = CardDb::model()->getList();
		echo CJSON::encode($return);
	}

	/**
	 * 获取数据表列表
	 */
	public function actionGetTables($databaseId = 0, $enname = '') {
		$return['code'] = 0;
		$selectField = array('id', 'database_id', 'name', 'en_name', 'fields','listorder');
		$_data = CardDs::model()->getList($databaseId, $enname, $selectField);
		//数据处理
	    foreach ($_data[0]['fields'] as $fields_k =>$fields_v){
	        //过滤非筛选字段
	        if($fields_v['extra']['filter']['type'] !=1){
	            continue;
	        }
	        $return['data'][$fields_k]['name'] = $fields_v['name'];
	        $return['data'][$fields_k]['field_info'] = $fields_v['extra']['field_info'];
	        $return['data'][$fields_k]['listorder'] = $fields_v['listorder'];
	    }
		echo CJSON::encode($return);
	}
	
	/**
	 * 获取列表页展示的字段
	 */
	public function actionGetSelectFields($databaseId = 0, $enname = ''){
	    $return['code'] = 0;
	    $selectField = array('id', 'database_id', 'name', 'en_name', 'fields','listorder');
	    $_data = CardDs::model()->getList($databaseId, $enname, $selectField);
	    //数据处理
	    foreach ($_data[0]['fields'] as $fields_k =>$fields_v){
	        //过滤非筛选字段
	        if($fields_v['must'] !=1){
	            continue;
	        }
	        $return['data'][$fields_k] = $fields_v['name'];
	    }
	    echo CJSON::encode($return);
	}
	
	
	/**
	 * 获取数据表列表 html
	 * @param int $databaseId
	 * @param string $enname
	 */
	public function actionGetTablesHtml($databaseId = 0, $enname = '')
	{
// 	    $return['code'] = 0;
	}
	

	/**
	 * 获取字段列表
	 */
	public function actionGetFields($datasetId = 0, $enName = '') {
	    $return['code'] = 0;
	    $return['data'] = CardDs::model()->getFieldList($datasetId, $enName);
	    echo CJSON::encode($return);
	}
	
	/**
	 * @info:获取游戏详细页面模板结构&&数据
	 * http://db.admin.mofang.com/api/card/getitemhtml?id=44
	 */
	public function actionGetItemHtml(){
	    $return = array('code'=>0,'data'=>array());
	    $id = isset($_GET['id'])?intval($_GET['id']):0;//数据itemid
	    $type = isset($_GET['type'])?intval($_GET['type']):1;
	    if(empty($id)){
	        $return['code'] = -9115;
	        echo CJSON::encode($return);
	    }
	    
	    $criteria = new EMongoCriteria();
	    $criteria->addCond('id', '==', $id);		//按id查询
	    $itemInfo = CardItem::model()->find($criteria);
	    unset($criteria);
	    
	    //获取卡牌对应的模板
	    if($itemInfo->dataset_id){
	        $criteria = new EMongoCriteria();
	        $criteria->addCond('type', '==', $type);		//type模板类型
	        $criteria->addCond('dataset_id', '==', $itemInfo->dataset_id);		//type模板类型
	        $templateInfo = Template::model()->find($criteria);
	        
	        $this->parseTemplate($templateInfo->content,$itemInfo->data);
	        echo html_entity_decode($templateInfo->content);
	    }
	    
	    return ;
	}
	
    /**
     * @info:解析模板标签 //@todo 解析
     * @param str $parseTemplate
     * @param array $items
     * [k] => Array
        (
            [0] => {F:name}
            [1] => {F:character}
            [2] => {F:profession}
            [3] => {F:life}
            [4] => {F:m_power}
            [5] => {F:w_power}
            [6] => {F:w_defense}
            [7] => {F:f_power}
            [8] => {F:f_defense}
            [9] => {F:strike}
            [10] => {F:treat}
            [11] => {F:parry}
            [12] => {F:hurt}
            [13] => {F:technical_title_0}
            [14] => {F:technical_description_0}
            [15] => {F:technical_title_1}
            [16] => {F:technical_description_1}
            [17] => {F:solder_title_1}
            [18] => {F:solder_description_1}
            [19] => {F:solder_title_2}
            [20] => {F:solder_description_2}
            [21] => {F:k_pic}
        )

    [v] => Array
        (
            [0] => 守護神謝爾
            [1] => 史詩
            [2] => 守護騎士
            [3] => 3712
            [4] => 371
            [5] => 294
            [6] => 294
            [7] => 0
            [8] => 312
            [9] => 40
            [10] => 0
            [11] => 200
            [12] => 40
            [13] => 普通技-死亡之握
            [14] => 戰士對自身一定半徑範圍內的所有目標進行嘲諷，聚集所有目標並使敵人暈眩，同時造成大量傷害。
            [15] => 普通技-雷霆一擊
            [16] => 戰士對自身周圍的所有單位發動範圍性重擊，降低敵人物理攻擊，同時造成大量傷害並擊飛所有目標。
            [17] => 無情守護
            [18] => 與斷罪神薩拉一同參戰，物理攻擊提高10%
            [19] => 庇護
            [20] => 與影舞領主羅琳一同參戰，物理防禦提高5%
            [21] => <img src="http://pic1.mofang.com/185/162/0a0dd636e1a9065964a5a90b98f7619600568d39.jpg">
        )

)
     */
	private function parseTemplate(&$parseTemplate,$items){
	    if(empty($items)||empty($parseTemplate)){
	        return -9122;
	    }
	    foreach ($items as $k=>$v){
	        
	        if(is_array($v)){
	            foreach ($v as $tk=>$_tv){
	                $_line =$tk+1;
	                foreach ($_tv as $fieldk=>$fieldv){
	                    $template['k'][] = "{F:{$k}_{$fieldk}_$_line}";
	                    $template['v'][] = $fieldv;
	                }
	            }
	        }else{
	            $template['k'][] = "{F:$k}";
	            $ext = @substr(strrchr($v, '.'), 1);
	            if(in_array($ext, array('jpg','png'))) $v = "<img src='".$v."'>";
	            $template['v'][] = $v;
	        }
	        
	    }
	    
// 	    FunctionUTL::Debug($template);//exit;
	    $parseTemplate = str_replace($template['k'], $template['v'], $parseTemplate);
	    return;
	}
	/**
	 * 获取一条内容
	 * http://db.admin.mofang.com/api/card/getitem?id=7836
	 * http://db.admin.mofang.com/api/card/getitem?setid=1&name=冬梦
	 */
	public function actionGetItem() {
		//参数接收
		//http://db.dev.mofang.com/api/card/getitem?itemid=7836&select=name,ms
		//http://db.dev.mofang.com/api/card/getitem?setid=1&name=冬梦&select=name,ms
		$id = isset($_GET['id'])?intval($_GET['id']):0;				//数据itemid	
		$datasetId = isset($_GET['setid'])?intval($_GET['setid']):0;//实体id
		$name = isset($_GET['name'])?$_GET['name']:'';				//数据name		setid = 4
		
		$select = isset($_GET['select'])?$_GET['select']:'';		//返回字段		select = data.name
		$return = array('code'=>0, 'data'=>array());
		
		//参数验证
		if(empty($id)&&empty($name)){
			 $return['code'] = -9101;	//接口参数不足
		}
		
		$cache_data = null;
		$this->checkCache($cache_data);	//检查缓存
		if(!empty($cache_data))
		{
		    echo $cache_data;
		    return ;
		}
		
		//开始查询
		if(empty($return['code'])){
			//查询器
			$criteria = new EMongoCriteria();
			if($id){
				$criteria->addCond('id', '==', $id);		//按id查询
			}else{
				$dsModel = $this->loadModel($datasetId, 'ds');
				$fields = $dsModel->getFieldNameMap();
				$fname = 'name';
				//查找第一个名字中有'name'的字段
				foreach($fields as $fk=>$fv){
					if(preg_match('/name/i', $fk)){
						$fname = $fk;
						break;
					}
				}
				$criteria->addCond('dataset_id', '==', $datasetId);	//名称查询需要限定实体，防止实体间有重名
				$criteria->addCond('data.'.$fname, '==', $name);	//按name查询
			}
			
			//加入字段
			if($select){
				$select = explode(',', $select);
				foreach($select as $skey=>$sfield){
					$select[$skey] = 'data.'.$sfield;
				}
				array_unshift($select, 'id');	//压入默认字段
				$criteria->select($select);
			}
			//查询
			$return['data'] = CardItem::model()->find($criteria);
			if($return['data']){
				$arr_info = $return['data']->toArray();
				//清除无用的字段
				unset($arr_info['dataset_id']);
				unset($arr_info['request_times']);
				unset($arr_info['last_uid']);
				unset($arr_info['update_time']);
				unset($arr_info['_id']);
				$return['data'] = $arr_info;
			}
			$this->writeCache(CJSON::encode($return));	//设置缓存
		}
		
	    echo CJSON::encode($return);
	}
	
	/**
	 * 获取内容列表
	 * http://db.admin.mofang.com/api/card/getitems?setid=1&select=name
	 */
	public function actionGetItems() {
		//参数接收
		//http://db.dev.mofang.com/api/card/getitems/setid/4/filter/djfl|珍品::/regex/djname|水/order/xyd|1/page/-2/size/20
		$datasetId = isset($_GET['setid'])?intval($_GET['setid']):0;				//表id			setid = 4
		$select = isset($_GET['select'])?$_GET['select']:'';						//返回字段		select = data.name
		$filter = isset($_GET['filter'])?$this->paramStr2Arr($_GET['filter']):'';	//过滤条件		filter = djfl|珍品::	m_power|[20,1000]（多个且关系）[m_power]魔力在20-1000范围内
		$regex = isset($_GET['regex'])?$this->paramStr2Arr($_GET['regex']):'';		//正则匹配		regex = djname|碎片（目前是不限定头尾匹配，后面需要优化）
		$order = isset($_GET['order'])?$this->paramStr2Arr($_GET['order']):'';		//排序			order = xyd|1
		$currPage = (isset($_GET['page'])&&$_GET['page']>=1)?intval($_GET['page']):1;//当前页码		page = 1 
		$pageSize = isset($_GET['size'])?intval($_GET['size']):0;					//每页数量		size = 20
		
		$return = array('code'=>0, 'data'=>array(), 'pages'=>array());
		
		//参数验证
		if(empty($datasetId)){
			 $return['code'] = -9101;	//接口参数不足
		}
		
		$cache_data = null;
		$this->checkCache($cache_data);	//检查缓存
	    if(!empty($cache_data))
		{
		    echo $cache_data;
		    return ;
		}
		
		//开始查询
		if(empty($return['code'])){
			$dsModel = $this->loadModel($datasetId, 'ds');
			$fields = array_keys($dsModel->fields);
			//查询器
			$criteria = new EMongoCriteria();
			$criteria->addCond('dataset_id', '==', $datasetId);	//指明对象
			
			if($select){
				$select = explode(',', $select);
				foreach($select as $skey=>$sfield){
					$select[$skey] = 'data.'.$sfield;
				}
				array_unshift($select, 'id');	//压入默认字段
				$criteria->select($select);
			}
			
			//加入过滤条件
			if($filter){
				foreach($filter as $fkey=>$fval){
					if(in_array($fkey, $fields)){
						//只查询定义的字段
						$criteria->addCond('data.'.$fkey, '==', $fval);
					}else if(in_array($fkey, array('id'))){
						$criteria->addCond($fkey, '==', intval($fval));
					}
				}
			}

			//加入正则
			if($regex){
				foreach($regex as $rkey=>$rval){
					if(in_array($rkey, $fields)){
						//只查询定义的字段
						$rkey = 'data.'.$rkey;
						$criteria->$rkey = new MongoRegex('/'.$rval.'/i');
					}
				}
			}
		
			//排序
			if($order){
				foreach($order as $okey=>$oval){
					if(in_array($okey, $fields)){
						//只查询定义的字段
						$criteria->sort('data.'.$okey, $oval);
					}
				}
			}else{
				$criteria->sort('id', EMongoCriteria::SORT_ASC);	//默认id正序
			}
			
// 			FunctionUTL::Debug($criteria);exit;
			
			//构建分页
			$count = CardItem::model()->count($criteria);
			$pages = new CPagination($count);
			$pages->pageSize = $pageSize;
			$offset = ($currPage - 1) * $pageSize;
			$criteria->limit($pageSize)->offset($offset);
			
			//查询本页
			$return['data'] = CardItem::model()->findAll($criteria);
			$return['pages'] = array('itemCount'=>$count, 'pageSize'=>$pageSize, 'currPage'=>$currPage);
			foreach($return['data'] as $rkey=>$rval){
				$arr_info = $return['data'][$rkey]->toArray();
				//清除无用的字段
				unset($arr_info['dataset_id']);
				unset($arr_info['request_times']);
				unset($arr_info['last_uid']);
				unset($arr_info['update_time']);
				unset($arr_info['_id']);
				$return['data'][$rkey] = $arr_info;
			}
			$this->writeCache(CJSON::encode($return));	//设置缓存
		}
	    echo CJSON::encode($return);
	}
	
	
	/**
	 * 获取选择框的候选项（带缓存）
	 * http://db.admin.mofang.com/api/card/getoptionlist?setid=1&field=xj
	 */
	public function actionGetOptionList() {
		//参数接收
		//http://db.dev.mofang.com/api/card/getoptionlist/setid/9/enname/select1
		$datasetId = isset($_GET['setid'])?intval($_GET['setid']):0;	//表id
		$fieldKay  = isset($_GET['enname'])?$_GET['enname']:'';			//字段名
		
		//初始化返回值
		$info = array();
		$return = array('code'=>0, 'data'=>array());
		
		//参数验证
		if(empty($datasetId)||empty($fieldKay)){
			$return['code'] = 1;	//接口参数不足
		//获取字段定义
		}else{
			//$this->checkCache();	//检查缓存
			$options = CardDs::model()->getFieldOption($datasetId, $fieldKay);
			if(empty($options)){
				$return['code'] = 2;
			}else{
				foreach($options as $oval){
					$return['data'][] = $oval['value'];
				}
			}
			$this->writeCache(CJSON::encode($return));	//设置缓存
		}

		echo CJSON::encode($return);
		return '';
	}
	
	/**
	 * 获取选择字段的已使用选项（带缓存）
	 * http://db.admin.mofang.com/api/card/getoptionuse?setid=1&field=fglx
	 */
	public function actionGetOptionUse() {
		//参数接收
		//http://db.dev.mofang.com/api/card/getoptionuse/setid/9/field/select1
		$datasetId = isset($_GET['setid'])?intval($_GET['setid']):0;	//表id
		$fieldKay  = isset($_GET['field'])?$_GET['field']:'';			//字段名
		$filter = isset($_GET['filter'])?$this->paramStr2Arr($_GET['filter']):'';	//过滤条件		filter = djfl|珍品::	（多个且关系）
		
		//初始化返回值
		$info = array();
		$return = array('code'=>0, 'data'=>array());
		
		//参数验证
		if(empty($datasetId)||empty($fieldKay)){
			$return['code'] = 1;	//接口参数不足
		//获取字段定义
		}else{
			$this->checkCache($cache_data);	//检查缓存
			if(!empty($cache_data))
			{
			    echo $cache_data;
			    return '';
			}
			
			$options = CardDs::model()->getFieldOption($datasetId, $fieldKay);
			if(empty($options)){
				$return['code'] = 2;
			}else{
				//过滤条件
				$condition = array(
					"dataset_id" => $datasetId,					//指定实体
					'data.'.$fieldKay => array('$exists'=>true)	//有该字段
				);
				
				//加入过滤条件
				if($filter){
					foreach($filter as $fkey=>$fval){
						$condition['data.'.$fkey] = $fval;
					}
				}
				
				//执行查询
				$option_dist = CardItem::model()->getCollection()->distinct(
					'data.'.$fieldKay,
					$condition
				);
				
				//若有分组数据，则填入返回值中
				if($option_dist){
					//合并选项并去重
					foreach($option_dist as $oval){
						if(is_array($oval)){
							$return['data'] = array_merge($return['data'], $oval);	//数组合并
						}else{
							array_push($return['data'], $oval);						//字符串压入
						}
					}
					$return['data'] = array_unique($return['data']);	//去重
					$return['data'] = array_filter($return['data']);	//去空
					$return['data'] = array_merge($return['data'], array());
				}
				
				$this->writeCache(CJSON::encode($return));	//设置缓存
			}
		}
		
		echo CJSON::encode($return);
		
	}
	
	/**
	 * 获取表字段类型包含字段name
	 * 
     *[character] => Array
     *   (
     *      [name] => 品质
     *      [addition_type] => select
     *   )
	 */
	protected function getTableFields(&$_fields=array())
	{
	    $dsModel = $this->loadModel($this->_setid, 'ds');//获取表模型
	    if($dsModel->fields)
	    {
	        foreach ($dsModel->fields as $field=>$info)
	        {
	            $_fields[$field]['name']=$info['name'];
	            $_fields[$field]['addition_type']=$info['extra']['field_info']['addition_type'];
	            
	        }
	        
	    }
	    
	    return 0;
	}
	
	/**
	 * 解析传入参数
	 * @param $str string	字符串条件	
	 * @return array 		解析好的条件
	 */
	 protected function paramStr2Arr($str){
		$arr = array();
		$rows = explode('::', $str);
		foreach($rows as $row){
			$info = explode('|', $row);
			if(!empty($info[0])&&!empty($info[1])){
				$arr[$info[0]] = $info[1];
			}
		}
		
		return $arr;
	}
	
	/**
	 * 格式化传入参数
	 * @param $arr array	条件数组
	 * @return string		字符串条件
	 */
	protected function paramArr2Str($arr){
		$str = '';
		foreach($arr as $key=>$val){
			if(!empty($key) && !empty($val)){
				$arr[$key] = $key.'|'.$val;
			}else{
				unset($arr[$key]);
			}
		}
		$str = implode('::', $arr);
		
		return $str;
	}
	
	/**
	 * 检查缓存，若已经存在则直接打印并终止
	 * @return null
	 */
	protected function checkCache(&$cache_data){
		//缓存时间 cache_expire === false 不缓存数据  ,  cache_expire == 0 缓存时间无限大
	    if($this->_obj->params['cache_expire'] === false)
	    {
	        return;
	    }
		//现尝试从缓存中获取
        $cache_key = 'db.admin.'.$_SERVER['REQUEST_URI'];	//db.admin./api/card/getitems?setid=1&select=name
        if(($cache_data = $this->_obj->cache->get($cache_key)) == false){
        	return -9100;
        }
        return ;
	}
	
	/**
	 * 添加或更新缓存
	 * @param $data	array	待缓存的数据
	 * @return unknown_type
	 */
	protected function writeCache($data){
	    //缓存时间 cache_expire === false 不缓存数据  ,  cache_expire == 0 缓存时间无限大
	    if($this->_obj->params['cache_expire'] === false)
	    {
	        return;
	    }
	    
		//添加缓存
		$cache_key = 'db.admin.'.$_SERVER['REQUEST_URI'];
		$ret =$this->_obj->cache->set($cache_key, $data, $this->_obj->params['cache_expire']);
      	if(!$ret){
    		Yii::log('设置缓存失败：key='.$cache_key, CLogger::LEVEL_WARNING, 'system.cache');
    		return -91001;
    		
    	};
    	
    	return 0;
	}
	
	
}
