<?php

class CardDbController extends Controller {

	public function init(){
		$this->actCheck('dbset', false);
	}
	
	/**
	* 卡牌库列表
	* @author gentle
	*/
	public function actionIndex() {
		$data = array();
		$data['model'] = new CardDb;
		$data['dataTree'] = $this->dataTree(0, 'datasetField/index');
		$data['info'] = $this->promptInfo();
		$this->render('index', $data);
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
