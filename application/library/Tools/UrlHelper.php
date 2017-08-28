<?php
/**
 * URL函数
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/28
 * @Time: 13:32
 */

namespace Library\Tools;

/**
 * Class UrlHelper
 * @package Library\Tools
 */
class UrlHelper
{
    /**
     * 拼接url，前端链接
     * @example siteUrl('/home/url') // http://www.hostname.com/home/url
     * @author: xieyong <qxieyongp@163.com>
     * @param string $uri uri
     *
     * @return string
     */
    public static function siteUrl(string $uri = '')
    {
        return BASE_URL . $uri;
    }

    /**
     * 拼接url，管理端链接
     * @example adminUrl('/home/url') // http://www.hostname.com/admin/home/url
     * @author: xieyong <qxieyongp@163.com>
     * @param string $uri
     *
     * @return string
     */
    public static function adminUrl(string $uri = '')
    {
        return ADMIN_URL . $uri;
    }
}
