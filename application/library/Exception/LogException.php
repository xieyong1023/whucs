<?php
/**
 *  日志异常
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/19
 * @Time: 20:34
 */

namespace Library\Exception;

use \Library\Core\Exception;

/**
 * Class LogException
 * @package Library\Exception
 */
class LogException extends Exception
{
    /**
     * @var array 范围 102000 ~ 103000
     */
    protected $map = [
        'INVALID_EXCEPTION_LEVEL' => ['code' => 102001, 'zh_cn' => '无效的异常等级'],
    ];
}
