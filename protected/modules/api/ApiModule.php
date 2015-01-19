<?php

/**
 * Api模块
 * @author gentle
 */
class ApiModule extends CWebModule {
    
	public function init() {
		$this->setImport(array(
			'api.models.*',
			'api.components.*',
		));
	}

	public function beforeControllerAction($controller, $action) {
		if (parent::beforeControllerAction($controller, $action))
			return true;
		else
			return false;
	}
}
