<?php
/**
 * 用户服务层异常
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/26
 * @Time: 15:26
 */

namespace Library\Exception;

use Library\Core\Exception;

class UserServiceException extends Exception
{
    /**
     * @var array 范围 104000 ~ 105000
     */
    protected $map = [
        'USERNAME_IS_EMPTY'  => ['code' => 104001, 'zh_cn' => '用户名为空'],
        'USERNAME_EXISTED'   => ['code' => 104002, 'zh_cn' => '该用户名已被占用'],
        'PASSWORD_IS_EMPTY'  => ['code' => 104003, 'zh_cn' => '密码为空'],
        'STUDENTID_IS_EMPTY' => ['code' => 104004, 'zh_cn' => '学号为空'],
        'STUDENTID_EXISTED'  => ['code' => 104005, 'zh_cn' => '该学号已被占用'],
        'INVALID_UID'        => ['code' => 104006, 'zh_cn' => '无效用户id'],
        'DB_ERROR'           => ['code' => 104007, 'zh_cn' => '数据库操作异常'],
        'NICKNAME_IS_EMPTY'  => ['code' => 104008, 'zh_cn' => '昵称为空'],
    ];
}
