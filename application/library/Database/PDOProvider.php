<?php
/**
 * PDO对象工厂方法，获取数据库连接
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/20
 * @Time: 19:58
 */

namespace Library\Database;

use Library\Config\ConfigManager;
use Library\Exception\DBException;

/**
 * Class PDOProvider
 * @package Library\Database
 */
class PDOProvider
{
    public static function getPdo()
    {
        $db_config_array = ConfigManager::getInstance()->getConfig('database')->toArray();

        if (empty($db_config_array)) {
            throw new DBException('DATABASE_CONFIG_NOT_SET');
        }

        foreach ($db_config_array as $name => $config) {

        }
    }
}
