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
     * UserCore constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
}
