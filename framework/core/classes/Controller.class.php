<?php
/**
 * AwayAPI 默认控制器类
 *
 * @copyright  Copyright (c) 2013-2014 Awaysoft.Com (http://www.awaysoft.com)
 * @license    http://www.apache.org/licenses/LICENSE-2.0   Apache License, Version 2.0
 * @version    Version 0.1.0
 * @link       http://AwayAPI.awaysoft.com
 * @since      File available since Release 0.1.0
 * @author     Tom <tom@awaysoft.com>
 */

class Controller {

    public function __construct () {
        tag('appControllerLoad');
    }

    /*
     * @param String $key 要查询的键
     * @param String|null $filterFunction 数据处理函数，值为null则不处理
     * @param String $defaultValue 默认结果
     * @param Array $array 处理的数组
     * @return String
     */
    private function _arrayGet($key, $filterFunction, $defaultValue, $array) {
        if (array_key_exists($key, $array)) {
            $value = $array['key'];
            /* 对数据处理 */
            if ($filterFunction !== null) {
                if (function_exists($filterFunction)) {
                    $value = $filterFunction($value);
                } else {
                    /* 函数不存在默认使用addslashes函数处理 */
                    $value = addslashes($value);
                }
            }
        } else {
            $value = $defaultValue;
        }
        return $value;
    }

    /*
     * @description 获取Get方式的信息
     * @param String $key 要查询的键
     * @param String|null $filterFunction 数据处理函数，值为null则不处理
     * @param String $defaultValue 默认结果
     * @return String
     */
    public function get($key, $filterFunction = "addslashes", $defaultValue = "") {
        return $this->_arrayGet($key, $filterFunction, $defaultValue, $_GET);
    }

    /*
     * @description 获取Post方式的信息
     * @param String $key 要查询的键
     * @param String|null $filterFunction 数据处理函数，值为null则不处理
     * @param String $defaultValue 默认结果
     * @return String
     */
    public function post($key, $filterFunction = "addslashes", $defaultValue = "") {
        return $this->_arrayGet($key, $filterFunction, $defaultValue, $_POST);
    }

    /*
     * @description 获取Request方式的信息
     * @param String $key 要查询的键
     * @param String|null $filterFunction 数据处理函数，值为null则不处理
     * @param String $defaultValue 默认结果
     * @return String
     */
    public function request($key, $filterFunction = "addslashes", $defaultValue = "") {
        return $this->_arrayGet($key, $filterFunction, $defaultValue, $_REQUEST);
    }

    /*
     * @description 返回成功的信息
     * @param String $msg 返回的信息
     * @param int $errorNo 返回的状态编号
     * @param mixed $data 附加返回对象
     */
    public function success($msg='操作成功！', $errorNo = 0, $data = '') {
        json_return($data, $errorNo, $msg);
    }

    /*
     * @description 返回失败的信息
     * @param String $msg 返回的信息
     * @param int $errorNo 返回的状态编号
     * @param mixed $data 附加返回对象
     */
    public function error($msg='操作失败！', $errorNo = 10, $data = '') {
        json_return($data, $errorNo, $msg);
    }

    /*
     * @description aJax返回
     * @param mixed $data 需要返回的数据
     * @param int $errorNo 返回的错误状态
     * @param string $errorMsg 返回的错误信息
     */
    public function returnJson($data, $errorNo = 0, $errorMsg = '') {
        json_return($data, $errorNo, $errorMsg);
    }
}