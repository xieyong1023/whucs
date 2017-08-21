<?php
/**
 * 日志记录类
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/19
 * @Time: 14:11
 */

namespace Library\Log;

use Library\Exception\LogException;

/**
 * Class Logger
 * @package Library\Log
 */
class Logger
{
    // 异常等级
    const DEBUG     = 1;
    const INFO      = 2;
    const NOTICE    = 3;
    const WARNING   = 4;
    const ERROR     = 5;
    const CRITICAL  = 6;
    const ALERT     = 7;
    const EMERGENCY = 8;
    /**
     * @var array 异常等级
     */
    protected $level = [
        self::DEBUG     => 'debug',
        self::INFO      => 'info',
        self::NOTICE    => 'notice',
        self::WARNING   => 'warning',
        self::ERROR     => 'error',
        self::CRITICAL  => 'critical',
        self::ALERT     => 'alert',
        self::EMERGENCY => 'emergency',
    ];
    /**
     * @var string 日志名
     */
    protected $log_name = '';
    /**
     * @var array 缓存
     */
    protected $cache = [];
    /**
     * @var int 缓存域值
     */
    protected $delay_threshold = 200;
    /**
     * @var int 缓存行数
     */
    protected $cache_count = 0;
    /**
     * @var string 阈值
     */
    protected $threshold = self::DEBUG;
    /**
     * @var LogWriter 写日志类
     */
    protected $writer = null;
    /**
     * @var bool 是否延迟写入
     */
    protected $delay_write = true;
    /**
     * @var string 分隔符
     */
    protected $separator = ' ';

    /**
     * Logger constructor.
     *
     * @param string    $log_name
     * @param LogWriter $writer
     */
    public function __construct(string $log_name, LogWriter $writer)
    {
        $this->log_name = $log_name . '_' . DATE_STRING;
        $this->writer = $writer;
    }

    /**
     * 记录日志
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param int   $level 异常等级
     * @param array ...$msg
     */
    protected function log(int $level, ...$msg)
    {
        // 只记录超过阈值的异常
        if ($level <= $this->threshold) {
            return;
        }

        $log_line = $this->format_line($level, $msg);

        // 延迟写入时，日记先保存到cache中，程序结束时执行写操作
        if ($this->delay_write) {
            array_push($this->cache, $log_line);
            $this->cache_count++;

            // 缓存达到域值立即写入
            if ($this->cache_count == $this->delay_threshold) {
                $this->write($this->cache);
                $this->clearCache();
            }
        } else {
            $this->write([$log_line]);
        }
    }

    /**
     * 格式化一行日志
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param int   $level 异常等级
     * @param array ...$msg 异常信息 可为string or array
     *
     * @return string
     */
    protected function format_line(int $level, ...$msg)
    {
        $datatime = '[' . DATE_TIME_STRING . ']';

        $msg = implode($this->separator, $msg);

        return implode($this->separator, [$datatime, $this->level[$level], $msg]) . PHP_EOL;
    }

    /**
     * 保存日志
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param array $content 写入内容
     */
    protected function write($content = [])
    {
        if (! empty($content)) {
            $this->writer->write($this->log_name, $content);
        }
    }

    /**
     * 清楚缓存
     * @author: xieyong <qxieyongp@163.com>
     */
    private function clearCache()
    {
        $this->cache = [];
        $this->cache_count = 0;
    }

    /**
     * 对象解构时写日志
     */
    function __destruct()
    {
        $this->write($this->cache);
    }

    /**
     * 设置日志记录阈值
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param int $threshold 阈值
     *
     * @throws LogException
     */
    public function setThreshold(int $threshold)
    {
        if (! array_key_exists($threshold, $this->level)) {
            throw new LogException('INVALID_EXCEPTION_LEVEL');
        }

        $this->threshold = $threshold;
    }

    /**
     * 设置是否延迟写日志
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param bool $delay_write true-延迟写入 false-不延迟写入
     */
    public function setDelayWrite(bool $delay_write)
    {
        $this->delay_write = $delay_write;
    }

    /**
     * Debug
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param $msg
     */
    public function debug(...$msg)
    {
        $this->log(self::DEBUG, $msg);
    }

    /**
     * Info
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param $msg
     */
    public function info(...$msg)
    {
        $this->log(self::INFO, $msg);
    }

    /**
     * Notice
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param $msg
     */
    public function notice(...$msg)
    {
        $this->log(self::NOTICE, $msg);
    }

    /**
     * Warning
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param $msg
     */
    public function warning(...$msg)
    {
        $this->log(self::WARNING, $msg);
    }

    /**
     * Error
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param $msg
     */
    public function error(...$msg)
    {
        $this->log(self::ERROR, $msg);
    }

    /**
     * Critical
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param $msg
     */
    public function critical(...$msg)
    {
        $this->log(self::CRITICAL, $msg);
    }

    /**
     * Alert
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param $msg
     */
    public function alert(...$msg)
    {
        $this->log(self::ALERT, $msg);
    }

    /**
     * Emergency
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param $msg
     */
    public function emergency(...$msg)
    {
        $this->log(self::EMERGENCY, $msg);
    }
}
