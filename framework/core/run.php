<?php
/**
 * AwayAPI 运行文件，执行框架初始化，运行等
 *
 * @copyright  Copyright (c) 2013-2014 Awaysoft.Com (http://www.awaysoft.com)
 * @license    http://www.apache.org/licenses/LICENSE-2.0   Apache License, Version 2.0
 * @version    Version 0.1.0
 * @link       http://AwayAPI.awaysoft.com
 * @since      File available since Release 0.1.0
 * @author     Tom <tom@awaysoft.com>
 */

function awayErrorHandle($errno, $errstr, $errfile, $errline, $errarg) {
    /* 调试模式启用 */
    if (RUN_MODE === 1) {
        $log = '[' . $errno . ']' . $errstr . ':' . $errfile . ':' . $errline;
        Log::write($log);
        if ($errno === E_ERROR || $errno === E_USER_ERROR) {
            $debugBacktrace = debug_backtrace();
            array_shift($debugBacktrace);
            Log::save();
            json_return($debugBacktrace, 1, '发生严重错误');
        }
    }

    /* 继续交由error_reporting()处理 */
    return false;
}

/* 检测PHP版本 */
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    die('框架需要PHP的版本大于5.4.0！');
}

/* 载入框架默认函数集 */
require(AWAY_PATH . '/core/functions.php');

/* 设置错误跟踪函数 */
set_error_handler("awayErrorHandle", E_ALL);

/* 载入框架默认配置文件 */
$config = require(AWAY_PATH . '/core/config.php');
config('', $config, 'merge');

/* 载入框架文件 */
$classes = array('App','Controller','Db','Log','Model','Plugin');
foreach ($classes as $class) {
    require(AWAY_PATH . '/core/classes/' . $class . '.class.php');
}

/* 载入用户的配置文件 */
if (file_exists(SITE_PATH . '/conf/conf.php')) {
    config('', require(SITE_PATH . '/conf/conf.php'), 'merge');
}

/* 载入用户默认函数集 */
if (file_exists(SITE_PATH . '/common/functions.php')) {
    require(SITE_PATH . '/common/functions.php');
}

/**
 * 载入模型
 * 只载入后缀是Model.class.php的文件
 */
if (file_exists(SITE_PATH . '/model')) {
    $dir = dir(SITE_PATH . '/model');
    while (($modelName = $dir -> read()) !== false) {
        /* 检查文件的后缀是否是Plugin.class.php */
        if (is_file(SITE_PATH . '/model/' . $modelName) && (substr($modelName, -15) === 'Model.class.php')){
            require_once (SITE_PATH . '/model/' . $modelName);
        }
    }
}

/**
 * 载入插件
 * 只载入后缀是Plugin.class.php的文件
 */
if (file_exists(SITE_PATH . '/plugins')) {
    $dir = dir(SITE_PATH . '/plugins');
    while (($pluginName = $dir -> read()) !== false) {
        /* 检查文件的后缀是否是Plugin.class.php */
        if (is_file(SITE_PATH . '/plugins/' . $pluginName) && (substr($pluginName, -16) === 'Plugin.class.php')){
            loadPlugin(SITE_PATH . '/plugins/' . $pluginName);
        }
    }
}

/**
 * 载入控制器
 */

$c = 'Index';
if (isset($_GET['c']) && is_string($_GET['c'])) {
    /* 过滤../之类跨目录路径 */
    $c = ucfirst(basename($_GET['c']));
}
$controllerClassName = $c . 'Controller';
$controllerFilename = SITE_PATH . '/controller/' . $c . 'Controller.class.php';

if (!file_exists($controllerFilename)) {
    trigger_error('控制器：' . APP_NAME . '不存在！', E_USER_ERROR);
}
require $controllerFilename;

/**
 * 载入控制器方法
 */
$methodName = 'index';
if (isset($_GET['a']) && is_string($_GET['a'])) {
    $methodName = $_GET['a'];
}
$controller = new $controllerClassName();

if (method_exists($controller, $methodName)) {
    $controller->$methodName();
} else if (method_exists($controller, '_empty')) {
    $controller->_empty();
} else {
    trigger_error('控制器:' . CONTROLLER_NAME . '不存在方法：' . $methodName, E_USER_ERROR);
}
