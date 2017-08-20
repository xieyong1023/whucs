<?php
/**
 * 日志适配器
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/19
 * @Time: 14:34
 */

namespace Library\Log;

/**
 * Interface LogWriter
 * @package Library\Log
 */
interface LogWriter
{
    public function write(string $log_name, array $content);
}
