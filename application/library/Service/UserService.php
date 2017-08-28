<?php
/**
 * 用户相关服务层
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/24
 * @Time: 9:23
 */

namespace Library\Service;

use Library\Config\ConfigManager;
use Library\Exception\DBException;
use Library\Exception\UserServiceException;
use Library\Mysql\UserCore;
use Library\Tools\Encrypt;
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
        try {
            $user_core = new UserCore();

            foreach ($user_data as $user) {
                if (empty($user['username'])) {
                    throw new UserServiceException('USERNAME_IS_EMPTY');
                }

                if ($user_core->isUsernameExist($user['username'])) {
                    throw new UserServiceException('USERNAME_EXISTED');
                }

                if (empty($user['password'])) {
                    throw new UserServiceException('PASSWORD_IS_EMPTY');
                }

                $salt = $user_core->createSalt();
                $key = ConfigManager::getInstance()->getConfig('encrypt')->item('password_key');
                $password = Encrypt::passwordEncrypt($user['password'], $salt, $key);

                $new_user = [
                    'username'    => $user['username'],
                    'nickname'    => $user['username'],
                    'student_id'  => '',
                    'password'    => $password,
                    'salt'        => $salt,
                ];

                array_push($user_to_add, $new_user);
            }

            if (! empty($user_to_add)) {
                $user_core->addUser(...$user_to_add);
            }
        } catch (DBException $e) {
            $this->logger->logException($e);

            throw new UserServiceException('DB_ERROR');
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

        try {
            $user_core = new UserCore();

            foreach ($user_data as $user) {
                if (empty($user['student_id'])) {
                    throw new UserServiceException('STUDENTID_IS_EMPTY');
                }

                if ($user_core->isStudentIdExist($user['student_id'])) {
                    throw new UserServiceException('STUDENTID_EXISTED');
                }

                if (empty($user['password'])) {
                    throw new UserServiceException('PASSWORD_IS_EMPTY');
                }

                $salt = $user_core->createSalt();
                $key = ConfigManager::getInstance()->getConfig('encrypt')->item('password_key');
                $password = Encrypt::passwordEncrypt($user['password'], $salt, $key);

                $new_user = [
                    'username'    => $user['student_id'],
                    'nickname'    => $user['student_id'],
                    'student_id'  => $user['student_id'],
                    'password'    => $password,
                    'salt'        => $salt,
                ];

                array_push($user_to_add, $new_user);
            }

            if (! empty($user_to_add)) {
                $user_core->addUser(...$user_to_add);

            }
        } catch (DBException $e) {
            $this->logger->logException($e);

            throw new UserServiceException('DB_ERROR');
        }
    }

    /**
     * uid换用户信息
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param int $uid uid
     *
     * @return array|bool|mixed
     * @throws UserServiceException
     */
    public function getUserCoreById(int $uid)
    {
        if ($uid < 0) {
            throw new UserServiceException('INVALID_UID');
        }

        try {
            $user_core = new UserCore();

            return $user_core->getUserById($uid);
        } catch (DBException $e) {
            $this->logger->logException($e);

            throw new UserServiceException('DB_ERROR');
        }
    }

    /**
     * 昵称换用户信息
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $nickname 昵称
     * @param        $columns
     *
     * @return array|bool|mixed
     * @throws UserServiceException
     */
    public function getUserCoreByNickname(string $nickname, $columns = '*')
    {
        if (empty($nickname)) {
            throw new UserServiceException('NICKNAME_IS_EMPTY');
        }

        try {
            $user_core = new UserCore();

            return $user_core->getUserByNickname($nickname, $columns);
        } catch (DBException $e) {
            $this->logger->logException($e);

            throw new UserServiceException('DB_ERROR');
        }
    }

    /**
     * 学号换用户信息
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $student_id 学号
     * @param        $columns
     *
     * @return array|bool|mixed
     * @throws UserServiceException
     */
    public function getUserCoreByStudentId(string $student_id, $columns = '*')
    {
        if (empty($student_id)) {
            throw new UserServiceException('STUDENTID_IS_EMPTY');
        }

        try {
            $user_core = new UserCore();

            return $user_core->getUserByStudentId($student_id, $columns);
        } catch (DBException $e) {
            $this->logger->logException($e);

            throw new UserServiceException('DB_ERROR');
        }
    }

    /**
     * 判断用户密码是否正确
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $student_id
     * @param string $password
     *
     * @return bool
     * @throws UserServiceException
     */
    public function isPasswordCorrect(string $student_id, string $password)
    {
        if (empty($password)) {
            return false;
        }

        $user = $this->getUserCoreByStudentId($student_id);

        if (empty($user)) {
            return false;
        }

        $key = ConfigManager::getInstance()->getConfig('encrypt')->item('password_key');
        $encrypt_password = Encrypt::passwordEncrypt($password, $user['salt'], $key);
        if ($encrypt_password === $user['password']) {
            return true;
        } else {
            return false;
        }
    }
}
