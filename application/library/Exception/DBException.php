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
        'DB_CONNECTION_ERROR'         => ['code' => 102001, 'zh_cn' => '数据库连接错误'],
        'INSERT_TOO_MANY_ITEMS'       => ['code' => 102002, 'zh_cn' => '同时插入的数据过多'],
        'DELETE_PARAM_EMPTY'          => ['code' => 102003, 'zh_cn' => '删除操作传入参数为空'],
        'ISEXIST_PARAM_EMPTY'         => ['code' => 102004, 'zh_cn' => '检查是否存在时传入参数为空'],
        'UPDATE_WITH_EMPTY_CONDITION' => ['code' => 102005, 'zh_cn' => '更新表操作未设置查询条件'],
        'DB_ERROR'                    => ['code' => 102006, 'zh_cn' => '数据库操作失败'],
    ];
}
