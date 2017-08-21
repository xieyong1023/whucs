<?php
/**
 * 依赖注入类
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/19
 * @Time: 22:17
 */

namespace Library\DI;

use Library\Exception\DIException;

/**
 * Class DI
 * @package Library\DI
 */
class DI
{
    /**
     * @var DI 实例变量
     */
    protected static $_instance = null;
    /**
     * @var array 已注册服务
     */
    protected $services = [];
    /**
     * @var array 已共享服务
     */
    protected $shared_services = [];

    /**
     * DI constructor.
     */
    private function __construct()
    {
    }

    /**
     * 单例模式，获取实例
     * @author: xieyong <qxieyongp@163.com>
     */
    public static function getInstance()
    {
        if (! self::$_instance instanceof self) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 设置服务
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $name 服务名字
     * @param        $define
     *
     * @throws DIException
     */
    public function set(string $name, $define)
    {
        if (isset($this->services[$name])) {
            throw new DIException('SERVICE_DUPLICATE_DEFINE');
        }

        $service = new DIService($name, $define, false);
        $this->services[$name] = $service;
    }

    /**
     * 获取服务
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $name 服务名
     * @param mixed  $params 调用服务时传递的参数
     *
     * @return mixed
     * @throws DIException
     */
    public function get(string $name, ...$params)
    {
        if (! isset($this->services[$name])) {
            throw new DIException('SERVICE_NOT_DEFINED');
        }

        $service = $this->services[$name];

        return $service->invoke($params);
    }

    /**
     * 设置共享服务
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $name 服务名
     * @param        $define
     *
     * @throws DIException
     */
    public function setShared(string $name, $define)
    {
        if (isset($this->shared_services[$name])) {
            throw new DIException('SERVICE_DUPLICATE_DEFINE');
        }

        $service = new DIService($name, $define, true);
        $this->shared_services[$name] = $service;
    }

    /**
     * 获取共享服务
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $name 服务名
     * @param array  $params 调用参数
     *
     * @return mixed
     * @throws DIException
     */
    public function getShared(string $name, ...$params)
    {
        if (! isset($this->shared_services[$name])) {
            throw new DIException('SERVICE_NOT_DEFINED');
        }

        $service = $this->shared_services[$name];

        return $service->invoke($params);
    }
}
