<?php
/**
 * 安全函数，输入过滤
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/28
 * @Time: 12:19
 */

namespace Library\Tools;

/**
 * Class Secure
 * @package Library\Tools
 */
class Secure
{
    /**
     * 生成防止CSRF攻击的token
     * @author: xieyong <qxieyongp@163.com>
     * @param string $name 区分标识符
     *
     * @return string
     */
    public static function createCSRFToken(string $name)
    {
        // 生成随机字符串
        $token = StringHelper::getRandomString(16);

        // 存入session用于验证
        Session::getInstance()->set(CSRF_TOKEN_PREFIX . $name, $token);

        return $token;
    }

    /**
     * 校验防止CSRF攻击的token
     * @author: xieyong <qxieyongp@163.com>
     * @param string $name
     * @param string $token
     *
     * @return bool
     */
    public static function checkCSRFToken(string $name, string $token)
    {
        $session = Session::getInstance();

        $session_csrf_token = $session->get(CSRF_TOKEN_PREFIX . $name);

        if (empty($session_csrf_token)) {
            return false;
        }

        if ($session_csrf_token === $token) {
            // 验证通过必须删除该token
            $session->delete(CSRF_TOKEN_PREFIX . $name);
            return true;
        }

        return false;
    }
}
