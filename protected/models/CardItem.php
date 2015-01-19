<?php

class CardItem extends DBModel {

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
	
	/**
	 * 获取某个字段当前最大元素数
	 * @author gavin
	 * @param $dsid			int		表id
	 * @param $field_key 	string	字段英文名
	 * @return int	最大元素数
	 */
	public function getFieldMaxSize($dsid, $field_key){
		$max = 1;
		//思路一：array('$size'=>10~2)，使用第一个有结果的
			//太有局限性，废除
		//思路二：分组统计所有
		$group = $this->getCollection()->group(
			array('id' => 1),
			array('size'=>0),	//, 'list'
			"function (obj, prev) {
				prev.size = obj.data.".$field_key.".length;
				//prev.list = obj.data.".$field_key.";
			}",
			array('condition' => array(
				"dataset_id" => $dsid,					//指定实体
				'data.'.$field_key => array('$exists'=>true)	//有该字段
			))
		);
		//若分组成功，从其中选取size最大的
		if($group['ok']){
			foreach($group['retval'] as $gv){
				if($gv['size'] > $max){
					$max = $gv['size'];
				}
			}
		}
		return $max;
	}

	protected function beforeSave() {
	    if (parent::beforeSave()) {
			$this->id = (int)$this->id;
			$this->dataset_id = (int)$this->dataset_id;
			return true;
	    } else {
			return false;
	    }
	}

}
