<?php
/**
 *
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/19
 * @Time: 10:47
 */
define('ROOT_PATH', realpath(dirname(__FILE__) . '/../'));
define('NAMESPACE_ROOT', ROOT_PATH . '/application/library'); // 命名空间根目录为application/library
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