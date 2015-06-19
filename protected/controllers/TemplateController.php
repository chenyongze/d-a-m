<?php
/**
 * 模板管理
 * @author yongze
 *
 */

class TemplateController extends Controller {
    
    public function init(){
        $this->actCheck('template', false);
    }

	/**
	* 模板列表
	* @author yongze
	*/
	public function actionIndex($id) {
	    $dsModel = $this->loadModel((int)$id, 'ds');
	    $dbModel = $this->loadModel((int)$dsModel->database_id, 'db');
// 	    $tpModel = $this->loadModel((int)$id, 'tp','dataset_id',true);

	    $attr = Template::model()->attributeLabels();
		$criteria = new EMongoCriteria;
		
		
	 	//添加查询条件
        if(isset($_GET['sub'])){
	        $criteria = $this->fillCond($criteria, Template::model()->attributeLabels());
    	}
//         FunctionUTL::Debug($criteria);
        $count = Template::model()->count($criteria);
        $pages = new CPagination($count);
        $perPage = 10;
        $pages->pageSize = $perPage;
        $offset = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($offset - 1) * $perPage;
        $criteria->limit($perPage)->offset($offset)->sort('id', EMongoCriteria::SORT_DESC);
        $tpModel = Template::model()->findAll($criteria);
      	$data['templateModels'] = $tpModel;
        $data['pages'] = $pages;
        $data['attr'] = $attr;	//模型属性
        
        $data['model'] = Template::model();
        $data['dbModel'] = $dbModel;
        $data['dsModel'] = $dsModel;
        $data['datasetId'] = $id;
//         $data['dataTree'] = $this->dataTree($dsModel->database_id, 'datasetField/index');
        $data['info'] = $this->promptInfo();
        
		$this->render('index', $data);
	}

	/**
	* 新建模板
	* @author yongze
	*/
	public function actionCreate($id) {
	    
	    $dsModel = $this->loadModel((int)$id, 'ds');
	    $dbModel = $this->loadModel((int)$dsModel->database_id, 'db');
	    $model = new Template('Create');
	    $data = array();
	    if(isset($_POST['Template'])){
	        
	        $model->attributes = $_POST['Template'];
			if ($model->save()) {
				$this->addLog('template', $model->id, '添加新模板“'.$model->tpname.'”');
				Yii::app()->user->setFlash("success", "新建 <b>{$model->tpname}</b> 模板成功!");
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
			$this->redirect(array('/Template/Index/19'));
	    }
	    
	    $data['dbModel'] = $dbModel;
	    $data['dsModel'] = $dsModel;
	    $data['model'] = $model;
	     
// 	    FunctionUTL::Debug($data['model']);//exit;
	    $this->render('edit',$data);
	}

	/**
	* 修改模板
	* @author yongze
	*/
	public function actionUpdate($id) {
	    $model = $this->loadModel((int)$id, 'tp');
	    //范围验证
	    $this->scopeCheck($model->id);
	    
	    if(isset($_POST['Template'])){
	        $model->attributes = $_POST['Template'];
	        if($model->save()){
	            $this->addLog('db', $model->id, '修改了“'.$model->tpname.'”');
	            Yii::app()->user->setFlash("success", "修改 <b>{$model->tpname}</b>成功!");
	        }else{
	            Yii::app()->user->setFlash("error", "修改 <b>{$model->tpname}</b> 失败!");
	        }
	        
	        $this->redirect(array('/Template/Index/19'));
	    }
	    $data = array('model' => $model, 'update' => true);
	    $this->render('edit',$data);
	}

	/**
	* 删除模板
	* @param integer $id 元素集id
	* @author yongze
	*/
	public function actionDelete($id) {

	    $model = $this->loadModel($id, 'tp');
	    //范围验证
	    $this->scopeCheck($id);
	    $old_id = $model->id;
	    if ($model->delete()) {
// 	        $this->addLog('template', $old_id, '删除了“'.$old_name.'”');
	        Yii::app()->user->setFlash("success", "删除 <b>模板ID:{$model->id}</b> 数据库成功!");
	    } else {
	        Yii::app()->user->setFlash("error", "删除 <b>模板ID:{$model->id}</b> 数据库失败!");
	    }
	     
	    $data = array();
	    $data['info'] = $this->promptInfo();
	    echo json_encode($data['info']);
	    //$this->redirect(array('CardDb/index'));
	    
	}

}
