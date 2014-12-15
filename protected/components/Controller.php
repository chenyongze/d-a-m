<?php
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column2';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	/**
	* 左侧树形菜单
	* @author gentle
	*/
	public $leftTree=array();


	/**
	* 初始化程序
	* @author gentle
	*/
	public function init() {
		$controller = Yii::app()->controller->id;
		$noCheck = array('site', 'card');
		if (Yii::app()->user->name=='Guest' && !in_array($controller, $noCheck)) {
			$this->redirect(array('Site/login'));
		}
		//如果是建库人员，则使用column1布局
		if (Yii::app()->user->name == 'creator') {
			$this->layout = '//layouts/column1';
		}
	}

	/**
	* 左侧栏树形结构
	* @author gentle
	*/
	public function dataTree($databaseId = 0) {
		$data = array();
		$databases = CardDb::model()->findAll();
		foreach ($databases as $key => $value) {
			$data[$key]['text'] = '<span>'.$value->name.'</span>';
			if ($databaseId == $value->id) {
				$data[$key]['expanded'] = true;
			} else {
				$data[$key]['expanded'] = false;
			}
			$data[$key]['children'] = array();
			$datasets = CardDs::model()->findAllByAttributes(array('database_id'=>(int)$value->id));
			if (empty($datasets)) {
				continue;
			}
			foreach($datasets as $k => $v) {
				$data[$key]['children'][$k] = array('text' => '<a href="'.$this->createUrl('/CardItem/Index/id/'.$v->id).'">'.$v->name.'</a>');
			}
		}
		return $data;
	}

	/**
	 * 载入model
	 * @param integer $id 元素集id
	 * @param string $type model类型
	 * @return Category the loaded model
	 * @throws CHttpException
	 * @author gentle
	 */
	public function loadModel($id, $type, $fieldType = 'id', $all = false) {
	    //$model = CardDs::model()->findByPk(new MongoID($id));
	    $modelClass = '';
	    switch ($type) {
	    	case 'db':
			$modelClass = 'CardDb';
			break;
		case 'ds':
			$modelClass = 'CardDs';
			break;
		case 'item':
			$modelClass = 'CardItem';
			break;
	    }
	    
	    if ($all) {
	    	$model = $modelClass::model()->findAllByAttributes(array($fieldType=>(int)$id));
	    } else {
	    	$model = $modelClass::model()->findByAttributes(array($fieldType=>(int)$id));
	    }

	    //if ($model === null)
		//throw new CHttpException(404, '请求的记录不存在');
	    return $model;
	}


	/**
	* 提示信息
	* @author gentle
	*/
	public function promptInfo() {
		$successInfo = Yii::app()->user->getFlash("success");
		$errorInfo = Yii::app()->user->getFlash("error");
		$info = array();
		if (!empty($successInfo)) {
			$info['type'] = 'success';
			$info['msg'] = $successInfo;
		}
		if (!empty($errorInfo)) {
			$info['type'] = 'error';
			$info['msg'] = $errorInfo;
		}
		return $info;
	}

}
