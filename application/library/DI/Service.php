<?php
/**
 * 依赖注入服务类
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/19
 * @Time: 22:17
 */

namespace Library\DI;

use Library\Exception\DIException;

/**
 * Class Service
 * @package Library\DI
 */
class Service
{
    /**
     * @var string 服务名
     */
    protected $name = '';
    /**
     * @var null 服务定义
     */
    protected $define = null;
    /**
     * @var bool 是否是共享服务
     */
    protected $is_shared = false;

    /**
     * Service constructor.
     *
     * @param string $name 服务名
     * @param        $define 服务定义
     * @param bool   $is_shared 是否共享
     */
    public function __construct(string $name, $define, bool $is_shared = false)
    {
        $this->name = $name;
        $this->define = $define;
        $this->is_shared = $is_shared;
    }

    /**
     * 调用服务
     *
     * @param mixed $param
     *
     * @author: xieyong <qxieyongp@163.com>
     * @return mixed
     * @throws DIException
     */
    public function invoke($param = [])
    {
        // 服务定义为函数闭包时，直接调用
        if (! is_callable($this->define)) {
            throw new DIException('SERVICE_DEFINE_IS_NOT_CALLABLE');
        }

        if (is_array($param)) {
            return call_user_func_array($this->define, $param);
        } else {
            return call_user_func_array($this->define, [$param]);
        }
    }

    /**
     * 判断服务是否共享
     * @author: xieyong <qxieyongp@163.com>
     * @return bool
     */
    public function isShared()
    {
        return (true === $this->is_shared);
    }

    /**
     * 设置是否共享
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param bool $is_shared
     */
    public function setShared(bool $is_shared)
    {
        $this->is_shared = $is_shared;
    }
}
