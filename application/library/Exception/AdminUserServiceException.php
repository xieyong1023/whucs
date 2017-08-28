<?php
/**
 * 管理员用户服务层异常
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/28
 * @Time: 21:36
 */

namespace Library\Exception;

use Library\Core\Exception;

/**
 * Class AdminUserServiceException
 * @package Library\Exception
 */
class AdminUserServiceException extends Exception
{
    /**
     * @var array 范围 105000 ~ 106000
     */
    protected $map = [
        'USERNAME_IS_EMPTY' => ['code' => 105001, 'zh_cn' => '用户名为空'],
        'DB_ERROR'          => ['code' => 105002, 'zh_cn' => '数据库错误'],
        'USERNAME_EXISTED'  => ['code' => 105003, 'zh_cn' => '用户名已存在'],
    ];
}
