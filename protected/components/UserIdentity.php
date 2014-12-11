<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id;

	/**
	 * Authenticates a user.
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate() {
		/*
		$user=User::model()->find('LOWER(username)=?',array(strtolower($this->username)));
		if($user===null)
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		else if(!$user->validatePassword($this->password))
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		else
		{
			$this->_id=$user->id;
			$this->username=$user->username;
			$this->errorCode=self::ERROR_NONE;
		}
		return $this->errorCode==self::ERROR_NONE;
		*/
		
		//内置用户定义
		$init_user = array(
			'creator'=>array(
				'id'=>1,
				'password'=>'123456',
			),
			'publisher'=>array(
				'id'=>2,
				'password'=>'654321',
			),
		);
		
		//登录信息验证
		$this->username = strtolower($this->username);
		if(isset($init_user[$this->username])){
			$login_info = $init_user[$this->username];
			//验证密码
			if ($this->password == $login_info['password']) {
				$this->_id = $login_info['id'];
				$this->username = $this->username;
				$this->errorCode = self::ERROR_NONE;
			}else{
				$this->errorCode=self::ERROR_PASSWORD_INVALID;
			}
		}else{
			//若不是预定义用户则提示账号错误
			$this->errorCode=self::ERROR_USERNAME_INVALID;	
		}
		
	}

	/**
	 * @return integer the ID of the user record
	 */
	public function getId()
	{
		return $this->_id;
	}
}
