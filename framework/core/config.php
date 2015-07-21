<?php
/**
 * AwayAPI 默认配置文件
 *
 * @copyright  Copyright (c) 2013-2014 Awaysoft.Com (http://www.awaysoft.com)
 * @license    http://www.apache.org/licenses/LICENSE-2.0   Apache License, Version 2.0
 * @version    Version 0.1.0
 * @link       http://AwayAPI.awaysoft.com
 * @since      File available since Release 0.1.0
 * @author     Tom <tom@awaysoft.com>
 */

return array(
    'SESSION' => array(
        'TYPE' => 'system',   /* 可选system, db, cache */
        'COOKIE' => 'AWAYSESSION',  /* session存在cookie中的名称 */
        'EXPIRE' => 180, /* session过期时间 */
        'GCDIVER' => 100, /* 删除过期session的几率1/GCDIVER */
        'DB' => array(         /* 当TYPE为db时有效,建议使用memory类型引擎，表结构为 key varchar(40), value text(65536), last unsigned int(11) */
            'dbType' => 'mysql',  /* 数据库类型 */
            'server' => 'localhost',  /* 数据库地址 */
            'user' => '',  /* 数据库用户名 */
            'pass' => '',  /* 数据库密码 */
            'dbName' => '',  /* 数据库名称 */
            'dbPrefix' => '',  /* 表前缀 */
            'port' => 3306,  /* 数据库端口 */
            'dbFile' => '',  /* 数据库文件，一般用于文件型数据库 */
            'charset' => 'utf8',  /* 数据库编码 */
            'tableName' => ''  /* session所存的表 */
        )
    )
);