<?php
/**
 * 错误类
 *
 * @author: xieyong <qxieyongp@163.com>
 * @date: 2017/8/7
 * @time: 15:31
 */

namespace Library\Core;

class Error
{
    public static function show_404()
    {
        \Yaf_Dispatcher::getInstance()->disableView();

    }
}
