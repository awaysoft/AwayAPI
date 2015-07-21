<?php
/**
 * AwayAPI 框架公共函数库
 *
 * @copyright  Copyright (c) 2013-2014 Awaysoft.Com (http://www.awaysoft.com)
 * @license    http://www.apache.org/licenses/LICENSE-2.0   Apache License, Version 2.0
 * @version    Version 0.1.0
 * @link       http://AwayAPI.awaysoft.com
 * @since      File available since Release 0.1.0
 */

static $pluginList = [];
static $tags = [];

/* 将数组中所有元素去除引号 */
function stripSlashesArray($array) {
    $result = array();
    $rvalue = null;
    foreach ($array as $key => $value) {
        /* 去除值的引号 */
        if (is_array($value)) {
            $rvalue = stripSlashesArray($value);
        } else {
            $rvalue = stripslashes($value);
        }
        /* 去除键的引号 */
        if (is_numeric($key)) {
            $result[$key] = $rvalue;
        } else {
            $result[stripslashes($key)] = $rvalue;
        }
    }
    return $result;
}

/*
 * 载入插件
 * @param string $pluginFilename 插件文件绝对路径
 * @return boolean true载入成功，false载入失败
 */
function loadPlugin($pluginFilename) {
    global $tags, $pluginList;
    require_once ($pluginFilename);
    $pluginName = basename($pluginFilename);
    $pluginName = substr($pluginName, 0, -10);
    $plugin = new $pluginName();
    $pluginList[$pluginName] = $plugin;

    $tagList = $plugin->getInterfaces();
    foreach ($tagList as $tagName => $funName) {
        if (!isset($tags[$tagName])) {
            $tags[$tagName] = [];
        }
        array_push($tags[$tagName], [$pluginName, $funName]);
    }
    return true;
}

/*
 * 设置插件插入点
 * @param string $tagName 插入点名称
 * @param mixed $userObject 插入点可选择操作的参数
 */
function tag($tagName, &$userObject = null) {
    global $tags, $pluginList;
    $tagList = $tags[$tagName];
    if (!$tagList) {
        return ;
    }
    foreach ($tagList as $tag) {
        $pluginName = $tag[0];
        $funName = $tag[1];
        $plugin = $pluginList[$pluginName];
        $plugin->$funName($userObject);
    }
}

/*
 * 读取或者写入配置
 * @param string $key 配置名称
 * @param mixed $value 配置的值，留空表示读取配置
 * @param string $operation 操作类型，可能值:default,merge,delete,destroy
 * @return mixed 读取配置返回结果，设置配置无返回
 */
function config($name, $value = '', $operation = '') {
    static $config = array();
    switch ($operation) {
        case 'merge':
            if (is_array($value)) {
                $config = array_merge($config, $value);
                return true;
            }
            break;
        case 'delete':
            if (is_string($name) && $name != '') {
                unset($config[$name]);
                return true;
            }
            break;
        case 'destroy':
            $config = array();
            return true;
    }
    if ($value === '') {
        if (array_key_exists($name, $config)) {
            return $config[$name];
        } else {
            return null;
        }
    } else {
        $config[$name] = $value;
    }
}

/*
 * 读取或者写入session
 * @param string $key 配置名称
 * @param mixed $value 配置的值，留空表示读取配置
 * @param string $operation 操作类型，可能值:default,delete,destroy
 * @return mixed 读取配置返回结果，设置配置无返回
 */
function session($name, $value = '', $operation = '') {
    static $config = null;  /* session设置 */
    static $type = null;    /* session类型 */
    static $db = null;      /* session数据库对象 */
    static $session_id = '';  /* session数据的id */
    static $session_object = array();  /* session数据对象 */
    static $initialized = false;  /* session是否初始化 */

    /* 生成随机的session id */
    function create_session_id() {
        $t = microtime();
        $str = $_SERVER['REMOTE_ADDR'] . $t . mt_srand();
        return md5($str);
    }

    /* 读取配置文件 */
    if ($config === null) {
        $config = config('SESSION');
        $type = $config['TYPE'];
    }

    switch ($type) {
        /* 使用系统的session */
        case 'system':
            /* 初始化 */
            if (!$initialized) {
                session_name($config['COOKIE']);
                session_cache_expire($config['EXPIRE']);
                session_start();
                $initialized = true;
            }
            switch ($operation) {
                /* 销毁session */
                case 'destroy':
                    session_unset();
                    session_destroy();
                    return true;
                /* 删除某个值 */
                case 'delete':
                    if (isset($_SESSION[$name])) {
                        unset($_SESSION[$name]);
                    }
                    return true;
            }
            /* 读取及赋值 */
            if (is_string($name) && $name !== '') {
                if ($value === '') {
                    return $_SESSION[$name];
                } else {
                    $_SESSION[$name] = $value;
                }
            }
            break;
        /* 使用数据库方式存储session */
        case 'db':
            /* 初始化数据库 */
            if ($db === null) {
                $db = new Model($config['DB']['tableName'], $config['DB']);
            }
            /* 根据几率触发session gc */
            if (mt_rand(1, $config['GCDIVER']) === 1) {
                $db->where(array('last' => array('lt', time() - $config['EXPIRE'])))->delete();
            }
            if (!$initialized) {
                $session_id = cookie($config['COOKIE']);
                if (!$session_id) {
                    $session_id = create_session_id();
                    $db->data(array('key' => $session_id, 'value' => '{}', 'last' => time()))->add();
                }
                $object = $db->where(array('key' => $session_id))->find();
                if ($object) {
                    /* 如果发现session超时 */
                    if ($object['last'] + $config['EXPIRE'] < time()) {
                        $session_id = create_session_id();
                        $db->data(array('key' => $session_id, 'value' => '{}', 'last' => time()))->add();
                        $session_object = array();
                    } else {
                        $session_object = json_decode($object['value'], true);
                    }
                }
                cookie($config['COOKIE'], $session_id);
            }
            switch ($operation) {
                case 'destroy':
                    $db->where(array('key'=>$session_id))->delete();
                    $initialized = false;
                    $session_object = array();
                    cookie($config['COOKIE'], '', time() - 100);
                    return true;
                case 'delete':
                    if (isset($session_object[$name])) {
                        unset($session_object[$name]);
                        $db->where(array('key' => $session_id))->data(array('value' => json_encode($session_object),
                                                                               'last' => time()))->update();
                    }
                    return true;
            }
            if (is_string($name) && $name !== '') {
                if ($value === '') {
                    return $session_object[$name];
                } else {
                    $session_object[$name] = $value;
                    $db->where(array('key' => $session_id))->data(array('value' => json_encode($session_object),
                        'last' => time()))->update();
                }
            }
            break;
        case 'cache':
            /*
             * @TODO: 完成cache部分
             */

            break;
    }
}

/*
 * 读取或者写入cookie
 * @param string $key 配置名称
 * @param mixed $value 配置的值，留空表示读取配置
 * @param int $expire
 * @return mixed 读取配置返回结果，设置配置无返回
 */
function cookie($name, $value = '', $expire = 0) {
    if ($value === '') {
        return $_COOKIE[$name];
    } else {
        setcookie($name, $value, $expire);
    }
}


/*
 * Json 返回结果
 */

function json_return($data, $errorNo = 0, $errorMsg = '') {
    echo json_encode([
        'data' => $data,
        'errorno' => $errorNo,
        'errormsg' => $errorMsg
    ]);
}