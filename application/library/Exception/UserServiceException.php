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
        'USERNAME_NOT_SET'  => ['code' => 104001, 'zh_cn' => '未设置用户名'],
        'USERNAME_EXIST'    => ['code' => 104002, 'zh_cn' => '该用户名已被占用'],
        'PASSWORD_NOT_SET'  => ['code' => 104003, 'zh_cn' => '密码未设置'],
        'STUDENTID_NOT_SET' => ['code' => 104004, 'zh_cn' => '学号未设置'],
        'STUDENTID_EXIST'   => ['code' => 104005, 'zh_cn' => '该学号已被占用'],
        'INVALID_UID'       => ['code' => 104006, 'zh_cn' => '无效用户id'],
    ];
}
