<?php
/**
 * Medoo对象工厂方法，获取数据库连接
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/20
 * @Time: 19:58
 */

namespace Library\Database;

/**
 * Class PDOProvider
 * @package Library\Database
 */
class MedooProvider
{
    /**
     * 工厂方法，返回构造Medoo对象的闭包
     * @author: xieyong <qxieyongp@163.com>
     * @param $config
     *
     * @return \Closure
     */
    public static function getMedoo($config)
    {
        return function () use ($config) {
            $params = [
                'database_type' => $config['database_type'],
                'database_name' => $config['database_name'],
                'server'        => $config['ip'],
                'port'          => $config['port'],
                'username'      => $config['username'],
                'password'      => $config['password'],
                'charset'       => $config['charset'],
                'prefix'        => '',
                'logging'       => false,
                'option'        => [
                    \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                ],
            ];

            return new Medoo($params);
        };
    }
}
