<?php
/**
 * 配置类异常
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/18
 * @Time: 23:41
 */

namespace Library\Exception;

use \Library\Core\Exception;

/**
 * Class ConfigException
 * @package Library\Exception
 */
class ConfigException extends Exception
{
    /**
     * @var array 范围 100000 ~ 101000
     */
    protected $map = [
        'CONFIG_PATH_NOT_SET'   => ['code' => 100001, 'zh_cn' => '配置文件路径未设置'],
        'CONFIG_FILE_NOT_EXIST' => ['code' => 100002, 'zh_cn' => '配置文件不存在'],
        'CONFIG_ITEM_NOT_EXIST' => ['code' => 100003, 'zh_cn' => '配置项不存在'],
    ];
}
