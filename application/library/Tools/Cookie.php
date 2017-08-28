<?php
/**
 * Cookie类
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/26
 * @Time: 16:38
 */

namespace Library\Tools;

use Library\Config\ConfigManager;

/**
 * Class Cookie
 * @package Library\Tools
 */
class Cookie
{
    /**
     * @var array 配置
     */
    protected $option = [];
    /**
     * @var Cookie
     */
    protected static $_instance = null;

    /**
     * Cookie constructor.
     *
     * @param array $option
     */
    private function __construct(array $option = [])
    {
        $this->option = ConfigManager::getInstance()->getConfig('cookie')->toArray();

        if (! empty($option)) {
            $this->option = array_merge($this->option, $option);
        }
    }

    /**
     * 获取实例
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param array $option 可选配置
     *
     * @return Cookie
     */
    public static function getInstance(array $option = [])
    {
        if (! self::$_instance instanceof Cookie) {
            self::$_instance = new self($option);
        }

        return self::$_instance;
    }

    /**
     * 设置cookie
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $name cookie名
     * @param string $value 内容
     */
    public function setCookie(string $name, string $value)
    {
        \setcookie(
            $this->option['prefix'] . $name,
            $value,
            NOW_TIME + $this->option['expire'],
            $this->option['path'],
            $this->option['domain'],
            $this->option['secure'],
            $this->option['httponly']
        );
    }

    /**
     * 获取cookie
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $name cookie名
     *
     * @return bool
     */
    public function getCookie(string $name)
    {
        $cookie_name = $this->option['prefix'] . $name;
        if (isset($_COOKIE[$cookie_name])) {
            return $_COOKIE[$cookie_name];
        } else {
            return false;
        }
    }

    /**
     * 删除cookie
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $name cookie名
     */
    public function deleteCookie(string $name)
    {
        $cookie_name = $this->option['prefix'] . $name;

        \setcookie(
            $cookie_name,
            false,
            1,
            $this->option['path'],
            $this->option['domain'],
            $this->option['secure'],
            $this->option['httponly']
        );
    }

    /**
     * 删除保存session会话id的cookie
     * @author: xieyong <qxieyongp@163.com>
     */
    public function deleteSessionId()
    {
        $cookie_name = session_name();

        \setcookie(
            $cookie_name,
            false,
            1,
            ini_get('session.cookie_path'),
            ini_get('session.cookie_domain'),
            ini_get('session.cookie_secure'),
            ini_get('session.cookie_httponly')
        );
    }
}
