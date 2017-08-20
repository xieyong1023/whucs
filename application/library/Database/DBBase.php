<?php
/**
 * 数据库操作基类
 *
 * @author: xieyong <qxieyongp@163.com>
 * @Date: 2017/8/20
 * @Time: 13:44
 */

namespace Library\Database;

use Library\DI\DI;
use Library\Log\Logger;

/**
 * Class DBBase
 * @package Library\Database
 */
class DBBase
{
    /**
     * @var string 配置在database中的数据库名，所有继承DBbase的类都需要提供
     */
    protected $config_name = '';
    /**
     * @var string 表名，所有继承DBBase的类都需要提供
     */
    protected $table = '';
    /**
     * @var \PDO 主数据库
     */
    protected $master = null;
    /**
     * @var array 从数据库
     */
    protected $slaves = [];
    /**
     * @var DI
     */
    protected $di = null;
    /**
     * @var string 日志服务名
     */
    private $logger_name = 'database';
    /**
     * @var Logger
     */
    protected $logger = null;

    /**
     * DBBase constructor.
     */
    public function __construct()
    {
        $this->di = DI::getInstance();

        $this->logger = $this->di->get($this->logger_name . '_log');
    }

    public function getOne(array $colums = [], $where = [])
    {
        try {
            $data = $this->medoo->get($this->table, $colums, $where);
        } catch (\PDOException $e) {
            $this->logException($e);
            return [];
        }
    }


    protected function logException(\PDOException $e)
    {
        $this->logger->error(
            'code:' . $e->getCode(),
            'msg:' . $e->getMessage(),
            'file:' . $e->getFile(),
            'line:' . $e->getLine()
        );
    }
}
