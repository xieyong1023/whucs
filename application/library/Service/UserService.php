<?php
/**
 * 用户相关服务层
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/24
 * @Time: 9:23
 */

namespace Library\Service;

use Library\Core\Exception;
use Library\Exception\UserServiceException;
use Library\Mysql\UserCore;
use Library\Tools\Encrypt;
use Library\Tools\StringHelper;
use Library\Core\Service;

/**
 * Class UserService
 * @package Library\Service
 */
class UserService extends Service
{
    /**
     * @var string 日志名
     */
    protected $log_name = 'user_service';
    /**
     * @var array 配置
     */
    protected $option = [
        'salt_length' => 6, // salt长度
    ];

    public function __construct(array $option = [])
    {
        parent::__construct($option);
    }

    /**
     * 根据用户名添加用户
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param array $user_data 用户
     * $user = [
     *      'student_id' => '123',
     *      'password' => 'some_pass',
     * ]
     *
     * @throws UserServiceException
     */
    public function addUserByUsername(...$user_data)
    {
        $user_to_add = [];
        $user_core = new UserCore();

        foreach ($user_data as $user) {
            if (empty($user['username'])) {
                throw new UserServiceException('USERNAME_NOT_SET');
            }

            $is_exist = [
                'OR' => [
                    'username' => $user['username'],
                    'nickname' => $user['nickname'],
                ],
            ];
            if ($user_core->isExist($is_exist)) {
                throw new UserServiceException('USERNAME_EXIST');
            }

            if (empty($user['password'])) {
                throw new UserServiceException('PASSWORD_NOT_SET');
            }

            $salt = $this->getPasswordSalt();
            $password = Encrypt::passwordEncrypt($user['password'], $salt);

            $new_user = [
                'username'    => $user['username'],
                'nickname'    => $user['username'],
                'student_id'  => '',
                'password'    => $password,
                'salt'        => $salt,
                'create_time' => NOW_TIME,
                'update_time' => NOW_TIME,
                'status'      => 0,
                'type'        => 0,
            ];

            array_push($user_to_add, $new_user);
        }

        if (! empty($user_to_add)) {
            $user_core->insert(...$user_to_add);
        }
    }

    /**
     * 根据学号添加用户
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param array ...$user_data
     *
     * @throws UserServiceException
     */
    public function addUserByStudentId(...$user_data)
    {
        $user_to_add = [];
        $user_core = new UserCore();

        foreach ($user_data as $user) {
            if (empty($user['student_id'])) {
                throw new UserServiceException('STUDENTID_NOT_SET');
            }

            $is_exist = [
                'OR' => [
                    'username'   => $user['student_id'],
                    'nickname'   => $user['student_id'],
                    'student_id' => $user['student_id'],
                ],
            ];
            if ($user_core->isExist($is_exist)) {
                throw new UserServiceException('STUDENTID_EXIST');
            }

            if (empty($user['password'])) {
                throw new UserServiceException('PASSWORD_NOT_SET');
            }

            $salt = $this->getPasswordSalt();
            $password = Encrypt::passwordEncrypt($user['password'], $salt);

            $new_user = [
                'username'    => $user['student_id'],
                'nickname'    => $user['student_id'],
                'student_id'  => $user['student_id'],
                'password'    => $password,
                'salt'        => $salt,
                'create_time' => NOW_TIME,
                'update_time' => NOW_TIME,
                'status'      => 0,
                'type'        => 0,
            ];

            array_push($user_to_add, $new_user);
        }

        if (! empty($user_to_add)) {
            $user_core->insert(...$user_to_add);
        }
    }

    /**
     * uid换用户信息
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param int $uid uid
     * @param     $columns 列名 不指定则获取所有信息
     *
     * @return array|bool|mixed
     * @throws UserServiceException
     */
    public function getUserCoreById(int $uid, $columns)
    {
        $user_core = new UserCore();

        if ($uid < 0) {
            throw new UserServiceException('INVALID_UID');
        }

        return $user_core->getOne($columns, ['id' => $uid]);
    }

    /**
     * 昵称换用户信息
     * @author: xieyong <qxieyongp@163.com>
     * @param string $nickname 昵称
     * @param        $columns 列名
     *
     * @return array|bool|mixed
     */
    public function getUserCoreByNickname(string $nickname, $columns)
    {
        $user_core = new UserCore();

        return $user_core->getOne($columns, ['nickname' => $nickname]);
    }

    /**
     * 学号换用户信息
     * @author: xieyong <qxieyongp@163.com>
     * @param string $student_id 学号
     * @param        $columns 列名
     *
     * @return array|bool|mixed
     */
    public function getUserCoreByStudentId(string $student_id, $columns)
    {
        $user_core = new UserCore();

        return $user_core->getOne($columns, ['student_id' => $student_id]);
    }

    /**
     * 判断用户密码是否正确
     * @author: xieyong <qxieyongp@163.com>
     * @param string $student_id
     * @param string $password
     *
     * @return bool
     */
    public function isPasswordCorrect(string $student_id, string $password)
    {
        if (empty($password)) {
            return false;
        }

        $user_core = new UserCore();
        $user = $user_core->getOne(['password', 'salt'], ['student_id' => $student_id]);

        if (empty($user)) {
            return false;
        }

        $encrypt_password = Encrypt::passwordEncrypt($password, $user['salt']);
        if ($encrypt_password === $user['password']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取密码加密的随机字符
     * @author: xieyong <qxieyongp@163.com>
     * @return string
     */
    protected function getPasswordSalt()
    {
        return StringHelper::getRandomString($this->option['salt_length']);
    }
}