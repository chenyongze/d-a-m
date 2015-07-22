<?php
//xhprof
// include_once './xhprof.php';
// remove the following lines when in production mode
define('YII_DEBUG', 1);
// specify how many levels of call stack should be shown in each log message
define('YII_TRACE_LEVEL', 3);
// change the following paths if necessary
$yii = '/opt/phplib/yii/yii.php';
$configDir = __DIR__ . '/protected/config/';
$config = $configDir . 'production.php';
define("HTTP_REQUEST",		'REQUEST');
define("HTTP_POST",			'POST');
define("HTTP_GET",			'GET');
define("FILTER_STRING",		'FILTER_STRING');
define("FILTER_NUMBER",		'FILTER_NUMBER');
define("FILTER_FLOAT",		'FILTER_FLOAT');

defined('ENVIRONMENT') || define('ENVIRONMENT', isset($_SERVER['ENVIRONMENT']) ? $_SERVER['ENVIRONMENT'] : 'production');
switch (ENVIRONMENT) {
    case 'development' :
        define('YII_ENABLE_ERROR_HANDLER',false);
        define('YII_ENABLE_EXCEPTION_HANDLER',false);
        error_reporting(E_ALL & ~E_NOTICE);
        ini_set('display_errors', 'off');

        $tryConfig = $configDir . ENVIRONMENT . '.php';
        file_exists($tryConfig) && $config = $tryConfig;
        unset($tryConfig);
        break;

    case 'testing' :
        define('YII_ENABLE_ERROR_HANDLER',false);
        define('YII_ENABLE_EXCEPTION_HANDLER',false);
        define('ENABLE_DEBUGLOG',true);//log日志
        define('ENABLE_DEBUGMONGODATA',true);//入库数组debug ==>/EMongoDocument[class]
        error_reporting(E_ALL & ~E_NOTICE);
        ini_set('display_errors', 'on');
        
        $tryConfig = $configDir . ENVIRONMENT . '.php';
        file_exists($tryConfig) && $config = $tryConfig;
        unset($tryConfig);
    case 'production' :
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
        ini_set('display_errors', 'off');
        break;

    default :
        exit();
}
require_once($yii);
Yii::createWebApplication($config)->run();
