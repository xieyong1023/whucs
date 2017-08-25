<?php
/**
 * 数据库异常
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/20
 * @Time: 20:00
 */

namespace Library\Exception;

use Library\Core\Exception;

/**
 * Class DBException
 * @package Library\Exception
 */
class DBException extends Exception
{
    /**
     * @var array 范围 102000 ~ 103000
     */
    protected $map = [

    ];
}
