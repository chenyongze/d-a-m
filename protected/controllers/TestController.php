<?php

class TestController extends Controller {
    
    
    private $gObj =null;
	public function init(){
		$this->actCheck('dbset', false);
		$this->gObj = Yii::app();
	}
	
	/**
	 * 测试 cache
	 */
	public function actionCache(){
	    $key ='1_3';
	    $val = 'yongze';
// 	    $this->gObj->cache->set($key,$val);
	    FunctionUTL::Debug($this->gObj->cache);
	    FunctionUTL::Debug($this->gObj->cache->get($key));
	    
	}
	
	/**
	* 卡牌库列表
	* @author gentle
	*/
	public function actionIndex($id) {
	    $json ='{ "id" : 29, "database_id" : 10, "name" : "ttt", "en_name" : "ttt", "fields" : { "ddd" : { "type" : "field", "name" : "ddd", "must" : 1, "extra" : { "filter" : { "type" : "0" }, "field_info" : { "field_type" : "normal", "addition_type" : "number", "num_type" : "0", "limit_from" : "1111", "limit_to" : "333" } }, "listorder" : 0 }, "daaa" : { "type" : "field", "name" : "ddaa", "must" : 1, "extra" : { "filter" : { "type" : "0" }, "field_info" : { "field_type" : "normal", "addition_type" : "multiselect", "select_value" : [ { "value" : "aaaaaa", "color" : "#ab3737" }, { "value" : "bbb", "color" : "#0a0808" } ] } }, "listorder" : 0 }, "kkk" : { "type" : "field", "name" : "kk", "must" : 1, "extra" : { "filter" : { "type" : "0" }, "field_info" : { "field_type" : "normal", "addition_type" : "text", "length" : "0" } }, "listorder" : 0 } }, "listorder" : 0, "request_times" : 0, "last_uid" : 2, "update_time" : 1434100827 }';
	    FunctionUTL::Debug(json_decode($json,true));exit;
	    
	    
// 	    die('----function:'.__FUNCTION__.'<hr>----class::'.__CLASS__);

        //获取一个可用id
        if(empty($id)){
        	$def_ds = CardDs::model()->findAll(User::model()->getScopeDsCriteria());
        	if($def_ds && $def_ds[0]->id){
        		$id = $def_ds[0]->id;
        	}
        }
        
        //$itemModel = $this->loadModel((int)$id, 'item', 'dataset_id', true);
        $dsModel = $this->loadModel((int)$id, 'ds');					//获取表模型
        $dbModel = $this->loadModel((int)$dsModel->database_id, 'db');	//获取库模型
		$dsModel = $dsModel->sortField();
		
		//范围验证
		$this->scopeCheck($dsModel->database_id, $id);
		 
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

        
        
        print_r($itemModel);exit;
        $data = array();
        $data['itemModel'] = $itemModel;
        $data['dbModel'] = $dbModel;
        $data['dsModel'] = $dsModel;
        $data['datasetId'] = $dsModel->id;
        $data['dataTree'] = $this->dataTree($dbModel->id);
        $data['info'] = $this->promptInfo();
        $data['pages'] = $pages;
        
        
        
        print_r($data);exit;
		
		
// 		$this->render('index', $data);
	}

	/**
	* 创建卡牌库
	* @author gentle
	*/
	public function actionCreate(){
		$model = new CardDb("Create");
		if(isset($_POST['CardDb'])){
			$model->attributes = $_POST['CardDb'];
			if ($model->save()) {
				$this->addLog('db', $model->id, '添加新库“'.$model->name.'”');
				Yii::app()->user->setFlash("success", "新建 <b>{$model->name}</b> 数据库成功!");
			} else {
				$errorMsg = '';
				$errorErr = $model->getErrors();
				foreach ($errorErr as $value) {
					$errorMsg .= "\t".$value[0];
				}
				$errorMsg = trim($errorMsg, ',');
				Yii::app()->user->setFlash("error", $errorMsg);
			}
			//$page = isset($_GET['CardDb_page']) ? $_GET['CardDb_page'] : 1;
			//$this->redirect(array('CardDb/index/CardDb_page/'.$page));
			$this->redirect(array('CardDb/index'));
		}

		//if (Yii::app()->request->isAjaxRequest) {
		//	$this->renderPartial('_form_db', array('model' => $model));
		//}
		$this->renderPartial('_form_db', array('model' => $model));
	}
	
	/**
	* 修改卡牌库
	* @param integer $id 卡牌库id
	* @author gentle
	*/
	public function actionUpdate($id){
		$model = $this->loadModel((int)$id, 'db');
		
		//范围验证
		$this->scopeCheck($model->id);
		
		if(isset($_POST['CardDb'])){
			$model->attributes = $_POST['CardDb'];
			if($model->save()){
				$this->addLog('db', $model->id, '修改了“'.$model->name.'”');
				Yii::app()->user->setFlash("success", "修改 <b>{$model->name}</b> 数据库成功!");
				$this->redirect(array('CardDb/index'));
			}else{
				Yii::app()->user->setFlash("error", "修改 <b>{$model->name}</b> 数据库失败!");
				//print_r($model->getErrors());
			}
		}
		$this->renderPartial('_form_db', array('model' => $model, 'update' => true));
	}

	/**
	* 删除卡牌库
	* @param integer $id 卡牌库id
	* @author gentle
	*/
	public function actionDelete($id) {
		$model = $this->loadModel($id, 'db');
		$dsModel = $this->loadModel($id, 'ds', 'database_id');
		//范围验证
		$this->scopeCheck($id);
		
		if ($dsModel != NULL) {
			Yii::app()->user->setFlash("error", "<b>{$model->name}</b> 下仍存在数据表!");
		} else {
			$old_id = $model->id;
			$old_name = $model->name;
			if ($model->delete()) {
				$this->addLog('db', $old_id, '清理了“'.$old_name.'”');
				Yii::app()->user->setFlash("success", "删除 <b>{$model->name}</b> 数据库成功!");
			} else {
				Yii::app()->user->setFlash("error", "删除 <b>{$model->name}</b> 数据库失败!");
			}
		}
		$data = array();
		$data['info'] = $this->promptInfo();
		echo json_encode($data['info']);
		//$this->redirect(array('CardDb/index'));
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}
