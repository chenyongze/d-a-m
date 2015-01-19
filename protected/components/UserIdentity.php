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
			'username'	=> 'admin',
			'password'	=> md5('gavin'),
			'role'		=> '10',
		);
		
		//登录信息验证
		$user_info = User::model()->findByAttributes(array('username'=>$this->username));
		
		//内置admin
		if($this->username=='admin'){
			$user_model = new User();
			$user_model->attributes = $init_user;
			if ($user_model->save()) {
				$user_info = $user_model;
				unset($user_model);
			}
		}

		if($user_info){
			//验证密码
			if (md5($this->password) == $user_info['password']) {
				$this->_id = $user_info->id;
				$this->username = $user_info->username;
				$user_info = $user_info->toArray();
				$user_info['actions'] = $this->getActionPoint($user_info['role']);	//获取指定角色对应的权限列表
				$user_info['scopeInfo'] = $this->getParseScope($user_info['scope'], ($user_info['username']=='admin'&&$user_info['role']=='10')?1:0);	//解析范围
				$this->setState('info', $user_info);	//用户登录信息
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
	
	/**
	 * 获取指定角色对应的权限列表
	 * @param $no 	string	角色编号
	 * @return 权限点列表
	 */
	public function getActionPoint($no){
		$actions = array();
		$role = Yii::app()->params['role'];
		$point = array_keys(Yii::app()->params['action_point']);
		if(isset($role[$no])){
			foreach($role[$no]['actions'] as $ao){
				foreach($point as $ro){
					if(preg_match('/^'.$ao.'/i', $ro)){
						$actions[] = $ro;
					}
				}
			}
		}
		return $actions;
	}
	
	/**
	 * 解析用户数据范围，便于直接使用
	 * @param $scope 	string	范围信息
	 * @return 权限点列表
	 */
	public function getParseScope($scope, $isAdmin=0){
		if(empty($scope)){
			$scope = array();
		}

		$rs = array('db'=>array(), 'ds'=>array());
		if($scope=='all' || $isAdmin){
			$rs = array('db'=>'all', 'ds'=>'all');
		}else{
			foreach($scope as $key=>$vo){
				if(is_array($vo)){
					$rs['db'][] = intval($key);
					$rs['ds'] = array_merge($rs['ds'], $vo);
				}else{
					$rs['db'][] = intval($vo);
					$rs['ds'] = 'all';
				}
			}
		}
		return $rs;
	}
	
	
}
