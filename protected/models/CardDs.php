<?php

class CardDs extends EMongoDocument
{
	public $id;
	public $database_id;
	public $name;
	public $en_name;
	public $fields = array();
	public $listorder = 0;
	public $request_times = 0;
	public $last_uid = 1;
	public $update_time = 0;

	/*
	private $fields = array(
				'name' => array(
					'type' => 'field',
					'listorder' => 1,
				),
				'sex' => array(
					'type' => 'field',
					'listorder' => 2,
				),
				'intro' => array(
					'type' => 'field',
					'listorder' => 3,
				),
				'addition' => array(
					'type' => 'group',
					'listorder' => 4,
					'fields' => array (
						'attach' => array(
							'type' => 'field',
							'listorder' => 1,
						),
						'damage' => array(
							'type' => 'field',
							'listorder' => 2,
						),
						'range' => array(
							'type' => 'field',
							'listorder' => 3,
						),
					)
				),
			),

	private $fields = array(
				array (
					'en_name' => 'name',
					'listorder' => 1,
				),
				array (
					'en_name' => 'sex',
					'listorder' => 2,
				),
				array (
					'en_name' => 'intro',
					'listorder' => 3,
				),
				array (
					'en_name' => 'addition',
					'listorder' => 4,
				),
			),
	*/

	public function getCollectionName() {
		return 'dataset';
	}

	public function getFields($fieldName = '') {
		$fields = $this->fields;
		if (!empty($fieldName)) {
			unset($fields[$fieldName]);
		}
		return $fields;
	}

	public function getField($enName) {
		$field = $this->fields[$enName];
		return $field;
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
			//array('name, en_name, database_id', 'required'),
			array('name, en_name, database_id', 'required'),
			//array('name', 'EMongoUniqueValidator'),
			array('name, en_name', 'EMongoUniqueValidator', 'on'=>'Create'),
			array('id, database_id', 'numerical', 'integerOnly' => true),
			array('fields', 'safe'),
		);
	}

	public function attributeLabels() {
		return array(
			'id'			=> 'id',
			'name'			=> '名称',
			'en_name'		=> '英文标识',
			'listorder'		=> '排序',
			'request_times'		=> '请求次数',
			'last_uid'		=> '最后修改用户',
			'update_time'		=> '最近更新时间',
		);
	}

	public function search($caseSensitive = false) {

		$criteria = new EMongoCriteria;
		if (!empty($this->id)) {
    			$criteria->id = $this->id;
		}
		if (!empty($this->database_id)) {
    			$criteria->database_id = (int)$this->database_id;
		}
		if (!empty($this->name)) {
    			$criteria->name = $this->name;
		}
		if (!empty($this->en_name)) {
    			$criteria->en_name = $this->en_name;
		}
		if (!empty($this->request_times)) {
    			$criteria->request_times = $this->request_times;
		}
	 	$criteria->sort('id', EMongoCriteria::SORT_DESC);

    		//$criteria->last_uid = $this->last_uid;
    		//$criteria->update_time = $this->update_time;

		return new EMongoDocumentDataProvider($this, array(
			'criteria' => $criteria,
		));
	}

	/**
	* 更新字段排序
	* @param array $sort 排序数组
	* @author gentle
	*/
	public function fieldChangeSort($sort) {
		foreach ($sort as $key=>$value) {
			if (strpos($key, '.') !== false) {
				list($group,,$field) = explode('.', $key);
				if (isset($this->fields[$group]['fields'][$field])) {
					$this->fields[$group]['fields'][$field]['listorder'] = intval($sort[$key]);
				}
			} else {
				if (isset($this->fields[$key])) {
					$this->fields[$key]['listorder'] = intval($sort[$key]);
				}
			}
		}
		return $this;
	}

	/**
	* 排序输出字段
	* @author gentle
	*/
	public function sortField() {
		$sortArray = array();
		//$this->fields = array_reverse($this->fields);
		//$n = 0;
		//foreach ($this->fields as $key=>$value) {
			//$this->fields[$key]['fieldId'] = $n;
			//$n++;
		//}
		//print_r($this->fields);
		//exit();
		foreach ($this->fields as $key=>$value) {
			//$sortArrayId[] = $value['fieldId'];
			$sortArray[] = isset($value['listorder'])?intval($value['listorder']):0;
			if ($value['type'] == 'group' && !empty($value['fields'])) {
				$groupSortArray = array();
				foreach ($value['fields'] as $gkey => $gvalue) {
					$groupSortArray[] = isset($gvalue['listorder'])?intval($gvalue['listorder']):0;
				}
				array_multisort($groupSortArray, SORT_ASC, $this->fields[$key]['fields']);
			}
		}
		//array_multisort($sortArrayId, SORT_DESC, $this->fields);
		array_multisort($sortArray, SORT_ASC, $this->fields);
		return $this;
	}

	/**
	* 删除字段
	* @param string $enName 字段英文标识
	* @author gentle
	*/
	public function deleteField($enName, $group = '') {
		if ($group) {
			$fieldInfo = $this->fields[$group]['fields'][$enName];
			unset($this->fields[$group]['fields'][$enName]);
		} else {
			$fieldInfo = $this->fields[$enName];
			unset($this->fields[$enName]);
		}
		return $fieldInfo;
	}

	/**
	* Field 增/改 处理
	* 	涉及数据验证 (这里应该挪到rules里)
	* @author gentle
	*/
	public function changeField($fields, $operation = 'create') {

		//这部分工作可放在自定义rules里
		if (empty($fields['name']) || empty($fields['en_name'])) {
			$data['code'] = 1;
			$data['msg'] = "名字/英文标识字段不能为空";
		} else {
			$en_name = $fields['en_name'];
			if (isset($fields['group'])) {
				$group = $fields['group'];
			} else {
				$group = '';
			}
			$old_en_name = isset($fields['old_en_name']) ? $fields['old_en_name'] : '';
			$old_name = isset($fields['old_name']) ? $fields['old_name'] : '';
			unset($fields['en_name'], $fields['old_en_name'], $fields['old_name']);

			if ( (($group && isset($this->fields[$group]['fields'][$en_name])) || (!$group && isset($this->fields[$en_name]))) && $operation != 'update' ) {
				$data['code'] = 2;
				if ($group) {
					$data['msg'] = "英文标识 {$group}.{$en_name} 重复";
				} else {
					$data['msg'] = "英文标识 {$en_name} 重复";
				}
			} else {
				foreach ($this->fields as $key=>$value) {
					//修改字段名不检验
					if (!empty($old_name) && $old_name==$fields['name']) {
					} else if ($value['name']==$fields['name']) {
						$data['code'] = 3;
						$data['msg'] = "字段名 {$fields['name']} 重复";
						return $data;
					}
				}
				$fields['listorder'] = isset($fields['listorder']) ? intval($fields['listorder']): 0;
				$fields['must'] = (isset($fields['must']) && $fields['must']==1) ?  1 : 0;

				if ($group) {
					$this->fields[$group]['fields'][$en_name] = $fields;
				} else {
					$this->fields[$en_name] = $fields;
				}
				if ($operation=='update' && $en_name!=$old_en_name) {
					if ($group) {
						unset($this->fields[$group]['fields'][$old_en_name]);
					} else {
						unset($this->fields[$old_en_name]);
					}
				}
				$data['code'] = 0;
				$data['data'] = $this;
			}
		}
		return $data;
	}

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}



	/**
	* 元素集列表
	* @param string $enName 字段英文标识
	* @author gentle
	*/
	public function getList($databaseId = 0, $enName = '', $select = array()) {
		if ($databaseId!=0) {
		    $condition = array('database_id' => (int)$databaseId);
		} else {
		    $condition = array('en_name' => $enName);
		}
		$dsList = self::findAllByAttributes(
			$condition,
			array(
			    'select' => $select,
			    'order' => 'id desc',
			    'limit' => 1,
			)
		);
		//手工过滤了AR填充的Model数据
		foreach ($dsList as $key=>$value) {
			$value = $value->toArray();
			foreach ($value as $k=>$v) {
				if (!in_array($k, $select)) {
					unset ($value[$k]);
				}
			}
			$dsList[$key] = $value;
		}
		return $dsList;
	}

	public function getFieldList($datasetId = 0, $enName = '') {
		$condition = array();
		$select = array('fields');
		if(empty($datasetId) || empty($enName)){
			return false;
		}
		
		$condition = array(
		  	'database_id' => (int)$datasetId,
		  	'en_name' => $enName
		 );
		
		$fieldList = self::findAllByAttributes(
			$condition,
			array(
			    'select' => $select,
			    'order' => 'id desc',
			)
		);
		
		//手工过滤了AR填充的Model数据
		foreach ($fieldList as $key=>$value) {
			$value = $value->toArray();
			foreach ($value as $k=>$v) {
				if (!in_array($k, $select)) {
					unset ($value[$k]);
				}
			}
			$fieldList[$key] = $value;
		}
		return $fieldList;
	}

	protected function beforeSave() {
	    if (parent::beforeSave()) {
		if ($this->isNewRecord) {
		    $this->id = $this->getAutoIncreaseId(false);
		}
		$this->id = (int)$this->id;
		$this->database_id = (int)$this->database_id;
		$this->update_time = time();
		return true;
	    } else {
		return false;
	    }
	}

}
