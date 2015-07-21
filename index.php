<?php
/**
 * 入口文件
 *
 * @copyright  Copyright (c) 2013-2014 Awaysoft.Com (http://www.awaysoft.com)
 * @license    http://www.apache.org/licenses/LICENSE-2.0   Apache License, Version 2.0
 * @version    Version 0.1.0
 * @link       http://awayphp.awaysoft.com
 * @since      File available since Release 0.1.0
 * @author     Tom <tom@awaysoft.com>
 */

/* 设置网站目录 */
define('SITE_PATH', dirname(__FILE__));
/* 设置为调试模式 */
define('RUN_MODE', 1);

require('./framework/load.php');
