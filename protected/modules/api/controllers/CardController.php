<?php

/**
 * 卡牌
 * @author gentle
 */
class CardController extends Controller {

	/**
	 * 执行前预处理
	 * @author gavin
	 * @param $action
	 * @return unknown_type
	 */
	public function beforeAction($action) {
		return true;
	}
	

	/**
	 * 获取数据库列表
	 * @author gentle
	 */
	public function actionGetDbs() {
		$return['code'] = 0;
		$return['data'] = CardDb::model()->getList();
		echo CJSON::encode($return);
	}

	/**
	 * 获取数据表列表
	 * @author gentle
	 */
	public function actionGetTables($databaseId = 0, $enName = '') {
		$return['code'] = 0;
		$selectField = array('id', 'database_id', 'name', 'en_name', 'listorder', 'request_times', 'last_uid', 'update_time');
		$return['data'] = CardDs::model()->getList($databaseId, $enName, $selectField);
		echo CJSON::encode($return);
	}

	/**
	 * 获取字段列表
	 * @author gentle
	 */
	public function actionGetFields($datasetId = 0, $enName = '') {
	    $return['code'] = 0;
	    $return['data'] = CardDs::model()->getFieldList($datasetId, $enName);
	    echo CJSON::encode($return);
	}

	/**
	 * 获取内容列表
	 * @author gavin
	 */
	public function actionGetItems() {
		//参数接收
		//http://db.dev.mofang.com/api/card/getitems/setid/4/filter/djfl|珍品::/regex/djname|水/order/xyd|1/page/-2/size/20
		$datasetId = isset($_GET['setid'])?intval($_GET['setid']):0;				//表id			setid = 4
		$filter = isset($_GET['filter'])?$this->paramStr2Arr($_GET['filter']):'';	//过滤条件		filter = djfl|珍品::	（多个且关系）
		$regex = isset($_GET['regex'])?$this->paramStr2Arr($_GET['regex']):'';		//正则匹配		regex = djname|碎片
		$order = isset($_GET['order'])?$this->paramStr2Arr($_GET['order']):'';		//排序			order = xyd|1
		$currPage = (isset($_GET['page'])&&$_GET['page']>=1)?intval($_GET['page']):1;//当前页码		page = 1 
		$pageSize = isset($_GET['size'])?intval($_GET['size']):0;					//每页数量		size = 20
		
		$return = array('code'=>0, 'data'=>array(), 'pages'=>array());
		
		//参数验证
		if(empty($datasetId)){
			 $return['code'] = 1;	//接口参数不足
		}
		
		//开始查询
		if(empty($return['code'])){
			$dsModel = $this->loadModel($datasetId, 'ds');
			$fields = array_keys($dsModel->fields);
			
			//查询器
			$criteria = new EMongoCriteria();
			
			//加入过滤条件
			if($filter){
				foreach($filter as $fkey=>$fval){
					if(in_array($fkey, $fields)){
						//只查询定义的字段
						$criteria->addCond('data.'.$fkey, '==', $fval);
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
			}
			
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
				unset($arr_info['request_times']);
				unset($arr_info['last_uid']);
				unset($arr_info['update_time']);
				unset($arr_info['_id']);
				$return['data'][$rkey] = $arr_info;
			}
		}

	    echo CJSON::encode($return);
	}
	
	
	/**
	 * 获取选择框的候选项（带缓存）
	 * @author gavin
	 */
	public function actionGetOptionList() {
		//参数接收
		//http://db.dev.mofang.com/api/card/getoptionlist/setid/9/field/select1
		$datasetId = isset($_GET['setid'])?intval($_GET['setid']):0;	//表id
		$fieldKay  = isset($_GET['field'])?$_GET['field']:'';			//字段名
		
		//初始化返回值
		$info = array();
		$return = array('code'=>0, 'data'=>array());
		
		//参数验证
		if(empty($datasetId)||empty($fieldKay)){
			$return['code'] = 1;	//接口参数不足
		//获取字段定义
		}else{
			$options = CardDs::model()->getFieldOption($datasetId, $fieldKay);
			if(empty($options)){
				$return['code'] = 2;
			}else{
				$return['data'] = $options;
			}
		}

		echo CJSON::encode($return);
	}
	
	/**
	 * 获取选择字段的已使用选项（带缓存）
	 * @author gavin
	 */
	public function actionGetOptionUse() {
		//参数接收
		//http://db.dev.mofang.com/api/card/getoptionuse/setid/9/field/select1
		$datasetId = isset($_GET['setid'])?intval($_GET['setid']):0;	//表id
		$fieldKay  = isset($_GET['field'])?$_GET['field']:'';			//字段名
		//初始化返回值
		$info = array();
		$return = array('code'=>0, 'data'=>array());
		
		//参数验证
		if(empty($datasetId)||empty($fieldKay)){
			$return['code'] = 1;	//接口参数不足
		//获取字段定义
		}else{
			$options = CardDs::model()->getFieldOption($datasetId, $fieldKay);
			if(empty($options)){
				$return['code'] = 2;
			}else{
				$option_group = CardItem::model()->getCollection()->group(
					array('data.'.$fieldKay => 1), 
					array('count'=>0), 
					"function (obj, prev) { 
						prev.count++; 
					}",
					array('condition' => array(
						"dataset_id" => $datasetId,					//指定实体
						'data.'.$fieldKay => array('$exists'=>true)	//有该字段
					))
				);
				
				//若有分组数据，则填入返回值中
				if($option_group['retval']){
					foreach($option_group['retval'] as $oval){
						$return['data'][] = $oval['data.'.$fieldKay];
					}
				}

			}
		}
		
		echo CJSON::encode($return);
		
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
	
	
	
	
}
