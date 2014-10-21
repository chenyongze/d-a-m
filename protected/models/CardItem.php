<?php

class CardItem extends EMongoDocument {

	public $id;
	public $dataset_id;
	public $data = array();
	public $listorder = 0;
	public $request_times = 0;
	public $last_uid = 1;
	public $update_time = 0;

	public function getCollectionName() {
		return 'item';
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
			array('dataset_id', 'required'),
			array('data', 'safe'),
		);
	}

	public function attributeLabels() {
		return array(
			'id'			=> 'id',
			'listorder'		=> '排序',
			'request_times'		=> '请求次数',
			'last_uid'		=> '最后修改用户',
			'update_time'		=> '最近更新时间',
		);
	}

	public function search($caseSensitive = false) {

		$criteria = new EMongoCriteria;
		if(!empty($this->id)){
    			$criteria->id = $this->id;
		}
		if(!empty($this->name)){
    			$criteria->name = $this->name;
		}
		if(!empty($this->en_name)){
    			$criteria->en_name = $this->en_name;
		}
		if(!empty($this->request_times)){
    			$criteria->request_times = $this->request_times;
		}
	 	$criteria->sort('id', EMongoCriteria::SORT_DESC);

    		//$criteria->last_uid = $this->last_uid;
    		//$criteria->update_time = $this->update_time;

		return new EMongoDocumentDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function getList($datasetId, $currPage = 1, $pageSize = 10) {
		$noSelect = array('id', 'dataset_id', '_id');
		$condition = array();
		$condition['condition'] = 'dataset_id = :dataset_id';
		$condition['params'] = array(':dataset_id' => $datasetId);
		$itemList = self::findAll(array(
		    'condition' => $condition['condition'],
		    'params' => $condition['params'],
		    'order' => 'id desc',
		    'offset' => $currPage,
		    'limit' => $pageSize,
		));
		//手工过滤了AR填充的Model数据
		foreach ($itemList as $key=>$value) {
			$value = $value->toArray();
			foreach ($value as $k=>$v) {
				if (in_array($k, $noSelect)) {
					unset ($value[$k]);
				}
			}
			$itemList[$key] = $value;
		}
		return $itemList;
	}

	protected function beforeSave() {
	    if (parent::beforeSave()) {
		if($this->isNewRecord){
		    $this->id = $this->getAutoIncreaseId(false);
		}

		$this->id = (int)$this->id;
		$this->dataset_id = (int)$this->dataset_id;
		
		$this->update_time = time();
		return true;
	    } else {
		return false;
	    }
	}

}
