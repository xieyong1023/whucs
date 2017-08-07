<?php
/**
 *
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 *
 * @author: xieyong <xieyong1023@qq.com>
 * @date: 2017/8/2
 * @time: 14:12
 * @see http://www.laruence.com/manual/yaf.class.bootstrap.html
 */

class Bootstrap extends Yaf_Bootstrap_Abstract{

    public function _init_test()
    {

    }

    /**
     * 是否输出错误
     */
    public function _init_is_open_error()
    {
        if (defined('DEBUG') && DEBUG == true) {
            ini_set('display_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        }
    }


    public function _init_config()
    {

    }

    public function _init_plugin()
    {

    }

    /**
     * 注册路由
     * @param Yaf_Dispatcher $dispatcher
     */
    public function _init_route(Yaf_Dispatcher $dispatcher)
    {
        $router = $dispatcher->getRouter();
        $router->addConfig(new Yaf_Config_Ini(CONFIG_PATH . '/router.ini'));
    }
}