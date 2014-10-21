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
	public function actionIndex()
	{
		// renders the view file 'protected/views/site/index.php'
		// using the default layout 'protected/views/layouts/main.php'
		$this->render('index');
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
		$model=new LoginForm;
		if (isset($_POST['LoginForm'])) {
			$model->attributes=$_POST['LoginForm'];
			//if($model->validate() && $model->login()) {
			if ($model->login()) {
				$this->redirect(array('CardDb/index'));
			}
		}
		$this->render('login',array('model'=>$model));
	}

	/**
	* 退出
	* @author gentle
	*/
	public function actionLogout() {
		Yii::app()->user->logout();
		$this->redirect(array('Site/login'));
	}
}
