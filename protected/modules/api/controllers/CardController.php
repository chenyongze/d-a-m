<?php

/**
 * 卡牌
 * @author gentle
 */
class CardController extends Controller {

	/*
	public function beforeAction($action) {
	}
	*/

	/**
	 * 获取数据库列表
	 * @author gentle
	 */
	public function actionGetDbs() {
		$return['code'] = 0;
		$return['data'] = CardDb::model()->getList();
		echo CJSON::encode($return);
	}

	/**
	 * 获取数据表列表
	 * @author gentle
	 */
	public function actionGetTables($databaseId = 0, $enName = '') {
		$return['code'] = 0;
		$selectField = array('id', 'database_id', 'name', 'en_name', 'listorder', 'request_times', 'last_uid', 'update_time');
		$return['data'] = CardDs::model()->getList($databaseId, $enName, $selectField);
		echo CJSON::encode($return);
	}

	/**
	 * 获取字段列表
	 * @author gentle
	 */
	public function actionGetFields($datasetId = 0, $enName = '') {
	    $return['code'] = 0;
	    $return['data'] = CardDs::model()->getFieldList($datasetId, $enName);
	    echo CJSON::encode($return);
	}

	/**
	 * 获取内容列表
	 * @author gentle
	 */
	public function actionGetItems($datasetId, $currPage = 1, $pageSize = 10) {
	    $return['code'] = 0;
	    $return['data'] = CardItem::model()->getList($datasetId, $currPage, $pageSize);
	    echo CJSON::encode($return);
	}
}
