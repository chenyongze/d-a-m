<?php

class CardDb extends EMongoDocument
//class CardDb extends EMongoUniqueValidator
{
	public $id;
	public $name;
	public $en_name;
	public $listorder = 0;
	public $request_times = 0;
	public $last_uid = 1;
	public $update_time = 0;

	public function getCollectionName() {
		return 'database';
	}

	/**
	 * If we override this method to return something different than '_id',
	 * internal methods as findByPk etc. will be using returned field name as a primary key
	 * @return string|array field name of primary key, or array for composited key
	 */
	public function primaryKey() {
		return 'id';
	}

	public function rules() {
		return array(
			array('name, en_name', 'required'),
			array('name', 'EMongoUniqueValidator'),
			array('en_name', 'EMongoUniqueValidator', 'on'=>'Create'),
			array('en_name', 'match', 'pattern'=>'/\w/'),
		);
	}

	public function attributeLabels() {
		return array(
			'id'			=> 'id',
			'name'			=> '数据库名称',
			'en_name'		=> '英文标识',
			'listorder'		=> '排序',
			'request_times'		=> '请求次数',
			'last_uid'		=> '最后修改用户',
			'update_time'		=> '最近更新时间',
		);
	}

	public function search($caseSensitive = false) {

		$criteria = new EMongoCriteria;
		//$criteria->_sort = array("id"=>"desc");
	 	//$criteria->sort = array('id'=>EMongoCriteria::SORT_DESC);
		if (!empty($this->id)) {
    			$criteria->conditions['id'] = $this->id;
		}
		if (!empty($this->name)) {
    			$criteria->conditions['name'] = $this->name;
		}
		if (!empty($this->en_name)) {
    			$criteria->conditions['en_name'] = $this->en_name;
		}
		if (!empty($this->request_times)) {
    			$criteria->conditions['request_times'] = $this->request_times;
		}

	 	$criteria->sort('id', EMongoCriteria::SORT_DESC);
	 	//$criteria->select=array('id');
	 	//$criteria->sort = array('id'=>EMongoCriteria::SORT_DESC);
		//print_r($criteria);
		//exit();

    		//$criteria->last_uid = $this->last_uid;
    		//$criteria->update_time = $this->update_time;

		return new EMongoDocumentDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function getList() {
		$noSelect = array('_id');
		$dbList = self::findAll(array(
		    'order' => 'id desc',
		));
		//手工过滤了AR填充的Model数据
		foreach ($dbList as $key=>$value) {
			$value = $value->toArray();
			foreach ($value as $k=>$v) {
				if (in_array($k, $noSelect)) {
					unset ($value[$k]);
				}
			}
			$dbList[$key] = $value;
		}
		return $dbList;
	}

	protected function beforeSave() {
	    if (parent::beforeSave()) {
		if($this->isNewRecord){
		    $this->id = $this->getAutoIncreaseId(false);
		}
		
		$this->update_time = time();
		return true;
	    } else
		return false;
	}

}
