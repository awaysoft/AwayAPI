<?php
/**
 * AwayAPI 框架常量初始化文件，初始化一系列常量等
 *
 * @copyright  Copyright (c) 2013-2015 Awaysoft.Com (http://www.awaysoft.com)
 * @license    http://www.apache.org/licenses/LICENSE-2.0   Apache License, Version 2.0
 * @version    Version 0.1.0
 * @link       http://AwayAPI.awaysoft.com
 * @since      File available since Release 0.1.0
 * @author     Tom <tom@awaysoft.com>
 */

/* 记录脚本开始时间和内存占用 */
$GLOBAL['startTime'] = microtime(true);
$GLOBALS['startMemory'] = memory_get_usage();

/* 定义一系列的常量 */
define('AWAY_VERSION', '0.1.0');
defined('AWAY_PATH') or define('AWAY_PATH', dirname(__FILE__));
defined('SITE_PATH') or define('SITE_PATH', dirname(AWAY_PATH));
defined('RUNTIME_PATH') or define('RUNTIME_PATH', SITE_PATH . '/runtime');
defined('LOG_PATH') or define('LOG_PATH', RUNTIME_PATH . '/logs');
defined('RUN_MODE') or define('RUN_MODE', 0); /* 默认发布模式 */

if (!file_exists(RUNTIME_PATH)) {
    mkdir(RUNTIME_PATH);
}

if (!file_exists(LOG_PATH)) {
    mkdir(LOG_PATH);
}


/* 载入框架运行文件 */
require(AWAY_PATH . '/core/run.php');