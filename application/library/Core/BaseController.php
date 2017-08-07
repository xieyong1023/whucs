<?php
/**
 * 控制器基类
 *
 * @author: xieyong <xieyong1023@qq.com>
 * @date: 2017/8/7
 * @time: 13:56
 */

namespace Library\Core;

class BaseController extends Yaf_Controller_Abstract
{
    public function init()
    {
        parent::init();
    }

    protected function outputJson($data = '', int $error = 0, string $msg = 'ok')
    {
        $rtv = [
            'error' => $error,
            'msg' => $msg,
            'data' => $data,
        ];

        $response = $this->getResponse();
        $response->setHeader('Content-Type', 'application/json');
        $response->setBody(\json_encode($rtv));
        $response->response();
    }
}
