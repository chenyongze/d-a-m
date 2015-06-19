<?php
/**
 * 模板
 * @author yongze
 */
class Template extends DBModel {
	//主要字段
	public $id;
	public $type;		    //模板类型pc,wap,....
	public $dataset_id;	    //表id
	public $dataset_name;   //表名【归属】
	public $acttime;		//操作时间
	public $tpname;        //模板名称
	public $content;			//模板格式化数据
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function getCollectionName() {
		return 'template';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('type,tpname,content,dataset_id', 'required'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' 		=> '序号',
			'type' 	    =>  '模板类型',
			'dataset_id'=> '表id',
		    'dataset_name'=>'归属',
			'acttime'	=> '操作时间',
		    'tpname'    =>'模板名称',
			'content'	=> '模板数据',
		);
	}
	
	protected function beforeSave() {
	    if (parent::beforeSave()) {
// 			$this->uid = Yii::app()->user->id;
// 			$this->uname = Yii::app()->user->name;
			$this->acttime = time();
			return true;
	    } else
			return false;
	}
	
	public function search($caseSensitive = false) {
	
	    $criteria = new EMongoCriteria;
	    $criteria = User::model()->getScopeDbCriteria();
	    if (!empty($this->id)) {
	        $criteria->conditions['id'] = $this->id;
	    }
	    
	    $criteria->sort('id', EMongoCriteria::SORT_DESC);
	
	    return new EMongoDocumentDataProvider($this, array(
	        'criteria' => $criteria,
	    ));
	}
	

}