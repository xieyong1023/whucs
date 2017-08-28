<?php
/**
 * 后台登录
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/17
 * @Time: 22:31
 */

use Library\Core\AdminController;

class LoginController extends AdminController
{
    public function init()
    {
        parent::init();
    }

    public function showLoginAction()
    {
        echo 'show login action';
    }

    public function doLoginAction()
    {
        echo 'admin index';
    }
}
