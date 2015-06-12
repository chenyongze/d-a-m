<?php
/**
 * 字段管理
 * @author yongze
 *
 */

class DatasetFieldController extends Controller {

	/**
	* Field列表
	* @author gentle
	*/
	public function actionIndex($id) {
		$dsModel = $this->loadModel((int)$id, 'ds');
		$dbModel = $this->loadModel((int)$dsModel->database_id, 'db');
		//范围验证
// 		$this->scopeCheck( $dbModel->id, $dsModel->id);
		
		$info = Yii::app()->user->getFlash("info");
		if (isset($_POST['listorder'])) {
			$dsModel = $dsModel->fieldChangeSort($_POST['listorder']);
			if ($dsModel->save()) {
				Yii::app()->user->setFlash("success", "排序成功!");
			} else {
				Yii::app()->user->setFlash("error", "排序失败!");
			}
			$this->redirect(array('DatasetField/index/id/'.$id));
		}

		$dsModel = $dsModel->sortField();

		$data = array();
		$data['model'] = $dsModel;
		$data['dbModel'] = $dbModel;
		$data['dsModel'] = $dsModel;
		$data['datasetId'] = $id;
		$data['dataTree'] = $this->dataTree($dsModel->database_id, 'datasetField/index');
		$data['info'] = $this->promptInfo();
		$this->render('index', $data);
	}

	/**
	* 第二个下拉菜单options动态输出
	* @author gentle
	*/
	public function actionAdditionType($fieldType, $additionType = -1, $databaseId = -1, $datasetId = -1) {
		switch ($fieldType) {
		    //独立字段
			case 'normal':
				$data = array('text'=>'文本', 'multitext'=>'多行文本', 'number'=>'数值', 'select'=>'单选', 'multiselect'=>'多选', 'image'=>'图片');
				break;
		    //调用元素集字段
		    case 'reference':
				$dsModel = $this->loadModel($databaseId, 'ds', 'database_id', true);
				$data = array();
				foreach ($dsModel as $key=>$value) {
					$data[$value->en_name] = $value->name;
				}
				break;
		}

		$dropDown = "<option value=''>选择附属类型</option>";
		foreach($data as $value=>$name) {
		    if ($additionType>=0 && $value==$additionType) {
				$dropDown .= CHtml::tag('option', array('value'=>$value, 'selected'=>'selected'),CHtml::encode($name),true);
		    } else {
				$dropDown .= CHtml::tag('option', array('value'=>$value),CHtml::encode($name),true);
		    }
		}

		echo CJSON::encode(array(
		    'dropDown'=>$dropDown,
		));
	}

	/**
	* Fields动态输出
	* @author gentle
	*/
	public function actionAdditionField($fieldType, $additionType, $cardDsId = -1, $enName = '', $group = '') {
		//数值thirdField恢复
		if ($cardDsId != -1 && $enName != '') {
		    $dsModel = $this->loadModel((int)$cardDsId, 'ds');
			if ($group) {
				$data = $dsModel->fields[$group]['fields'][$enName];
			} else {
				$data = $dsModel->fields[$enName];
			}
		} else {
			$data = array();
		}

		//普通字段
		if ($fieldType == 'normal') {
			$additionFieldHtml = $this->renderPartial('_form_field_'.$fieldType.'_'.$additionType, array('data' => $data), true);
		}

		//调用元素集字段
		if ($fieldType == 'reference') {
			$dsModel = CardDs::model()->findByAttributes(array('en_name'=>$additionType));
		    $fields = $dsModel->getFields();
		    $fieldArray = array();
		    foreach ($fields as $key=>$value) {
			$fieldArray[$key] = $value['name'];
		    }
			$additionFieldHtml = $this->renderPartial('_form_field_reference', array('fieldArray' => $fieldArray, 'data' => $data), true);
		}

		echo CJSON::encode(array(
		    'fieldHtml'=>$additionFieldHtml,
		));
	}

	/**
	* 新建字段
	* @author gentle
	*/
	public function actionCreate($id, $type = 0, $group = '') {
		//$model = new CardDs;
	    	//$model->id = $id;
		$dsModel = $this->loadModel((int)$id, 'ds');
		if (isset($_POST['fields'])) {
			$create = $dsModel->changeField($_POST['fields'], 'create');
			if ($create['code']==0) {
				if ($create['data']->save()) {
					$this->addLog('ds', $dsModel->id, '在表“'.$dsModel->name.'”中新加了字段“'.$_POST['fields']['name'].'”');
					Yii::app()->user->setFlash("success", "创建 <b>{$_POST['fields']['name']}</b> 字段成功!");
				} else {
					Yii::app()->user->setFlash("error", "保存失败!");
				}
			} else {
				Yii::app()->user->setFlash("error", $create['msg']);
			}
			$this->redirect(array('DatasetField/index/id/'.$id));
		}
		$data = array();
		$data['model'] = $dsModel;
		if ($group) {
			$data['group'] = $group;
		}
		$this->renderPartial('_form_field', $data);
	}


	/**
	* 创建组字段
	* @author gentle
	*/
	public function actionCreateGroup($id) {
		$dsModel = $model = $this->loadModel((int)$id, 'ds');
		$model->id = $id;
		$data = array();
		$data['errorMsg'] = NULL;
		if(isset($_POST['fields'])){

			$en_name = $_POST['fields']['en_name'];
			unset($_POST['fields']['en_name']);

			$fields = $_POST['fields'];

			//这部分工作可放在自定义rules里
			if (isset($model->fields[$en_name])) {//指定英文标识重复
				$data['errorMsg'] = "英文标识 {$en_name} 已存在";
			} else {
				$model->fields[$en_name] = $fields;
				if($model->save()) {
					$this->addLog('ds', $dsModel->id, '在表“'.$dsModel->name.'”中新加了字段组“'.$_POST['fields']['name'].'”');
					//Ajax结束app
					$this->redirect(array('DatasetField/index/id/'.$model->id));
				} else {
					print_r($model->getErrors());
				}
			}
		}

		$dbModel = $this->loadModel((int)$dsModel->database_id, 'db');
		$this->renderPartial('create_group', array('model' => $model, 'dsModel' => $dsModel, 'dbModel' => $dbModel, 'datasetId'=> $id, 'dataTree' => $this->dataTree($id), 'errorMsg'=>$data['errorMsg']));
	}
	

	/**
	* 修改字段
	* @author gentle
	*/
	public function actionUpdate($id, $enName, $type, $group = '') {

		$dsModel = $model = $this->loadModel((int)$id, 'ds');
		$errorMsg = '';
		if (isset($_POST['fields'])) {
			if ($type == 'field') {
				$create = $dsModel->changeField($_POST['fields'], 'update');
				if ($create['code']==0) {
					if ($create['data']->save()) {
						$this->addLog('ds', $dsModel->id, '修改了“'.$dsModel->name.'”中的字段“'.$_POST['fields']['name'].'”');
						Yii::app()->user->setFlash("success", "修改 <b>{$_POST['fields']['name']}</b> 字段成功!");
					} else {
						Yii::app()->user->setFlash("error", "保存失败!");
					}
				} else {
					Yii::app()->user->setFlash("error", $create['msg']);
				}
				$this->redirect(array('DatasetField/index/id/'.$id));

			} elseif($type == 'group') {
				//修改字段组
				$en_name = $_POST['fields']['en_name'];
				unset($_POST['fields']['en_name']);

				$fields = $_POST['fields'];

				//这部分工作可放在自定义rules里
				if (!isset($model->fields[$en_name])) {//指定英文标识不存在
					$data['errorMsg'] = "英文标识 {$en_name} 不存在";
				} else {
					$model->fields[$en_name] = $fields;
					if($model->save()) {
						$this->addLog('ds', $dsModel->id, '修改了“'.$dsModel->name.'”中的字段组“'.$_POST['fields']['name'].'”');
						Yii::app()->user->setFlash("success", "新建字段成功!");
						$this->redirect(array('DatasetField/index/id/'.$model->id));
					} else {
						print_r($model->getErrors());
					}
				}
			}
		}

		$dbModel = $this->loadModel((int)$dsModel->database_id, 'db');

		if ($type == 'field') {

			$data = array();
			$data['enName'] = $enName;
			$data['model'] = $model;
			$data['dbModel'] = $dbModel;
			$data['dsModel'] = $dsModel;
			$data['datasetId'] = $model->id;
			$data['dataTree'] = $this->dataTree($id);
			$data['info'] = $this->promptInfo();
			if ($group) {
				$data['group'] = $group;
			}
			$this->renderPartial('_form_field', $data);
		} elseif ($type == 'group') {
			$this->renderPartial('create_group', array('model' => $model, 'dsModel' => $dsModel, 'dbModel' => $dbModel, 'datasetId'=> $id, 'dataTree' => $this->dataTree($id), 'update'=>true, 'enName'=>$enName));
		}

	}

	/**
	* 删除字段
	* @param integer $id 元素集id
	* @param string $enName 英文标识
	* @author gentle
	*/
	public function actionDelete($id, $enName, $group = '') {
		$dsModel = $this->loadModel((int)$id, 'ds');
		$itemModel = $this->loadModel((int)$id, 'item', 'dataset_id', true);
		$old_name = '';
		//删除字段下数据
		foreach ($itemModel as $key=>$value) {
			if ($group) {
				if(isset($value->data[$group][$enName])) {
					unset($value->data[$group][$enName]);
				}
			} else {
				if(isset($value->data[$enName])) {
					unset($value->data[$enName]);
				}
			}
			$value->save();
		}
		
		$old_name = $dsModel['fields'][$enName]['name'];
		$old_type = $dsModel['fields'][$enName]['type'];
		$fieldInfo = $dsModel->deleteField($enName, $group);
		if ($dsModel->save()) {
			//根据字段类型记录不同日志
			if ($old_type=='group') {
				$this->addLog('ds', $dsModel->id, '清理了“'.$dsModel->name.'”中的字段组“'.$old_name.'”');
			}else{
				$this->addLog('ds', $dsModel->id, '清理了“'.$dsModel->name.'”中的字段“'.$old_name.'”');
			}
			Yii::app()->user->setFlash("success", "删除 <b>{$fieldInfo['name']}</b> 字段成功!");
		} else {
			Yii::app()->user->setFlash("error", "删除 <b>{$fieldInfo['name']}</b> 字段失败!");
		}
		$this->redirect(array('DatasetField/index/id/'.$id));
	}

}
