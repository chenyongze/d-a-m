<?php

// change the following paths if necessary
$yii='/opt/phplib/yii/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
//define('YII_ENABLE_ERROR_HANDLER', false);
//error_reporting(E_ALL);
//error_reporting(E_ALL & ~E_NOTICE);
//ini_set('display_errors', 1);
mb_internal_encoding('UTF-8');

//include(dirname(__FILE__)."/protected/function.php");
require_once($yii);
Yii::createWebApplication($config)->run();
