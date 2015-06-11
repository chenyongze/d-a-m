<?php
/**
 * @info: api提供前台数据【卡牌】
 * @info: 数据来源后台卡牌库mfcart【dataset】
 * @author yongze
 *
 */

class ApiController extends Controller {

	public function init(){
	    $arr = [
	        ["name"=>'cyz'],
	        2,
	        5,
	    ];
	    
	}
	
	public function actionIndex() {
	    
	   $id = FunctionUTL::GetRequestParam('id',FILTER_NUMBER);
	   $info = array();
	   if(empty($id))
	   {
	       echo FunctionUTL::ToJson($info);
	       return '';
	   }
// 	   $itemModel = $this->loadModel((int)$id, 'item', 'dataset_id', true);

	   $dsModel = $this->loadModel((int)$id, 'ds');					//获取表模型
	   $dsModel = $dsModel->sortField();
	   $criteria = new EMongoCriteria();
	   $criteria->dataset_id = $id;
// 	   FunctionUTL::Debug($dsModel['fields']);
	   //添加查询条件
	   if(isset($_GET['sub'])){
	       $criteria = $this->fillCond($criteria, $dsModel['fields']);
	   }
	   
// 	   FunctionUTL::Debug($criteria->getConditions());
	   $count = CardItem::model()->count($criteria);
	   $pages = new CPagination($count);
	   $perPage = 10;
	   $pages->pageSize = $perPage;
	   //$pages->applyLimit($criteria);
	   $offset = isset($_GET['page']) ? intval($_GET['page']) : 1;
	   $offset = ($offset - 1) * $perPage;
	   $criteria->limit($perPage)->offset($offset)->sort('id', EMongoCriteria::SORT_DESC);
	   $itemModel = CardItem::model()->findAll($criteria);
// 	   FunctionUTL::Debug($itemModel);

	   $info = array();
	   if(!empty($itemModel))
	   {
	       foreach ($itemModel as $itemObj)
	       {
	           if(empty($itemObj->data))
	           {
	               continue;
	           }
	           
	          $info[] =$itemObj->data; 
	       }
	   }
	   
// 	   FunctionUTL::Debug($info);
	   echo FunctionUTL::ToJson($info);
	}
	
	/**
	 * @info:内容【详细】
	 */
	public function actionDetail(){}
	
	/**
	 * @info:【搜索】
	 */
	public function actionSearch(){}
	

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
