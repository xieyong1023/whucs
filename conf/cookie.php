<?php
/**
 * cookie 配置
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/26
 * @Time: 16:39
 * @see http://php.net/manual/en/function.setcookie.php
 */

return [
    'prefix'   => 'whucs_',
    'path'     => '/',
    'domain'   => '',
    'secure'   => false,
    'httponly' => true,
    'expire'   => 605500, // 一周
];
