<?php
/**
 * 操作日志
 * @author Gavin
 */
class Log extends DBModel {
	//主要字段
	public $id;
	public $uid;			//操作者id
	public $uname;			//操作者姓名
	public $obj_cate;		//操作对象类型-传入
	public $obj_id;			//操作对象id-传入
	public $acttime;		//操作时间
	public $txt;			//操作描述-传入
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function getCollectionName() {
		return 'log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('obj_cate,txt', 'required'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' 		=> '序号',
			'uid' 		=> '操作者id',
			'uname' 	=> '操作者姓名',
			'obj_cate' 	=> '对象类型',
			'obj_id'	=> '对象id',
			'acttime'	=> '操作时间',
			'txt'		=> '描述信息',
		);
	}
	
	protected function beforeSave() {
	    if (parent::beforeSave()) {
			$this->uid = Yii::app()->user->id;
			$this->uname = Yii::app()->user->name;
			$this->acttime = time();
			return true;
	    } else
			return false;
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
