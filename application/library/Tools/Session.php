<?php
/**
 * Session访问类
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/27
 * @Time: 23:15
 */

namespace Library\Tools;

/**
 * Class Session
 * @package Library\Tools
 */
class Session
{
    /**
     * @var Session
     */
    protected static $_instance = null;

    /**
     * Session constructor.
     */
    private function __construct()
    {
    }

    /**
     * 获取实例
     * @author: xieyong <qxieyongp@163.com>
     * @return Session
     */
    public static function getInstance()
    {
        if (! self::$_instance instanceof self) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 设置变量
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $name 变量名
     * @param        $value
     */
    public function set(string $name, $value)
    {
        session_start();

        $_SESSION[$name] = $value;

        // php默认用文件存session，修改后提交以解除session文件锁
        session_commit();
    }

    /**
     * 获取变量
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $name 变量名
     *
     * @return mixed
     */
    public function get(string $name)
    {
        if (PHP_SESSION_ACTIVE !== session_status()) {
            session_start();
        }

        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        } else {
            return null;
        }
    }

    /**
     * 删除变量
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $name 变量名
     */
    public function delete(string $name)
    {
        if (PHP_SESSION_ACTIVE !== session_status()) {
            session_start();
        }

        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);

            session_commit();
        }
    }

    /**
     * 销毁session
     * @author: xieyong <qxieyongp@163.com>
     */
    public function destroy()
    {
        if (PHP_SESSION_ACTIVE !== session_status()) {
            session_start();
        }

        session_destroy();
        Cookie::getInstance()->deleteSessionId();
    }
}
