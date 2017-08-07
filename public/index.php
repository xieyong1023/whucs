<?php
/**
 * 入口文件
 *
 * @author: xieyong <xieyong1023@qq.com>
 * @date: 2017/8/2
 * @time: 12:49
 *
 * @see Yaf框架 http://www.laruence.com/manual/index.html
 */

define('DEBUG', true);

define('ENV', 'dev');

// 定义目录
define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
define('APP_PATH', ROOT_PATH . '/application');
define('CONFIG_PATH', ROOT_PATH . '/conf');
define('LOG_PATH', ROOT_PATH . '/logs');
define('LIB_PATH', APP_PATH . '/library');

// 定义时区
date_default_timezone_set("Asia/Shanghai");

// 其他宏定义
require CONFIG_PATH . '/define.php';

$app = new Yaf_Application(ROOT_PATH . "/conf/application.ini", ENV);
$app->bootstrap()->run();
