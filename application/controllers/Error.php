<?php
/**
 * 错误控制器
 *
 * @author: xieyong <xieyong1023@qq.com>
 * @date: 2017/8/2
 * @time: 14:37
 */

use Library\Core\BaseController;

class ErrorController extends Yaf_Controller_Abstract
{
    //从2.1开始, errorAction支持直接通过参数获取异常
    public function errorAction(\Exception $exception)
    {
        $error_code = $exception->getCode();

        switch ($error_code)
        {
            case YAF_ERR_NOTFOUND_MODULE:
            case YAF_ERR_NOTFOUND_CONTROLLER:
            case YAF_ERR_NOTFOUND_ACTION:
            case YAF_ERR_NOTFOUND_VIEW:
        }

        Yaf_Dispatcher::getInstance()->disableView();
        echo ($exception->xdebug_message);
    }
}