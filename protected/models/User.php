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
			array('username,password,role', 'required'),
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
	

}
