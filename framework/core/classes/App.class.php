<?php
/**
 * AwayAPI 默认应用类
 *
 * @copyright  Copyright (c) 2013-2014 Awaysoft.Com (http://www.awaysoft.com)
 * @license    http://www.apache.org/licenses/LICENSE-2.0   Apache License, Version 2.0
 * @version    Version 0.1.0
 * @link       http://AwayAPI.awaysoft.com
 * @since      File available since Release 0.1.0
 * @author     Tom <tom@awaysoft.com>
 */

class App {

    private $controller = null;

    public function __construct () {
        if (!file_exists(APP_PATH)) {
            trigger_error('应用：' . APP_NAME . '不存在！', E_USER_ERROR);
        }
    }

    protected function init() {
        /* 载入APP的配置文件 */
        tag('appConfLoad');
        if (file_exists(APP_PATH . '/conf/conf.php')) {
            $config = require(APP_PATH . '/conf/conf.php');
            config('', $config, 'merge');
        }
        tag('appConfLoaded');

        tag('appFunctionsLoad');
        /* 载入APP的函数集 */
        if (file_exists(APP_PATH . '/common/functions.php')) {
            require(APP_PATH . '/common/functions.php');
        }
        tag('appFunctionsLoaded');

        /**
         * 载入APP的插件
         * 只载入后缀是Plugin.class.php的文件
         */
        tag('appPluginsLoad');
        if (file_exists(APP_PATH . '/plugins')) {
            $dir = dir(APP_PATH . '/plugins');
            while (($pluginName = $dir -> read()) !== false) {
                /* 检查文件的后缀是否是Plugin.class.php */
                if (is_file(APP_PATH . '/plugins/' . $pluginName) && (substr($pluginName, -16) === 'Plugin.class.php')){
                    loadPlugin(APP_PATH . '/plugins/' . $pluginName);
                }
            }
        }
        tag('appPluginsLoaded');

        /**
         * 载入路由
         */
        tag('appRoutesLoad');
        if (file_exists(APP_PATH . '/conf/routes.php')) {
            require(APP_PATH . '/conf/routes.php');
        }
        tag('appRoutesLoad');

        defined('CONTROLLER_NAME') or define('CONTROLLER_NAME', 'Index');
        defined('METHOD_NAME') or define('METHOD_NAME', 'index');
    }

    protected function getController () {
        $controllerClassName = ucfirst(CONTROLLER_NAME) . 'Controller';
        $controllerFilename = APP_PATH . '/controller/' . $controllerClassName . '.class.php';
        $methodName = METHOD_NAME;

        if (!file_exists($controllerFilename)) {
            trigger_error('控制器：' . CONTROLLER_NAME . '不存在！', E_USER_ERROR);
        }

        require ($controllerFilename);
        $this->controller = new $controllerClassName();

        if (method_exists($this->controller, $methodName)) {
            $this->controller->$methodName();
        } else if (method_exists($this->controller, '_empty')) {
            $this->controller->_empty();
        } else {
            trigger_error('控制器:' . CONTROLLER_NAME . '不存在方法：' . $methodName, E_USER_ERROR);
        }
    }

    /* 应用运行程序 */
    public function run() {
        tag('appStart');
        $this->init();
        tag('appStarted');

        tag('controllerInstance');
        $this->getController();
        tag('controllerInstanced', $this->controller);
    }
}