<?php
/**
 * 依赖注入异常
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/19
 * @Time: 22:25
 */

namespace Library\Exception;

use \Library\Core\Exception;

/**
 * Class DIException
 * @package Library\Exception
 */
class DIException extends Exception
{
    /**
     * @var array 范围 101000 ~ 102000
     */
    protected $map = [
        'SERVICE_DEFINE_IS_NOT_CALLABLE' => ['code' => 101001, 'zh_cn' => '服务不可调用'],
        'SERVICE_DUPLICATE_DEFINE'       => ['code' => 101002, 'zh_cn' => '服务重复定义'],
        'SERVICE_NOT_DEFINED'            => ['code' => 101003, 'zh_cn' => '服务未定义'],
    ];
}
