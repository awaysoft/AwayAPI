<?php
/**
 * AwayAPI 默认模型类
 *
 * @copyright  Copyright (c) 2013-2014 Awaysoft.Com (http://www.awaysoft.com)
 * @license    http://www.apache.org/licenses/LICENSE-2.0   Apache License, Version 2.0
 * @version    Version 0.1.0
 * @link       http://AwayAPI.awaysoft.com
 * @since      File available since Release 0.1.0
 * @author     Tom <tom@awaysoft.com>
 */

class Model extends Db{

    private $tableName = '';

    public function __construct ($tableName = '', $db = 'DB') {
        $config = null;
        if (is_string($db)) {
            $config = config($db);
        }
        if (is_array($db)) {
            $config = $db;
        }
        if ($config === null) {
            trigger_error('找不到数据库配置，请检查配置文件！', E_USER_ERROR);
        }
        parent::__construct($config);
        $this->tableName = $tableName;
    }
}