<?php
/**
 * 后台首页
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/28
 * @Time: 11:38
 */

use Library\Core\AdminController;

/**
 * Class Home
 */
class HomeController extends AdminController
{
    public function indexAction()
    {

        $params = [

        ];
        $this->display('index', $params);
    }
}