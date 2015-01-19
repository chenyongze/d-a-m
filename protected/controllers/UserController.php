<?php
/**
 * 用户管理
 * @author Gavin
 */
class UserController extends Controller {

	public function init(){
		$this->actCheck('user', false);
		$this->layout = '//layouts/column1';
	}
	
   /**
	* 卡牌库列表
	* @author gentle
	*/
	public function actionIndex() {
		$data = array();
		$data['model'] = new User();
		$data['info'] = $this->promptInfo();
		$this->render('index', $data);
	}

	/**
	* 创建卡牌库
	* @author gentle
	*/
	public function actionCreate(){
		$model = new User();
		if(isset($_POST['User'])){
			//admin管理员不能手动添加
			if($_POST['User']['username']=='admin'){
				$this->redirect_back();exit();
			}
			//添加默认密码
			if($_POST['User']['password']==''){
				$_POST['User']['password'] = Yii::app()->params['def_password'];
			}
			
			//接收范围权限并预处理
			$_POST['User']['scope'] = User::model()->makeScope();
			$_POST['User']['password'] = md5($_POST['User']['password']);
			$model->attributes = $_POST['User'];
			if ($model->save()) {
				$this->addLog('user', $model->id, '添加了名为“'.$model->username.'”的“'.Yii::app()->params["role"][$model->role]["name"].'”');
				Yii::app()->user->setFlash("success", "新建 <b>{$model->username}</b> 用户成功!");
			} else {
				$errorMsg = '';
				$errorErr = $model->getErrors();
				foreach ($errorErr as $value) {
					$errorMsg .= "\t".$value[0];
				}
				$errorMsg = trim($errorMsg, ',');
				Yii::app()->user->setFlash("error", $errorMsg);
			}
			$this->redirect(array('user/index'));
		}
		$yxlist = User::model()->getScope();
		$this->renderPartial('_form', array('model' => $model, 'yxlist'=>$yxlist));
	}
	
	/**
	* 修改卡牌库
	* @param integer $id 卡牌库id
	* @author gentle
	*/
	public function actionUpdate($id){
		$model = $this->loadModel((int)$id, 'user');
		//admin管理员不能被被其他人修改
		if($model->username=='admin' && $this->get_login_user('username')!='admin'){
			$this->redirect_back();exit();
		}
		if(isset($_POST['User'])){
			//若没有填密码则忽略该项
			if($_POST['User']['password']==''){
				unset($_POST['User']['password']);
			}else{
				$_POST['User']['password'] = md5($_POST['User']['password']);
			}
			$_POST['User']['scope'] = User::model()->makeScope();	//接收范围
			$model->attributes = $_POST['User'];
			if($model->save()){
				$this->addLog('user', $model->id, '修改了名为“'.$model->username.'”的“'.Yii::app()->params["role"][$model->role]["name"].'”');
				Yii::app()->user->setFlash("success", "修改 <b>{$model->username}</b> 数据库成功!");
				$this->redirect(array('user/index'));
			}else{
				Yii::app()->user->setFlash("error", "修改 <b>{$model->username}</b> 数据库失败!");
			}
		}
		$model->password = '';
		$scopeinfo = User::model()->decodeScope($model->scope);	//获取选中范围
		$yxlist = User::model()->getScope();
		$this->renderPartial('_form', array('model' => $model, 'update' => true, 'yxlist'=>$yxlist, 'scopeinfo'=>$scopeinfo));
	}

	/**
	* 删除卡牌库
	* @param integer $id 卡牌库id
	* @author gentle
	*/
	public function actionDelete($id) {
		$model = $this->loadModel($id, 'user');
		$id = $model->id;
		$username = $model->username;
		$role = $model->role;
		
		if ($model->delete()) {
			$this->addLog('user', $id, '清理了名为“'.$username.'”的“'.Yii::app()->params["role"][$role]["name"].'”');
			Yii::app()->user->setFlash("success", "删除 <b>{$model->username}</b> 数据库成功!");
		} else {
			Yii::app()->user->setFlash("error", "删除 <b>{$model->username}</b> 数据库失败!");
		}
		$data = array();
		$data['info'] = $this->promptInfo();
		echo json_encode($data['info']);
	}


}
