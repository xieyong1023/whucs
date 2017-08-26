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
use Library\Exception\DBException;
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
    protected $config_name = 'default';
    /**
     * @var Medoo
     */
    public $medoo = null;
    /**
     * @var \PDO
     */
    public $pdo = null;
    /**
     * @var string 表名，所有继承DBBase的类都需要提供
     */
    protected $table = '';
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

        $this->medoo = $this->di->getShared($this->config_name . '_db');
        if (false === $this->medoo) {
            throw new DBException('DB_CONNECTION_ERROR');
        }

        $this->pdo = $this->medoo->pdo;
    }

    /**
     * 获取数据
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string|array $columns
     * colums:
     *      '*' // 获取所有列
     *      ['col1', 'col2', ..., 'coln'] // 获取指定列
     * Data Mapping:
     *      // 指定输出数据的结构
     *      [
     *          'map1' => [
     *              'col1', // 列名
     *              'col2',
     *          ],
     *          'map2' => [
     *              'col3',
     *              'col4',
     *          ],
     *      ]
     * Data Type Declaration:
     *      // Supported data type: [String | Bool | Int | Number | Object | JSON]
     *      // [String] is the default type for all output data.
     *      // [Object] is a PHP object data decoded by serialize(), and will be unserialize()
     *      // [JSON] is a valid JSON, and will be json_decode()
     *      [
     *          'col1[Int]' // int
     *          'col2[Bool]' // bool
     *          'col3[JSON]' // json
     *      ]
     * Alias:
     *      ['nickname(my_nickname)'] // Output my_nickname => value
     * @param array|null   $where 见getOne
     *
     * @return array|bool
     */
    public function getData($columns, $where = null)
    {
        return $this->medoo->select($this->table, $columns, $where);
    }

    /**
     * 表连接
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param array        $join 连接语句
     * [>] == LEFT JOIN
     * [<] == RIGH JOIN
     * [<>] == FULL JOIN
     * [><] == INNER JOIN
     *
     * Here is the table relativity argument that tells the relativity between the table you want to join.
     *
     * The row author_id from table post is equal the row user_id from table account
     * "[>]account" => ["author_id" => "user_id"],
     * The row user_id from table post is equal the row user_id from table album.
     * This is a shortcut to declare the relativity if the row name are the same in both table.
     * "[>]album" => "user_id",
     *
     * [post.user_id is equal photo.user_id and post.avatar_id is equal photo.avatar_id]
     * Like above, there are two row or more are the same in both table.
     * "[>]photo" => ["user_id", "avatar_id"],
     *
     * If you want to join the same table with different value,
     * you have to assign the table with alias.
     * "[>]account (replyer)" => ["replyer_id" => "user_id"],
     * You can refer the previous joined table by adding the table name before the column.
     * "[>]account" => ["author_id" => "user_id"],
     * "[>]album" => ["account.user_id" => "user_id"],
     *
     * Multiple condition
     *  "[>]account" => [
     *      "author_id" => "user_id",
     *      "album.user_id" => "user_id"
     *  ]
     * @param string|array $columns
     * @param array|null   $where
     *
     * @return array|bool
     */
    public function getDataWithJoin(array $join, $columns, $where = null)
    {
        return $this->medoo->select($this->table, $join, $columns, $where);
    }

    /**
     * 获取一条数据
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string|array $columns
     * @param array|null   $where
     * =:
     *      ["email" => "foo@bar.com"] // WHERE email = 'foo@bar.com'
     * IN:
     *      ["id" => [1, 2, 3, 4]] // WHERE id IN (1, 2, 3, 4)
     * >, >=, <, <=, !:
     *      ["user_id[>]" => 200] // WHERE user_id > 200
     *      大于等于[>=], 不等于[!], 小于[<], 小于等于[<=]
     * BETWEEN:
     *      ["age[<>]" => [200, 500]] // WHERE age BETWEEN 200 AND 500
     * NOW BETWEEN:
     *      ["age[><]" => [200, 500]] // WHERE age NOT BETWEEN 200 AND 500
     *      ["birthday[><]" => [date("Y-m-d", mktime(0, 0, 0, 1, 1, 2015)), date("Y-m-d")]]
     *      //WHERE "create_date" BETWEEN '2015-01-01' AND '2015-05-01' (now)
     * OR:
     *      ["OR" => ["id" => 1, "name" => 'abc']] // WHERE id = 1 OR name = abc
     * AND:
     *      ["AND" => ["id" => 1, "name" => 'abc']] // WHERE id = 1 AND name = abc
     * LIKE:
     *      ["name[~]" => ["value1", "value2"]] // WHERE name LIKE %value1% OR name LIKE %value2%
     *      ["city[~]" => "%abc"] // AAAabc BBBabc CCCabc
     *      ["city[~]" => "abc_"] // abcAAA abcBBB abcCCC
     *      ["city[~]" => "[ABC]at"] // Aat Bat Cat
     *      ["city[~]" => [!ABC]at] // Eat Dat Hat
     * NOT LIKE:
     *      ["name[!~]" => "value"] // WHERE name NOT LIKE %value%
     *
     * ORDER:
     *      ["ORDER" => "col1"] // ORDER BY col1
     *      [
     *          "ORDER" => [
     *              'col1' => [1, 2, 3], // Order by column with sorting by customized order.
     *              'col2', // Order by col2
     *              'col3' => "DESC" // Order by col3 DESC
     *              'col4' => "ASC" // Order by col4 ASC
     *          ]
     *      ]
     * SQL FUNCTIONS:
     *      ["#datetime" => 'NOW()'] // WHERE "datetime" = NOW()
     * LIMIT:
     *      ['LIMIT' => 20] // LIMIT 20
     *      ['LIMIT' => [100, 20]] // OFFSET 100 LIMIT 20
     * GROUP:
     *      ['GROUP' => 'col'] // GROUP BY col
     *      ['GROUP' => ['col1', 'col2', 'col3']]
     * HAVING:
     *      [HAVING => ['col[>]' => 'value']]
     *
     * @return array|bool|mixed
     */
    public function getOne($columns, $where = null)
    {
        return $this->medoo->get($this->table, $columns, $where);
    }

    /**
     * 插入，支持多个数据同时插入
     *
     * $this->insert(table, ['col1' => value1, 'col2' => value2, ..., 'coln' => valuen], ...);
     *
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param array ...$data 插入数据
     *
     * @return \PDOStatement
     */
    public function insert(...$data)
    {
        return $this->medoo->insert($this->table, ...$data);
    }

    /**
     * 返回自增键值
     * @author: xieyong <qxieyongp@163.com>
     * @return int|string
     */
    public function lastInsertId()
    {
        return $this->medoo->id();
    }

    /**
     * 更新
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param array      $data 数据
     *
     * "type" => "user",
     *
     * All age plus one
     * "age[+]" => 1,
     *
     * All level subtract 5
     * "level[-]" => 5,
     *
     * All score multiplied by 2
     * "score[*]" => 2,
     *
     * Array value
     * "lang" => ["en", "fr", "jp", "cn"],
     *
     * Array value encoded as JSON
     * "lang [JSON]" => ["en", "fr", "jp", "cn"],
     *
     * Boolean value
     * "is_locked" => true,
     *
     * Object value
     * "object_data" => $object_data,
     *
     * Large Objects (LOBs)
     * "image" => $fp,
     *
     * You can also assign # for using SQL functions
     * "#uid" => "UUID()"
     *
     * @param array|null $where 条件
     *
     * @return \PDOStatement
     */
    public function update(array $data, $where = null)
    {
        return $this->medoo->update($this->table, $data, $where);
    }

    /**
     * 删除
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param array $where 见getOne()
     *
     * @return \PDOStatement
     */
    public function delete(array $where)
    {
        return $this->medoo->delete($this->table, $where);
    }

    /**
     * 替换
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param        $colums 列名
     * @param null   $where 见getOne()
     *
     * @return bool|\PDOStatement
     */
    public function replace($colums, $where = null)
    {
        return $this->medoo->replace($this->table, $colums, $where);
    }

    /**
     * 检测是否存在
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param array $where 见getOne()
     *
     * @return bool
     */
    public function isExist(array $where)
    {
        return $this->medoo->has($this->table, $where);
    }

    /**
     * 计算总数
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param null $where 见getOne()
     *
     * @return bool|int
     */
    public function getCount($where = null)
    {
        return $this->medoo->count($this->table, $where);
    }

    /**
     * 获取某一列的最大值
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $column 列名
     * @param null   $where 见getOne
     *
     * @return bool|int|string
     */
    public function getMax(string $column, $where = null)
    {
        return $this->medoo->max($this->table, $column, $where);
    }

    /**
     * 获取某一列最小值
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $column 列名
     * @param null   $where 见getOne
     *
     * @return bool|int|string
     */
    public function getMin(string $column, $where = null)
    {
        return $this->medoo->min($this->table, $column, $where);
    }

    /**
     * 获取某一列平均值
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $column 列名
     * @param null   $where 见getOne
     *
     * @return bool|int
     */
    public function getAvg(string $column, $where = null)
    {
        return $this->medoo->avg($this->table, $column, $where);
    }

    /**
     * 计算某一列的和
     * @author: xieyong <qxieyongp@163.com>
     *
     * @param string $column 列名
     * @param null   $where 见getOne
     *
     * @return bool|int
     */
    public function getSum(string $column, $where = null)
    {
        return $this->medoo->sum($this->table, $column, $where);
    }

    /**
     * 获取上一条执行的语句
     * @author: xieyong <qxieyongp@163.com>
     * @return mixed
     */
    public function getLastQuery()
    {
        return $this->medoo->last();
    }

    /**
     * 获取数据库信息(Medoo)
     * @author: xieyong <qxieyongp@163.com>
     * @return array
     */
    public function getDbInfo()
    {
        return $this->medoo->info();
    }

    /**
     * 获取数据库连接属性(PDO)
     * @author: xieyong <qxieyongp@163.com>
     * @return array
     */
    public function getAttribute()
    {
        $attributes = [
            "AUTOCOMMIT", "ERRMODE", "CASE", "CLIENT_VERSION", "CONNECTION_STATUS",
            "ORACLE_NULLS", "PERSISTENT", "PREFETCH", "SERVER_INFO", "SERVER_VERSION",
            "TIMEOUT",
        ];

        $rtv = [];
        foreach ($attributes as $val) {
            $attr_name = "PDO::ATTR_" . $val;
            try {
                $attr_value = $this->pdo->getAttribute(constant($attr_name)) ?? '';
            } catch (\Exception $e) {
                // 有异常说明数据库不支持某个属性，忽略
            }
            $rtv[$attr_name] = $attr_value;
        }

        return $rtv;
    }
}
