<?php
/**
 * define 宏定义
 *
 * @author: xieyong <qxieyongp@163.com>
 * @date: 2017/8/7
 * @time: 19:25
 */

// 当前时间
define('NOW_TIME', time());

// 时间戳转换格式
define('DATE_FMT', 'Y-m-d H:i:s');

// 当前日期 + 时间
define('DATE_TIME_STRING', date(DATE_FMT, NOW_TIME));

// 当前日期
define('DATE_STRING', date('Y-m-d', NOW_TIME));

// 当前时间
define('TIME_STRING', date('H:i:s', NOW_TIME));
