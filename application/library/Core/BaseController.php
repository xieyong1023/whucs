<?php
/**
 * 控制器基类
 *
 * @author: xieyong <qxieyongp@163.com>
 * @date: 2017/8/7
 * @time: 13:56
 */

namespace Library\Core;

class BaseController extends \Yaf_Controller_Abstract
{
    public function init()
    {
        // 禁用自动渲染
        \Yaf_Dispatcher::getInstance()->disableView();
    }

    /**
     * json输出
     * @author: xieyong <qxieyongp@163.com>
     * @param array  $data 要输出数据
     * @param int    $error 错误号 0-无错误 其他-有错
     * @param string $msg 错误消息
     */
    protected function outputJson($data = [], int $error = 0, string $msg = 'ok')
    {
        $rtv = [
            'error' => $error,
            'msg' => $msg,
            'data' => $data,
        ];

        \Yaf_Dispatcher::getInstance()->disableView();
        $response = $this->getResponse();
        $response->setHeader('Content-Type', 'application/json');
        $response->setBody(\json_encode($rtv));
    }
}
