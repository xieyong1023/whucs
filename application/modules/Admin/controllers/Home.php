<?php
/**
 * 后台首页
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/28
 * @Time: 11:38
 */

use Library\Core\AdminController;
use Library\Service\AdminMenuService;

/**
 * Class Home
 */
class HomeController extends AdminController
{
    /**
     * @var string 默认控制器
     */
    protected $default_action = 'adminCenter';

    /**
     * 后台默认页面
     * @author: xieyong <qxieyongp@163.com>
     */
    public function indexAction()
    {
        $this->forward($this->_module, $this->_name, $this->default_action);
    }

    /**
     * 个人中心页面
     * @author: xieyong <qxieyongp@163.com>
     */
    public function adminCenterAction()
    {
        $menu_service = new AdminMenuService(['current_top_category' => 'admin_center']);

        $params = [
            'top_category' => $menu_service->getTopCategory(),
            'left_category' => $menu_service->getLeftCategory(),
        ];
        $this->display('index', $params);
    }

    /**
     * 系统管理
     * @author: xieyong <qxieyongp@163.com>
     */
    public function systemAction()
    {
        $menu_service = new AdminMenuService(['current_top_category' => 'system']);

        $params = [
            'top_category' => $menu_service->getTopCategory(),
            'left_category' => $menu_service->getLeftCategory(),
        ];
        $this->display('index', $params);
    }

    /**
     * 内容管理
     * @author: xieyong <qxieyongp@163.com>
     */
    public function contentAction()
    {
        $menu_service = new AdminMenuService(['current_top_category' => 'content']);

        $params = [
            'top_category' => $menu_service->getTopCategory(),
            'left_category' => $menu_service->getLeftCategory(),
        ];
        $this->display('index', $params);
    }

    /**
     * 用户管理
     * @author: xieyong <qxieyongp@163.com>
     */
    public function userAction()
    {
        $menu_service = new AdminMenuService(['current_top_category' => 'user']);

        $params = [
            'top_category' => $menu_service->getTopCategory(),
            'left_category' => $menu_service->getLeftCategory(),
        ];
        $this->display('index', $params);
    }
}
