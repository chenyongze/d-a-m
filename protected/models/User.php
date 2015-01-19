<?php
/**
 * 系统用户模型
 * @author Gavin
 */
class User extends DBModel {
	//主要字段
	public $id;
	public $username;
	public $password;
	public $role = '30';			//角色
	public $scope;					//操作范围
	public $listorder = 0;
	public $last_uid = 1;
	public $update_time = 0;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function getCollectionName() {
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username', 'unique'),
			array('username,password,role,scope', 'required'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' 			=> 'ID',
			'username' 		=> '用户名',
			'password' 		=> '密码',
			'role' 			=> '角色',
			'scope'			=> '范围',
			'listorder'		=> '排序',
			'last_uid'		=> '最后修改用户',
			'update_time'	=> '最近更新时间',
		);
	}
	
	public function search($caseSensitive = false) {
		$criteria = new EMongoCriteria;
    	//$criteria->conditions['id'] = $this->id;

	 	$criteria->sort('id', EMongoCriteria::SORT_DESC);
		return new EMongoDocumentDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
	
	/**
	 * 用户管理范围候选项
	 * @return array 
	 */
	public function getScope(){
		//获取游戏列表
		$grs = CardDs::model()->getDBDSMap();
		$yxlist = array(0=>array(), 1=>array());
		foreach ( $grs as $key=>$val ) {
			$yxlist[0][$val['name']] = array();
			$yxlist[1][$key] = $val['name'];
			foreach($val['list'] as $kl=>$vl){
				$yxlist[0][$val['name']][$key.'--'.$kl] = $vl['name'];
			}
		}
		if(empty($yxlist)){
			$yxlist = array('没有数据');
		}
		return $yxlist;
	}
	
	/**
	 * 接收提交参数进行格式化
	 * @return array
	 */
	public function makeScope(){
		$val = $_POST['User']['scope'];			//分散
		$onescope = $_POST['User']['onescope'];	//整体
		$role = $_POST['User']['role'];
		
		//获取范围
		if (in_array($role, array('10', '20')) ) {
			if (empty($val) && empty($onescope)){
				$val = array();
			}else{
				$val = $onescope;
			}
		}
		
		unset($_POST['User']['onescope']);
		
		//范围预处理
		return $this->encodeScope($val);
	}
	
	/**
	 * 范围格式转换（页面->数据库）
	 * @param $scope array	提交的范围 	
	 * @return string/array	all/数组
	 */
	public function encodeScope($scopes){
		//获取游戏列表
		$grs = CardDs::model()->getDBDSMap();
		$grs = array_keys($grs);	//抽取数据
		$val = array();
		if (!array_diff($grs, $scopes)) {			//和所有游戏取差集
			$val = 'all';
		}else{
			foreach($scopes as $skey=>$scopesval){
				$a = explode("--",$scopesval);
				$a[0] = intval($a[0]);
				if(count($a)>1){
					$a[1] = intval($a[1]);
					if(!isset($val[$a[0]])){
						$val[$a[0]] = array();
					}
					$val[$a[0]][] = $a[1];
				}else{
					$val[] = $a[0];
				}
			}
		}
		return $val;
	}
	
	/**
	 * 范围格式转换（数据库->页面）
	 * @param $scope string/array	用户的范围 	
	 * @return array	一维数组
	 */
	public function decodeScope($scope){
		//空验证
		if(empty($scope)){
			return array();
		}
		
		$stinfo = array();
		if ( $scope == 'all' ) {
			$grs = CardDs::model()->getDBDSMap();
			$stinfo = array_keys($grs);	//抽取数据
		}else{
			foreach ( $scope as $key=>$val ) {
				if (is_array($val)) {
					foreach($val as $keys=>$vals){
						$stinfo[] = $key.'--'.$vals;
					}
				}else{
					$stinfo[] = $val;
				}
			}
		}
		return $stinfo;
	}
	
	/**
	 * 获取库查询的范围条件
	 * @return unknown_type
	 */
	public function getScopeDbCriteria(){
		$criteria = new EMongoCriteria();
        if($this->get_login_user('scopeInfo', 'db') != 'all'){
        	$criteria->addCond('id', 'in', $this->get_login_user('scopeInfo', 'db'));
        }
        return $criteria;
	}
	
	/**
	 * 获取表查询的范围条件
	 * @return unknown_type
	 */
	public function getScopeDsCriteria(){
		$criteria = new EMongoCriteria();
        if($this->get_login_user('scopeInfo', 'ds') != 'all'){
        	$criteria->addCond('id', 'in', $this->get_login_user('scopeInfo', 'ds'));
        }
        return $criteria;
	}

	

}
