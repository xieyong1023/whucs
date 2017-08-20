<?php
/**
 * Index控制器
 *
 * @author: xieyong <qxieyongp@163.com>
 * @date: 2017/8/2
 * @time: 12:55
 */

use Library\Core\BaseController;

class IndexController extends BaseController
{

    public function indexAction()
    {
        Yaf_Dispatcher::getInstance()->autoRender(false);
        echo 'index page';
    }

    public function phpinfoAction()
    {
        Yaf_Dispatcher::getInstance()->disableView();
        phpinfo();
    }

    public function testAction()
    {
        $this->show_404();
    }
}