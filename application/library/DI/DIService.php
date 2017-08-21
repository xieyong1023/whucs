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
class DIService
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
     * @var DIService 若服务为已共享，则保存当前服务实例
     */
    protected $shared_instance = null;

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
    public function invoke(...$param)
    {
        // 共享服务返回已共享实例
        if ($this->is_shared && $this->shared_instance !== null) {
            return $this->shared_instance;
        }

        // 服务为已加载的类类名，则生成实例
        if (is_string($this->define) && class_exists($this->define)) {
            $class_reflection = new \ReflectionClass($this->define);
            $instance = $class_reflection->newInstanceArgs($param);
        }

        // 服务定义为函数闭包时，直接调用
        if (is_callable($this->define, true)) {
            $instance = call_user_func_array($this->define, $param);
        }

        // 共享服务则保存实例
        if ($this->is_shared) {
            $this->shared_instance = $instance;
        }

        return $instance;
    }

    /**
     * 判断服务是否共享
     * @author: xieyong <qxieyongp@163.com>
     * @return bool
     */
    public function isShared()
    {
        return $this->is_shared;
    }
}
