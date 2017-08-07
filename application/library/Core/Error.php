<?php
/**
 * 错误类
 *
 * @author: xieyong <xieyong1023@qq.com>
 * @date: 2017/8/7
 * @time: 15:31
 */

namespace Library\Core;

class Error
{
    public static function show_404()
    {
        header('HTTP/1.1 404 Not Found');
    }
}
