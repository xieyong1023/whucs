<?php
/**
 * 管理员用户服务层
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/28
 * @Time: 19:38
 */

namespace Library\Service;

use Library\Config\ConfigManager;
use Library\Core\Service;
use Library\Exception\AdminUserServiceException;
use Library\Exception\DBException;
use Library\Mysql\AdminUser;
use Library\Tools\Encrypt;

class AdminUserService extends Service
{
    /**
     * @var array 配置
     */
    protected $option = [
        'salt_length' => 6,
    ];
    /**
     * @var string 日志名
     */
    protected $log_name = 'admin_user_service';

    public function __construct(array $option = [])
    {
        parent::__construct($option);
    }

    /**
     * 添加管理员用户
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $user_name 用户名
     * @param string $password 密码
     * @param int    $group 组id
     *
     * @throws AdminUserServiceException
     */
    public function addAdminUser(string $user_name, string $password, int $group)
    {
        if (empty($user_name)) {
            throw new AdminUserServiceException('USERNAME_IS_EMPTY');
        }

        try {
            $admin_user = new AdminUser();

            if ($admin_user->isUsernameExist($user_name)) {
                throw new AdminUserServiceException('USERNAME_EXISTED');
            }

            $salt = $admin_user->createSalt();
            $key = ConfigManager::getInstance()->getConfig('encrypt')->item('admin_password_key');
            $encrypt_password = Encrypt::passwordEncrypt($password, $salt, $key);

            $new_admin = [
                'username' => $user_name,
                'password' => $encrypt_password,
                'salt'     => $salt,
                'group'    => $group,
            ];

            $admin_user->addAdminUser($new_admin);
        } catch (DBException $e) {
            $this->logger->logException($e);

            throw new AdminUserServiceException('DB_ERROR');
        }
    }

    /**
     * 根据uid获取管理员信息
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param int $uid uid
     *
     * @return array
     * @throws AdminUserServiceException
     */
    public function getAdminUserById(int $uid)
    {
        try {
            $admin_user = new AdminUser();

            return $admin_user->getAdminUserById($uid);
        } catch (DBException $e) {
            $this->logger->logException($e);

            throw new AdminUserServiceException('DB_ERROR');
        }
    }

    /**
     * 根据用户名获取管理员用户信息
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $username username
     *
     * @return array
     * @throws AdminUserServiceException
     */
    public function getAdminUserByUsername(string $username)
    {
        try {
            $admin_user = new AdminUser();

            return $admin_user->getAdminUserByUsername($username);
        } catch (DBException $e) {
            $this->logger->logException($e);

            throw new AdminUserServiceException('DB_ERROR');
        }
    }
}
