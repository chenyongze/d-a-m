<?php

// remove the following lines when in production mode
define('YII_DEBUG', 1);

// specify how many levels of call stack should be shown in each log message
define('YII_TRACE_LEVEL', 3);

// change the following paths if necessary
$yii = '/opt/phplib/yii/yii.php';
$configDir = __DIR__ . '/protected/config/';
$config = $configDir . 'production.php';

defined('ENVIRONMENT') || define('ENVIRONMENT', isset($_SERVER['ENVIRONMENT']) ? $_SERVER['ENVIRONMENT'] : 'production');

switch (ENVIRONMENT) {
    case 'development' :
        define('YII_ENABLE_ERROR_HANDLER',false);
        define('YII_ENABLE_EXCEPTION_HANDLER',false);
        error_reporting(E_ALL & ~E_NOTICE);
        ini_set('display_errors', 'on');

        $tryConfig = $configDir . ENVIRONMENT . '.php';
        file_exists($tryConfig) && $config = $tryConfig;
        unset($tryConfig);
        break;

    case 'testing' :
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
