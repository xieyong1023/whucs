<?php
/**
 * 日志记录类
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/19
 * @Time: 14:11
 */

namespace Library\Log;

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
     * @var array 设置
     */
    protected $option = [
        'max_buffer_size' => 200,          // 缓冲区最大值
        'log_threshold'   => self::DEBUG,  // 阈值
        'delay_write'     => true,         // 是否延迟写入
        'seperator'       => "\x1e",       // 记录分隔符
    ];
    /**
     * @var string 日志名
     */
    protected $log_name = '';
    /**
     * @var array 缓存
     */
    protected $buffer = [];
    /**
     * @var int 缓存行数
     */
    protected $buffer_length = 0;
    /**
     * @var LogWriter 写日志类
     */
    protected $writer = null;

    /**
     * Logger constructor.
     *
     * @param string    $log_name 日志名
     * @param LogWriter $writer 执行写操作的对象
     * @param array     $option 设置
     */
    public function __construct(string $log_name, LogWriter $writer, array $option = [])
    {
        $this->log_name = $log_name . '_' . DATE_STRING;
        $this->writer = $writer;

        if (! empty($option)) {
            $this->option = array_merge($this->option, $option);
        }
    }

    /**
     * 记录日志
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param int   $level 异常等级
     * @param array $msg 错误消息 [msg1, msg2, ..., msgn] 按顺序组合
     */
    protected function log(int $level, array $msg)
    {
        // 过滤不超过阈值的异常
        if ($level < $this->option['log_threshold']) {
            return;
        }

        $log_line = $this->format_line($level, $msg);

        // 延迟写入时，日记先保存到buffer中，程序结束时执行写操作
        if ($this->option['delay_write']) {
            array_push($this->buffer, $log_line);
            $this->buffer_length++;

            // 缓存达到域值立即写入
            if ($this->buffer_length == $this->option['max_buffer_size']) {
                $this->write($this->buffer);
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
     * @param array $msg 异常信息 [msg1, msg2, ..., msgn] 按顺序组合
     *
     * @return string
     */
    protected function format_line(int $level, array $msg)
    {
        $datatime = '[' . date(DATE_FMT, time()) . ']';

        $msg = implode($this->option['seperator'], $msg);

        return implode($this->option['seperator'], [$datatime, $this->level[$level], $msg]) . PHP_EOL;
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
        $this->buffer = [];
        $this->buffer_length = 0;
    }

    /**
     * 对象解构时写日志
     */
    function __destruct()
    {
        $this->write($this->buffer);
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

    /**
     * 记录异常信息
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param \Exception $e
     */
    public function logException(\Exception $e)
    {
        $msg = [
            'code:' . $e->getCode(),
            'msg:' . $e->getMessage(),
            'file:' . $e->getFile(),
            'line:' . $e->getLine(),
            'trace:' . $e->getTraceAsString(),
        ];

        $this->log(self::ERROR, $msg);
    }
}
