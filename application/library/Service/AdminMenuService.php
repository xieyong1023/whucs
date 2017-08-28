<?php
/**
 * 菜单服务类
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/28
 * @Time: 23:26
 */

namespace Library\Service;

use Library\Config\ConfigManager;
use Library\Core\Service;

/**
 * Class AdminMenuService
 * @package Library\Service
 */
class AdminMenuService extends Service
{
    /**
     * @var array 配置
     */
    protected $option = [
        'current_top_category' => 'admin_center',
    ];

    /**
     * AdminMenuService constructor.
     *
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        parent::__construct($option);
    }

    /**
     * 获取后台顶级菜单
     * @author: xieyong <qxieyongp@163.com>
     * @return array
     */
    public function getTopCategory()
    {
        $menu = ConfigManager::getInstance()->getConfig('menu')->toArray();

        $top_category = [];
        foreach ($menu as $key => $val) {
            $category = [];
            $category['name'] = $val['name'];
            $category['url'] = $val['url'];

            if ($key === $this->option['current_top_category']) {
                $category['current'] = true;
            } else {
                $category['current'] = false;
            }

            // todo 权限过滤
            array_push($top_category, $category);
        }

        return $top_category;
    }

    /**
     * 获取当前页面的左侧菜单
     * @author: xieyong <qxieyongp@163.com>
     * @return array
     */
    public function getLeftCategory()
    {
        // 当前页面的左侧菜单
        return ConfigManager::getInstance()->getConfig('menu')
            ->item($this->option['current_top_category'])['child'];
    }

    /**
     * 获取当前顶级菜单
     * @author: xieyong <qxieyongp@163.com>
     * @return mixed
     */
    public function getCurrentTopCategory()
    {
        return $this->option['current_top_category'];
    }
}