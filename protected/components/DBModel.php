<?php
/**
 * 数据库模型基类
 * @author user
 */
class DBModel extends EMongoDocument{
	
	/**
	 * 获取模型对象
	 * @param $className 自己
	 * @return CMole 模型对象
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
	
	/**
	 * mongodb模型必备方法
	 * @author lyj
	 */
	public function getCollectionName(){
		return 'test';
	}
	
	protected function beforeSave() {
	    if (parent::beforeSave()) {
			if($this->isNewRecord){
			    $this->id = $this->getAutoIncreaseId(false);
			}
			$this->last_uid = Yii::app()->user->id;	//添加操作者
			$this->update_time = time();
			return true;
	    } else
			return false;
	}
	
	/**
	 * If we override this method to return something different than '_id',
	 * internal methods as findByPk etc. will be using returned field name as a primary key
	 * @return string|array field name of primary key, or array for composited key
	 */
	public function primaryKey() {
		return 'id';
	}
	
	/**
	 * 根据传入的数据生成表索引
	 * 	array(
	 * 		array("i" => -1),		//字段i倒序
	 * 		array("name"=> 1),		//字段name正序
	 * 	)
	 * @param $indexs	索引配置
	 * @return array	执行结果
	 */
	public function createIndexs($indexs=array()){
		if(empty($indexs)){
			return false;
		}
		$rs = array();
		foreach($indexs as $ik=>$iv){
			$rs[$ik] = $this->getCollection()->ensureIndex($iv);
		}
		return $rs;
	}
	
	
	/**
	 * 实现mongo唯一验证
	 * @param string 	被验证的属性名字		password
	 * @param array		制定的校验规则		array()
	 */
	public function unique($attribute,$params){
		$val = $this->$attribute;
		
		$criteria = new EMongoCriteria(array(
			'conditions'=>array(
				'_id'		=> array('<>' => empty($this->_id)?0:$this->_id),
				$attribute	=> array('==' => $val)
			)
	 	));
		
		$cou = $this->count($criteria);
		if($cou){
			$this->addError($attribute, '该记录已经存在！');
		}
	}
	
	/**
	 * 根据传入条件数组查询
	 * 注意此方法跳过了criteria的环节，直接执行查询，虽提高了适应能力，但大大增加使用者要求
	 * @param $query array 查询数组
	 * @author lyj
	 * @return array 查询结果集
	 */
	public function findAllByQuery($query, $fields=false, $sort=false, $offset=false, $limit=false){
		if($this->beforeFind()){
			if(empty($fields)){
				$cursor = $this->getCollection()->find($query);			//所有字段
			}else{
				$cursor = $this->getCollection()->find($query, $fields);//指定字段
			}
			if($this->getUseCursor()){
				return new EMongoCursor($cursor, $this->model());	//游标错误
			}else{
				if($sort){
					$cursor->sort($sort);
				}
				if($offset){
					$cursor->skip($offset);
				}
				if($limit){
					$cursor->limit($limit);
				}
				$qq = $this->_populateRecords($cursor);	//游标结果集转对象数组
				return $qq;	
			}
		}
		return array();	//结束返回空
		
		/* 
		//查询数组示例
		$query = array( 
			'game_code' => 'dota2',							// {'game_code':'dota2'}
			'$or' => array( 								// {$or: [{name: {$gte: 龙龙}}, {真名: '龙龙'}]
				array('name' => array('$gte'=>'龙龙')), 
				array('真名' => array('龙龙'))
			),
			'age' => array( 								// {age: {$gt: 5, $lt: 20}}
				'$gt' => 5, 
				'$lt' => 20 
			),
			'age' => array(									//{age:{$in:[1,2,3], $all:[2,3]}}
				'$in'=>array(1,2,3),
				'$all'=>array(2,3)
			),
		)
		//调用示例
		$rs = Entity::model('entity_'.Yii::app()->params['dbGame'].'_hero')->findAllByArray(
			$query, array('_id','name')
		);
		*/
	}
	
	/**
	 * 简化的数据获得方法
	 * @param $cursor	mogon查询后游标
	 * @return array 数据
	 */
	private function _populateRecords($cursor){
		$rs = array();
		foreach($cursor as $vo){		//循环1
			$rs[] = $vo;
		}
		return $rs;
	}
	
	/**
	 * 获取登录用户信息
	 * @param $key 属性值
	 * @param $key1 二级属性值
	 * @return unknown_type
	 */
	public static function get_login_user($key='',$key1=false){
		$rs = 0;
		if(!Yii::app()->user->isGuest){
			$rs = Yii::app()->user->getState('info');
			if(!empty($key)){
				$rs = isset($rs[$key])?$rs[$key]:'';
				if($key1!==false){
					$rs = isset($rs[$key1])?$rs[$key1]:'';
				}
			}
		}
		return $rs;
	}
    
    /**
     * 根据表名获取记录总数
     * @param $tname	string	表名（可以是由item虚化出来的，如“item_nnhysj_tz”）
     * @return 记录总数
     */
   	public function getCount($tname){
   		$ts = $this->parseRealTable($tname);
   		$this->applyScopes($ts['criteria']);
   		$rows = DBModel::model()->getDb()->selectCollection($ts['name'])->count($ts['criteria']->getConditions());
   		return $rows;
	}
	
	/**
     * 根据表名获取记录
     * @param $tname	string	表名（可以是由item虚化出来的，如“item_nnhysj_tz”）
     * @return 记录总数
     */
   	public function getFind($tname){
   		$ts = $this->parseRealTable($tname);
   		if($this->beforeFind()){
			$this->applyScopes($ts['criteria']);
			$rows = DBModel::model()->getDb()->selectCollection($ts['name'])->find($ts['criteria']->getConditions());
   		}
   		return $rows;
	}
	
	/**
     * 根据表名清理记录
     * @param $tname	string	表名（可以是由item虚化出来的，如“item_nnhysj_tz”）
     * @return 记录总数
     */
   	public function getRemove($tname){
   		$ts = $this->parseRealTable($tname);
   		$this->applyScopes($ts['criteria']);
		return CardItem::model()->getDb()->selectCollection($ts['name'])->remove($ts['criteria']->getConditions());
   	}
   	
	/**
	 * 根据表名得到真实的表和对应的查询条件
	 * @param $tname	string	待解析表名
	 * @return 真实表名和条件
	 */
	public function parseRealTable($tname){
		$rs = array(
			'name'=>$tname,
			'criteria'=>new EMongoCriteria(),
		);
   		if(preg_match('/^(item_)/i', $tname)){
   			$tinfo = explode('_', $tname);
   			if(count($tinfo)==3){
   				$dbinfo = CardDb::model()->findByAttributes(array('en_name'=>$tinfo[1]));
   				
   				if($dbinfo){
   					$dsinfo = CardDs::model()->findByAttributes(array('database_id'=>$dbinfo->id, 'en_name'=>$tinfo[2]));
   					if($dsinfo){
   						$rs['name'] = $tinfo[0];
       					$rs['criteria']->dataset_id = (int)$dsinfo['id'];
   					}
   				}
   			}
   		}
		return $rs;
	}
    
    

}