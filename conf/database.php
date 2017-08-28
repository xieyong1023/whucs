<?php
/**
 * 数据库配置
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/20
 * @Time: 16:00
 */

return [
    'default' => [
        'database_type' => 'mysql',       // 数据库类型
        'database_name' => 'whucs_exp',   // 数据库名
        'ip'            => '192.168.31.200',  // ip地址
        'port'          => 3306,          // 端口号
        'username'      => 'shey',        // 用户名
        'password'      => 'asdf23*&23dsAdsDWE',      // 密码
        'charset'       => 'utf8',        // 字符集
        'prefix'        => '',            // 表前缀
        'persistent'    => false,         // 是否长连接
    ],
];
