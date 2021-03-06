<?php
/**
 * 供线上正式环境使用
 */

Yii::setPathOfAlias('pub','/opt/phplib/components');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'卡牌数据库',
	'defaultController' => 'site',
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
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123456',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('124.207.174.198','::1'),
		),
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
			'connectionString' => 'mongodb://210.65.11.22:27017,203.66.45.95:27017,203.66.45.59:27017/?replicaSet=shard1',
			'dbName' => 'card_db_v2',
			'fsyncFlag' => true,
			'safeFlag' => true,
			'useCursor' => false,
		),

		'cache' => array(
			'class' => 'system.caching.CMemCache',
			'useMemcached' => true,
			'servers' => array(
				'01' => array('host' => '210.61.12.172', 'port' => 11212),
			),
		),
// 	    'redis'=>array(
// 	        'class'=>'system.caching.CRedisCache',     //对应caching/CredisCache.php
// 	        'hostname'=>'127.0.0.1',
// 	        'port'=>6379,
// 	        'database'=>0,
// 	    ),
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
                    'logPath' => '/data/log/www/db.admin.mofang.com',
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
	    //配置引用ueeditor
// 	    'controllerMap'=>array(
// 	        'ueditor'=>array(
// 	            'class'=>'ext.baiduUeditor.UeditorController',
// 	            'config'=>array('initialFrameWidth'=>1000),
// 	            'thumbnail'=>false,//是否开启缩略图
// 	            'watermark'=>'',//水印图片的地址，使用相对路径
// 	            'locate'=>9,//水印位置，1-9，默认为9在右下角
// 	            'savePath'=>'xxx',
// 	            'allowFiles'=>array(
// 	                'image' => array('.gif', '.png', '.jpg', '.jpeg'),
// 	                'file' => array( '.doc' , '.docx' , '.zip' , '.pdf' , '.txt'),
// 	            ),
// 	        ),
// 	    ),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		'cache_expire'	=> 120,			//cache缓存时间
		'filter_operator' => array(
			'=='=>'等于', '!='=>'不等于', '>'=>'大于', '<'=>'小于', 'regex'=>'匹配', 	//简单类型
			'in'=>'包含', 'notin'=>'不包含', 'all'=>'全包含'	//复合类型
		),
		'inlay_tabse'	=>array(
			'database'=>'库定义表',
			'dataset'=>'表定义表',
			'item'=>'全数据表',
			'user'=>'用户表',
		    'template'=>'模板定义表',
		    'log'=>'操作日志表',
		),
		
		//角色与权限
		'role'=>array(
			'10'=>array(
				'name'=>'管理员',
				'actions'=>array('item', 'dbset', 'user', 'log','dump','template'),
			),
			'20'=>array(
				'name'=>'数据员',
				'actions'=>array('item'),
			),
			'30'=>array(
				'name'=>'录入员',
				'actions'=>array('item-add'),
			),
		),
		//权限点
		'action_point'=>array(
			'item'		=> '数据操作',	//拥有旗下所有权限
			'item-add'	=> '数据操作-添加/编辑',
			'item-del'	=> '数据操作-删除',
			'item-import'=> '数据操作-导入',	//导出模板+导入
			'item-export'=> '数据操作-导出',
			'dbset'		=> '结构定义',
			'user'		=> '用户管理',
			'log'		=> '操作日志',
			'dump'		=> '备份恢复',
		    'template'  =>'模板管理',
		),
		//用户默认密码
		'def_password'=> 'mofang888',
	),

);
