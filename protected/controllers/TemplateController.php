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
        $model = Template::model();
	    $attr = $model->attributeLabels();
	    $model->dataset_id = (int)$id;
        $data['attr'] = $attr;	//模型属性
        $data['model'] = $model;
        $data['dbModel'] = $dbModel;
        $data['dsModel'] = $dsModel;
        $data['datasetId'] = $dsModel->id;
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
			$this->redirect(array('/Template/Index/'.$dsModel->id));
	    }
	    
	    $data['dbModel'] = $dbModel;
	    $data['dsModel'] = $dsModel;
	    $data['model'] = $model;
	    $data['datasetId'] = $id;
	    $this->_getFieldsInfos($data['_txtfiled'],$id);
	    $this->render('edit',$data);
	}

	/**
	* 修改模板
	* @author yongze
	*/
	public function actionUpdate($id) {
	    $model = $this->loadModel((int)$id, 'tp');
	    $setid = $model->dataset_id;
	    if(empty($setid)){
	        Yii::app()->user->setFlash("error", "修改 <b>{$model->tpname}</b> 失败!");
	        $this->redirect(array('/Template/Index/0'));
	    }
	    $dsModel = $this->loadModel((int)$setid, 'ds');
	    $dbModel = $this->loadModel((int)$dsModel->database_id, 'db');
	    //范围验证
	    $this->scopeCheck($model->id);
	    if(isset($_POST['Template'])){
	        
	        if(isset($_POST['Template']['type'])){
	            $_POST['Template']['type'] = (int)$_POST['Template']['type'];
	        }
	        if(isset($_POST['Template']['dataset_id'])){
	            $_POST['Template']['dataset_id'] = (int)$_POST['Template']['dataset_id'];
	        }
	        if(isset($_POST['Template']['content'])){
	            $_POST['Template']['content'] = htmlentities($_POST['Template']['content']);
	        }
	        $model->attributes = $_POST['Template'];
	        if($model->save()){
	            $this->addLog('db', $model->id, '修改了“'.$model->tpname.'”');
	            Yii::app()->user->setFlash("success", "修改 <b>{$model->tpname}</b>成功!");
	        }else{
	            Yii::app()->user->setFlash("error", "修改 <b>{$model->tpname}</b> 失败!");
	        }
	        
	        $this->redirect(array('/Template/Index/'.$setid));
	    }
	    $data['model'] = $model;
	    $data['dbModel'] = $dbModel;
	    $data['dsModel'] = $dsModel;
	    $data['update'] = true;
	    $this->_getFieldsInfos($data['_txtfiled'],$setid);
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
	        Yii::app()->user->setFlash("success", "删除 <b>模板ID:{$model->id}</b> 成功!");
	    } else {
	        Yii::app()->user->setFlash("error", "删除 <b>模板ID:{$model->id}</b> 失败!");
	    }
	     
	    $data = array();
	    $data['info'] = $this->promptInfo();
	    echo json_encode($data['info']);
	    
	}
	
	/**
	 * @info:获取字段属性信息
	 * @param array $info
	 * @param int $setid
	 */
	public function _getFieldsInfos(&$info=array(),$setid,$type=false){
	    if(empty($setid)) return -2;
	    $dsModel = $this->loadModel((int)$setid, 'ds');
	    $_tipfileds = array();
	    $info =<<<EOF
	<table class="table table-hover table-striped table-bordered table-condensed">
	<caption>调用标签预览表</caption>
	<thead>
	<tr class="even">
	<th>名称</th>
	<th>标签</th>
	</tr>
	</thead>
	<tbody>
EOF;
	    if(!empty($dsModel->fields)){
	        foreach ($dsModel->fields as $key=>$val){
	            //group 组
	            if($val['type'] == 'group'){
	                foreach ($val['fields'] as $_key => $_tval){
	                    
	                    $_tval['name'] = $_tval['name'].'1';
	                    $_k = $key.'_'.$_key.'_1';
	                    $info.=<<<EOF
        	            <tr>
        	            <td>{$_tval['name']}</td>
        	            <td>{F:$_k}</td>
        	            </tr> 
EOF;
	                    $_tipfileds[$_k] = $_tval['name'];
	               }
	            }else{
	                $_tipfileds[$key] = $val['name'];
	                $info.=<<<EOF
	            <tr>
	            <td>{$val['name']}</td>
	            <td>{F:$key}</td>
	            </tr>
EOF;
	            }

	        } 
	        $info.='</tbody></table>';
	        
	    }else {
	        return -3;
	    }
	    return ;
	}

}
