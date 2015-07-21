<?php
/**
 * AwayAPI 框架日志类
 *
 * @copyright  Copyright (c) 2013-2014 Awaysoft.Com (http://www.awaysoft.com)
 * @license    http://www.apache.org/licenses/LICENSE-2.0   Apache License, Version 2.0
 * @version    Version 0.1.0
 * @link       http://AwayAPI.awaysoft.com
 * @since      File available since Release 0.1.0
 */

class Log {
    static private $fileHandle = null;

    /* 写日志 */
    static public function write($log) {
        if (self::$fileHandle == null) {
            self::$fileHandle = fopen(LOG_PATH . '/' . date('Y-m-d'), 'ab');
        }
        fwrite(self::$fileHandle, '[' . date('Y-m-d H:i:s') . ']' . $log . "\n");
    }

    /* 保存日志 */
    static public function save() {
        if (self::$fileHandle == null) {
            fclose(self::$fileHandle);
            self::$fileHandle = null;
        }
    }
}