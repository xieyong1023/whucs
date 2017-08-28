<?php
/**
 * 用户核心信息表
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/26
 * @Time: 10:47
 */

namespace Library\Mysql;

use Library\Database\DBBase;
use Library\Exception\DBException;
use Library\Tools\StringHelper;

/**
 * Class UserCore
 * @package Library\Mysql
 */
class UserCore extends DBBase
{
    /**
     * @var string 数据库配置名
     */
    protected $config_name = 'default';
    /**
     * @var string 表名
     */
    protected $table = 'user_core';
    /**
     * @var array 配置
     */
    protected $option = [
        'salt_length'    => 6, // salt长度
        'user_core_info' => [
            'id',
            'username',
            'nickname',
            'student_id',
            'status',
        ],
    ];

    /**
     * UserCore constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 添加用户
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param array ...$data
     */
    public function addUser(...$data)
    {
        $user_to_add = [];
        $user = [];
        foreach ($data as $item) {
            $user['username'] = $item['username'];
            $user['nickname'] = $item['nickname'];
            $user['student_id'] = $item['student_id'];
            $user['password'] = $item['password'];
            $user['salt'] = $item['salt'];
            $user['create_time'] = NOW_TIME;
            $user['update_time'] = NOW_TIME;
            $user['status'] = 0;
            $user['type'] = 0;

            array_push($user_to_add, $user);
        }

        $this->insert(...$user_to_add);
    }

    /**
     * 判断用户名是否存在
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $username username
     *
     * @return bool
     * @throws DBException
     */
    public function isUsernameExist(string $username)
    {
        if (empty($username)) {
            return false;
        }

        $where = [
            'OR' => [
                'username' => $user['username'],
                'nickname' => $user['nickname'],
            ],
        ];

        return $this->isExist($where);
    }

    /**
     * 判断学号是否存在
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $nickname nickname
     *
     * @return bool
     * @throws DBException
     */
    public function isStudentIdExist(string $nickname)
    {
        if (empty($nickname)) {
            return false;
        }

        $where = [
            'OR' => [
                'username'   => $user['student_id'],
                'nickname'   => $user['student_id'],
                'student_id' => $user['student_id'],
            ],
        ];

        return $this->isExist($where);
    }

    /**
     * 根据id获取用户信息
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param int $uid uid
     *
     * @return array
     * @throws DBException
     */
    public function getUserById(int $uid)
    {
        return $this->getOne($this->option['user_core_info'], ['id' => $uid]);
    }

    /**
     * 根据昵称获取用户信息
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $nickname nickname
     *
     * @return array
     */
    public function getUserByNickname(string $nickname)
    {
        return $this->getOne($this->option['user_core_info'], ['nickname' => $nickname]);
    }

    /**
     * 根据学号获取用户信息
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $student_id 学号
     *
     * @return array
     */
    public function getUserByStudentId(string $student_id)
    {
        return $this->getOne($this->option['user_core_info'], ['student_id' => $student_id]);
    }

    /**
     * 生成密码加密随机串salt
     * @author: xieyong <qxieyongp@163.com>
     * @return string
     */
    public function createSalt()
    {
        return StringHelper::getRandomString($this->option['salt_length']);
    }
}
