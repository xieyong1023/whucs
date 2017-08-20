<?php
/**
 * 配置管理类
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/18
 * @Time: 22:24
 */

namespace Library\Config;

use Library\Exception\ConfigException;

/**
 * Class ConfigManager
 * @package Library\Config
 */
class ConfigManager
{
    /**
     * @var ConfigManager 当前类的实例
     */
    protected static $_instance = null;
    /**
     * @var string 配置文件路径
     */
    protected static $_config_path = CONFIG_PATH;
    /**
     * @var array 缓存
     */
    protected $config = [];

    /**
     * 单例模式
     * ConfigManager constructor.
     */
    private function __construct()
    {
    }

    /**
     * 获取当前类的实例
     * @author: xieyong <qxieyongp@163.com>
     * @return ConfigManager
     */
    public static function getInstance()
    {
        if (self::$_instance instanceof self) {
            return self::$_instance;
        } else {
            self::$_instance = new self();

            return self::$_instance;
        }
    }

    /**
     * 设置配置文件路径
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $path
     */
    public static function setConfigFilePath(string $path)
    {
        self::$_config_path = $path;
    }

    /**
     * 获取配置
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $filename 文件名
     *
     * @return Config
     * @throws ConfigException
     */
    public function getConfig(string $filename)
    {
        if (isset($this->config[$filename])) {
            return $this->config[$filename];
        } else {
            $read_config = $this->loadConfigFile($filename);
            $this->config[$filename] = $read_config;

            return $read_config;
        }
    }

    /**
     * 读取配置文件
     * 保存在变量 $_config中
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $filename 配置文件名
     *
     * @return Config
     * @throws ConfigException
     */
    protected function loadConfigFile(string $filename)
    {
        if (empty(self::$_config_path)) {
            throw new ConfigException('CONFIG_PATH_NOT_SET');
        }

        $config_file_path = rtrim(self::$_config_path, DIRECTORY_SEPARATOR);
        $file_path = $config_file_path . DIRECTORY_SEPARATOR . $filename . '.php';

        if (! file_exists($file_path)) {
            throw new ConfigException('CONFIG_FILE_NOT_EXIST');
        }

        $config_data = include $file_path;

        return new Config($config_data);
    }
}
