<?php
/**
 * 日志工厂类
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/19
 * @Time: 21:43
 */

namespace Library\Log;

use Library\Log\Adapter\FileWriter;

/**
 * Class LoggerManager
 * @package Library\Log
 */
class LoggerProvider
{
    /**
     * 获取日志记录对象
     * @author: xieyong <qxieyongp@163.com>
     * @param string $log_name 日志名
     *
     * @return \Closure
     */
    public static function getLogger(string $log_name)
    {
        $logger_closure =  function ($option = []) use ($log_name) {
            $writer = new FileWriter(LOG_PATH);

            return new Logger($log_name, $writer, $option);
        };

        return $logger_closure;
    }
}
