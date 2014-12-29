<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
Yii::setPathOfAlias('pub','/opt/phplib/components');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'卡牌数据库',
	'defaultController' => 'cardDb',
	'theme' => 'abound',
	'language'=>'zh_cn',		//启用中文语言包

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',

		'ext.YiiMongoDbSuite.*',
		'ext.FancyBox.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		/*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123456',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('124.207.174.198','::1'),
		),*/
		'api',
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>false,
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),
        //'assetManager'=>array(
        //    'class'=>'ext.qiniuasset',
        //),
		// uncomment the following to use a MySQL database
		/*
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=testdrive',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),
		*/
		'mongodb'=>array(
			'class'=>'EMongoDB',
			'connectionString' => 'mongodb://10.6.12.25:27017,10.6.13.121:27017,10.6.31.174:27017/?replicaSet=shard1',
			'dbName' => 'card_db_v2',
			'fsyncFlag' => true,
			'safeFlag' => true,
			'useCursor' => false,
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
                    'logPath' => '/data/log/www/db.mofang.com',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
		'curl' => array(
			'class' => 'pub.MCurl',
		),
		'mcss' => array(
			'class' => 'pub.MMcss',
			'from' => 'mga',
			'timeout'=>600,
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		//'adminEmail'=>'webmaster@example.com',
		//'userInfoApi'=>'http://u.mofang.com/api/get_detail_by_user',
	),
);
