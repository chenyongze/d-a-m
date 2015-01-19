<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex(){
		//未登录跳到“登陆页面”
		if(empty(Yii::app()->user->id)){
			$this->redirect(array('site/login'));
		}else{
			//有结构定义权限则跳入“结构定义”
			if($this->actCheck('dbset')){
				$this->redirect(array('cardDb/index'));
			//有数据管理权限则跳入“数据操作”
			}else if($this->actCheck('item-add')){
				$this->redirect(array('cardItem/index/0'));
			}else{
				//若当前有登录但没有dbset或item-add权限则踢出重新登录
				Yii::app()->user->logout();
				$this->redirect(array('site/login'));
			}
		}
		exit();
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	* 登陆
	* @author gentle
	*/
	public function actionLogin() {
		
		//禁止重复登录
		if(Yii::app()->user->id){
			$this->redirect(array('site/index'));
			exit();
		}
		
		$model=new LoginForm;
		if (isset($_POST['LoginForm'])) {
			$model->attributes=$_POST['LoginForm'];
			//if($model->validate() && $model->login()) {
			if ($model->validate() && $model->login()) {
				$this->addLog('user', Yii::app()->user->id, '“'.Yii::app()->user->name.'”登陆系统');
				if($this->actCheck('dbset')){
					$this->redirect(array('CardDb/index'));
				}else{
					$this->redirect(array('CardItem/index', 'id'=>0));
				}
			}
		}
		$this->render('login',array('model'=>$model));
	}

	/**
	* 退出
	* @author gentle
	*/
	public function actionLogout() {
		$this->addLog('user', Yii::app()->user->id, '“'.Yii::app()->user->name.'”退出系统');
		Yii::app()->user->logout();
		$this->redirect(array('Site/login'));
	}
}
