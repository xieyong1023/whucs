<?php
/**
 * 登录控制器
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/17
 * @Time: 21:47
 */

use Library\Core\FrontController;

class LoginController extends FrontController
{
    public function showLoginAction()
    {
        $this->disableView();
        echo 'show login';
    }
}
