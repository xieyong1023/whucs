<?php
/**
 * 写文件类
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/19
 * @Time: 14:38
 */

namespace Library\Log\Adapter;

use Library\Log\LogWriter;

/**
 * Class FileWriter
 * @package Library\Log\Adapter
 */
class FileWriter implements LogWriter
{
    /**
     * @var string 文件路径
     */
    protected $dir = '';
    /**
     * @var int 日志文件权限
     */
    protected $file_permissions = 0644;

    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    /**
     * 写到文件
     * @author: xieyong <qxieyongp@163.com>
     * @param string $log_name 日志名（文件名）
     * @param array  $content 内容
     */
    public function write(string $log_name, array $content)
    {
        $dir = rtrim($this->dir, DIRECTORY_SEPARATOR);

        if (! file_exists($dir)) {
            mkdir($dir, $this->file_permissions);
        }

        // 日志按日期归类
        $sub_dir = $dir . DIRECTORY_SEPARATOR . date('Y_m_d', NOW_TIME);

        if (! file_exists($sub_dir)) {
            mkdir($sub_dir, $this->file_permissions);
        }

        // 写日志
        $file = $sub_dir . DIRECTORY_SEPARATOR . $log_name;
        file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
    }
}
