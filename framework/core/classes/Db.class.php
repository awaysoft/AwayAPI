<?php
/**
 * AwayAPI 默认数据库类
 *
 * @copyright  Copyright (c) 2013-2014 Awaysoft.Com (http://www.awaysoft.com)
 * @license    http://www.apache.org/licenses/LICENSE-2.0   Apache License, Version 2.0
 * @version    Version 0.1.0
 * @link       http://AwayAPI.awaysoft.com
 * @since      File available since Release 0.1.0
 * @author     Tom <tom@awaysoft.com>
 */

class Db extends PDO{
    private $config = [
        'dsn' => '',
        'user' => '',
        'pass' => '',
        'driver_options' => []
    ];

    public function __construct($config = null) {
        if (!$config) {
            $config = config('db');
        }
        $this->config = array_merge($this->config, $config);
        try {
            parent::__construct($this->config['dsn'], $this->config['user'], $this->config['pass'], $this->config['driver_options']);
        } catch (Exception $e) {
            trigger_error('数据库连接失败：' . $e->getMessage(), E_USER_ERROR);
        }
    }
}