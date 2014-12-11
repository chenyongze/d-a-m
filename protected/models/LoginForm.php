<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel {
	public $username;
	public $password;
	public $rememberMe;

	private $_identity;

	public function rules() {
		return array(
			array('username, password', 'required'),
			array('rememberMe', 'boolean'),
			array('password', 'authenticate'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels() {
		return array(
			'username'=>'用户名',
			'password'=>'密码',
			'rememberMe'=>'记住我',
		);
	}

	/**
	* 验证密码
	* @author gentle
	*/
	public function authenticate($attribute,$params) {
		$this->_identity=new UserIdentity($this->username,$this->password);
		$this->_identity->authenticate();
		if($this->_identity->errorCode == UserIdentity::ERROR_USERNAME_INVALID){
			$this->addError('username','账号不存在');
		}else if($this->_identity->errorCode == UserIdentity::ERROR_PASSWORD_INVALID){
			$this->addError('password','输入的密码有误，请重新输入');
		}	
	}

	/**
	* 登陆
	* @author gentle
	*/
	public function login() {
		if($this->_identity===null) {
			$this->_identity=new UserIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE) {
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		} else {
			return false;
		}
	}
}
