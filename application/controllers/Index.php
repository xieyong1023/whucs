<?php
/**
 * Index控制器
 *
 * @author: xieyong <xieyong1023@qq.com>
 * @date: 2017/8/2
 * @time: 12:55
 */

class IndexController extends Yaf_Controller_Abstract
{
    public function indexAction()
    {
        Yaf_Dispatcher::getInstance()->autoRender(false);
        $view = $this->getView();
        $view->assign('content', 'hello world');

//        var_dump($view);

        echo $this->getViewpath();
        var_dump(new __PHP_Incomplete_Class());

//        $response = $this->getResponse();
//
//        $response->response();
    }

    public function phpinfoAction()
    {
        Yaf_Dispatcher::getInstance()->disableView();
        phpinfo();
    }

    public function testAction()
    {
        $a = $this->getRequest()->getQuery('a');
        $post = $this->getRequest()->getPost();
        var_dump($post);
//        var_dump($a);
        var_dump($_FILES);
        die;
    }
}