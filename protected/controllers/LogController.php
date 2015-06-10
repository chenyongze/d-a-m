<?php
/**
 * 操作日志
 * @author Gavin
 */
class LogController extends Controller {

	public function init(){
		$this->actCheck('log', false);
		$this->layout = '//layouts/column1';
	}
	
	/**
	* 卡牌库列表
	* @author gentle
	*/
	public function actionIndex() {
		$data = array();
		
		$attr = Log::model()->attributeLabels();
		$criteria = new EMongoCriteria;
		
		//其他人无法看见admin的操作记录
		if(Yii::app()->user->name != 'admin'){
			$criteria->addCond('uname', '!=', 'admin');
		}
		
	 	//添加查询条件
        if(isset($_GET['sub'])){
	        $criteria = $this->fillCond($criteria, Log::model()->attributeLabels());
    	}
//     	print_r($criteria);
        $count = Log::model()->count($criteria);
        $pages = new CPagination($count);
        $perPage = 20;
        $pages->pageSize = $perPage;
        $offset = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($offset - 1) * $perPage;
        $criteria->limit($perPage)->offset($offset)->sort('id', EMongoCriteria::SORT_DESC);
        $logModel = Log::model()->findAll($criteria);
        
      	$data['logModels'] = $logModel;
        $data['pages'] = $pages;
        $data['attr'] = $attr;	//模型属性
	 	
		$this->render('index', $data);
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
    	$type = 'text';
    	if(in_array($kfield, array('id', 'uid', 'obj_id'))){
    		$type = 'number';
    	}else if(in_array($kfield, array('acttime'))){
    		$type = 'date';
    	}
    	
		//根据提交参数添加条件    	
    	return $this->makeCond($criteria, $type, $kfield, $koperator, $kword);
    }


}
