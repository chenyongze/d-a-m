<?php

return CMap::mergeArray(
    require(dirname(__FILE__) . '/production.php'),
    array(
        // application components
        'components'=>array(

            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=>array(
                    array(
                        'class'=>'CFileLogRoute',
                        'levels'=>'error, warning',
                        'logPath'=>'/data/log/www/db.mofang.com/nnhysj',
                    ),
                    // uncomment the following to show log messages on web pages
                    /*array(
                        'class'=>'CWebLogRoute',
                    ),*/
                ),
            ),

			'mongodb'=>array(
				'class'=>'EMongoDB',
				'connectionString' => 'mongodb://127.0.0.1',
				'dbName' => 'card_db_v2',
				'fsyncFlag' => true,
				'safeFlag' => true,
				'useCursor' => false,
			),

            'cache' => array(
                'class' => 'system.caching.CMemCache',
                'useMemcached' => true,
                'servers' => array(
                    '01' => array('host' => '127.0.0.1', 'port' => 11211),
                ),
            ),
        ),

        // application-level parameters that can be accessed
        // using Yii::app()->params['paramName']
        'params'=>array(
			'cache_expire'	=> 120,		//缓存时间
        ),
    )
);
