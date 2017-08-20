<?php
/**
 * 异常类基类
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/18
 * @Time: 22:27
 */

namespace Library\Core;

use Throwable;

/**
 * Class Exception
 * @package Library\Core
 */
class Exception extends \Exception
{
    /**
     * $_map = [
     *     'EXCEPTION_NAME' => ['code' => 1, 'zh_cn' => '中文名'],
     * ]
     * @var array 字典
     */
    protected $map = [];
    /**
     * @var string 输出语言
     */
    protected static $_language = 'zh_cn';

    /**
     * Exception constructor.
     *
     * @param string         $message msg
     * @param Throwable|null $previous
     *
     * @throws \Exception
     */
    public function __construct($message = "", Throwable $previous = null)
    {
        if (empty($message)) {
            throw new \Exception('exception message is empty');
        }

        if (! isset($this->map[$message])) {
            throw new \Exception('exception type is not set yet : ' . $message);
        }

        if (! isset($this->map[$message]['code']) || ! isset($this->map[$message][self::$_language])) {
            throw new \Exception('exception code or content is not set yet');
        }

        $msg = $this->map[$message][self::$_language];
        $code = $this->map[$message]['code'];

        parent::__construct($msg, $code, $previous);
    }

    /**
     * 设置异常时输出语言
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $language
     */
    public static function setLanguage(string $language)
    {
        self::$_language = $language;
    }
}
