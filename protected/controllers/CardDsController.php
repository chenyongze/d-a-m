<?php

class CardDsController extends Controller {
	
	public function init(){
		$this->actCheck('dbset', false);
	}
	
	/**
	* 元素集列表
	* @author gentle
	*/
	public function actionIndex($id) {
	    $model = new CardDs;
	    $model->database_id = $id;

		$dbModel = $this->loadModel($id, 'db');
		
		//范围验证
		$this->scopeCheck($id);

		$data = array();
		$data['model'] = $model;
		$data['dbModel'] = $dbModel;
		$data['databaseId'] = $id;
		$data['dataTree'] = $this->dataTree($id, 'datasetField/index');
		$data['info'] = $this->promptInfo();
		$this->render('index', $data);
	}

	/**
	* 创建元素集
	* @author gentle
	*/
	public function actionCreate($id) {
		$model = new CardDs("Create");
		$model->database_id = $id;
		$db_model = $this->loadModel($id, 'db');
		if(isset($_POST['CardDs'])) {
			$CardDsArray = array();
			$CardDsArray[0]['database_id'] = (int)$_POST['CardDs']['database_id'];
			$CardDsArray[0]['name'] = $_POST['CardDs']['name'];
			$CardDsArray[0]['en_name'] = $_POST['CardDs']['en_name'];

			if (isset($_POST['CardDs']['additionField'])) {
				foreach ($_POST['CardDs']['additionField'] as $key=>$value) {
					if (empty($value['name']) || empty($value['en_name'])) {
						continue;
					}
					$CardDsArray[$key] = $value;
					$CardDsArray[$key]['database_id'] = (int)$_POST['CardDs']['database_id'];
				}
			}
			foreach ($CardDsArray as $val) {
				$model = new CardDs("Create");
				$model->database_id = $id;
				$model->attributes = $val;
				if(!$model->save()){
					$errorMsg = '';
					$errorErr = $model->getErrors();
					foreach ($errorErr as $value) {
						$errorMsg .= "\t".$value[0];
					}
					$errorMsg = trim($errorMsg, ',');
					Yii::app()->user->setFlash("error", $errorMsg);
					$this->redirect(array('CardDs/index/id/'.$id));
					Yii::app()->end();
				}else{
					$this->addLog('ds', $model->id, '在“'.$db_model->name.'”中新加了表“'.$model->name.'”');
				}
			}
			Yii::app()->user->setFlash("success", "新建 <b>{$model->name}</b> 数据表成功!");
			$this->redirect(array('CardDs/index/id/'.$id));
		}
		$dbModel = $this->loadModel($id, 'db');
		$this->renderPartial('_form_ds', array('model' => $model, 'dbModel' => $dbModel));
	}

	/**
	* 新增一行
	* @author gentle
	*/
	public function actionAddRow($id) {
		$rowHtml = '
			<div class="row">
				<div class="span1" style="width:80px;">
					<label>数据库表名:</label>
				</div>
				<div class="span2">
					<input name="CardDs[additionField]['.$id.'][name]" type="text" />
				</div>

				<div class="span1" style="width:70px;margin-left:70px;">
					<label>英文标识:</label>
				</div>
				<div class="span2">
					<input name="CardDs[additionField]['.$id.'][en_name]" type="text" />
				</div>
				<div class="span1" style="margin-left:60px;width:30px;">
					<span style="font-size:30px;">&nbsp;</span>
				</div>
			</div>';
	    	echo CJSON::encode($rowHtml);
		exit();
	}

	/**
	* 修改元素集
	* @author gentle
	*/
	public function actionUpdate($id) {
		$model = $this->loadModel($id, 'ds');
		$db_model = $this->loadModel($model->database_id, 'db');
		if(isset($_POST['CardDs'])){
			$model->attributes = $_POST['CardDs'];
			if($model->save()){
				$this->addLog('ds', $model->id, '修改了“'.$db_model->name.'”中的“'.$model->name.'”');
				Yii::app()->user->setFlash("success", "修改 <b>{$model->name}</b> 数据表成功!");
				$this->redirect(array('CardDs/index?id='.$model->database_id));
			}else{
				$errorMsg = '';
				$errorErr = $model->getErrors();
				foreach ($errorErr as $value) {
					$errorMsg .= "\t".$value[0];
				}
				$errorMsg = trim($errorMsg, ',');
				Yii::app()->user->setFlash("error", $errorMsg);
			}
		}

		$data = array();
		$data['model'] = $model;
		$data['database_id'] = $id;
		$data['update'] = true;
		$data['dbModel'] = $this->loadModel($model->database_id, 'db');
		$this->renderPartial('_form_ds', $data);
	}

	/**
	* 删除元素集
	* @param integer $id 卡牌库id
	* @author gentle
	*/
	public function actionDelete($id) {
		$model = $this->loadModel($id, 'ds');
		$db_model = $this->loadModel($model->database_id, 'db');
		$itemModel = $this->loadModel($id, 'item', 'dataset_id');
		if ($itemModel != NULL) {
			Yii::app()->user->setFlash("error", "<b>{$model->name}</b> 下仍存在数据!");
		} else {
			$old_id = $model->id;
			$old_name = $model->name;
			if ($model->delete()) {
				$this->addLog('ds', $old_id, '清理了“'.$db_model->name.'”中的“'.$old_name.'”');
				Yii::app()->user->setFlash("success", "删除 <b>{$model->name}</b> 数据表成功!");
			} else {
				Yii::app()->user->setFlash("error", "删除 <b>{$model->name}</b> 数据表失败!");
			}
		}
		$data = array();
		$data['info'] = $this->promptInfo();
		echo json_encode($data['info']);
		//$this->redirect(array('CardDs/index/id/'.$model->database_id));
	}
	/**
	* 清空数据
	* @param integer $id 元素集id
	* @author gentle
	*/
	public function actionClearData($id) {
		$model = $this->loadModel($id, 'ds');
		$itemModel = $this->loadModel($id, 'item', 'dataset_id');
		$data = array();
		if ($itemModel == NULL) {
			$data['type'] = 'error';
			$data['msg'] = '该表无数据!';
		} else {
			$itemModelArray = $this->loadModel($id, 'item', 'dataset_id', true);
			foreach ($itemModelArray as $key=>$value) {
				$value->delete();
			}
			$data['type'] = 'success';
			$data['msg'] = '清空数据!';
			$this->addLog('ds', $model->id, '清空了“'.$model->name.'”中所有的数据');
		}
		echo json_encode($data);
	}
}
