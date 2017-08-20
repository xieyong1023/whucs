<?php
/**
 * 入口文件
 *
 * @author: xieyong <qxieyongp@163.com>
 * @date: 2017/8/2
 * @time: 12:49
 *
 * @see Yaf框架 http://www.laruence.com/manual/index.html
 */

define('DEBUG', true);

// 定义执行环境
define('ENV', 'dev');

// CLI命令
if (defined('STDIN')) {
    chdir(dirname(__FILE__));
}

// 定义目录
define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
define('APP_PATH', ROOT_PATH . '/application');
define('CONFIG_PATH', ROOT_PATH . '/conf');
define('LOG_PATH', ROOT_PATH . '/logs');
define('LIB_PATH', APP_PATH . '/library');
define('VIEW_PATH', APP_PATH . '/views');

// 定义时区
date_default_timezone_set("Asia/Shanghai");

// 其他宏定义
require CONFIG_PATH . '/define.php';

// 检测php版本
if (phpversion() < 7) {
    echo 'php version should higher than 7';
}

// 检查php扩展
$need_extentions = ['yaf', 'PDO', 'pdo_mysql', 'json', 'mbstring'];
$php_extentions = get_loaded_extensions();
foreach ($need_extentions as $ext) {
    if (! in_array($ext, $php_extentions)) {
        echo 'need extension: ' . $ext;
        exit(-1);
    }
}

// psr-4 autoloader
define('NAMESPACE_ROOT', LIB_PATH); // 命名空间根目录为application/library
spl_autoload_register(function ($class) {
    // 去除Library前缀
    if (0 !== preg_match("#^Library\\\\(.*)#", $class, $matches)) {
        $path = $matches[1];
        $path_array = explode('\\', $path);
        $file_path = NAMESPACE_ROOT . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $path_array) . '.php';
        if (file_exists($file_path)) {
            include $file_path;
        }
    }
});

$app = new Yaf_Application(ROOT_PATH . "/conf/application.ini", ENV);
$app->bootstrap()->run();
