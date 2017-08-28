<?php
/**
 * 错误控制器
 *
 * @author: xieyong <qxieyongp@163.com>
 * @date: 2017/8/2
 * @time: 14:37
 */

use Library\Core\BaseController;
use Library\Config\ConfigManager;

class ErrorController extends BaseController
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
//                $this->show_404();
            default:

        }
        $this->disableView();
        $this->outputJson($exception->__toString(), $error_code, $exception->getMessage());
    }

    /**
     * 显示http code和msg
     * @author: xieyong <qxieyongp@163.com>
     * @param int    $code code
     */
    public function showHttpCodeAction(int $code = 200)
    {
        $http_code = ConfigManager::getInstance()->getConfig('http_code')->toArray();

        $params = [
            'code' => $code,
            'msg' => $http_code[$code],
        ];

       $this->display('showHttpCode', $params);
    }
}