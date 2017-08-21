<?php
/**
 * QueryBuild
 *
 * @author: xieyong <qxieyongp@163.com>
 * @date: 2017/8/21
 * @time: 17:48
 */

namespace Library\Database;

use PDO;

/**
 * Class QueryBuilder 用来辅助构建一个完整的Query对象.
 * @usage
 * <code>
 * use DouyuFramework\Database\QueryBuilder;
 *
 * QueryBuilder::factory(['pdo' => $pdo])->select(['id', 'name', 'price'])->from('book')->where('on_shelf=1')->fetchAll(); // 查询命令
 * QueryBuilder::factory()->table('book')->where('on_shelf=1')->withPDO($pdo)->count(); // 查询数量
 * QueryBuilder::factory()->table('book')->where(['name' => 'The Little Prince'])->exists($pdo); // 查询是否存在
 * QueryBuilder::factory()->update('book')->set(['on_shelf=0'])->where(['create_time' < 10200302])->execute($pdo); // 执行更新命令
 * QueryBuilder::factory()->insert('book')->value(['name' => 'The Little Prince', 'price' => 15.9])->execute($pdo); // 执行插入命令
 * QueryBuilder::factory()->delete()->from('book')->where(['on_shelf=0'])->limit(1)->execute($pdo); // 执行删除命令
 *
 * QueryBuilder::factory(['pdo' => $pdo])
 *  ->select(['b.id', 'b.name as bookName', 'a.name AS authorName'])
 *  ->from('book', 'b')
 *  ->leftJoin('author', 'a', 'a.id = b.author_id') // 构建联表查询
 *  ->fetchAll();
 *
 * QueryBuilder::factory(['pdo' => $pdo])
 *  ->selectMap(['a.name AS authorName' => 'COUNT(b.id) as bookCount']) // 直接构建select出一个map的查询
 *  ->from('author', 'a')
 *  ->leftJoin('book', 'b', 'b.author_id = a.id')
 *  ->fetchMap(); // 得到类似： ['托尔斯泰' => 23, '鲁迅' => 19, ...]
 *
 * // Query::builder() will also return an instance of QueryBuilder.
 * </code>
 *
 *
 * When calling these method, method build() will be called automatically
 * and afterward invoke these method on the Query object.
 * @method array fetchAll(PDO $pdo = null, int $fetchStyle = PDO::FETCH_BOTH, $fetchArguments = null, array $constructArgs = null)
 * @method array fetchAllNum(PDO $pdo = null)
 * @method array fetchAllAssoc(PDO $pdo = null)
 * @method array fetchAllObject($className = null, array $constructArgs = null, PDO $pdo = null)
 * @method array|false fetchRow(PDO $pdo = null, int $fetchStyle = PDO::FETCH_BOTH)
 * @method array|false fetchRowNum(PDO $pdo = null)
 * @method array|false fetchRowAssoc(PDO $pdo = null)
 * @method object|false fetchRowObject(string $className, $constructArgs = null, PDO $pdo = null)
 * @method array fetchColumn(PDO $pdo = null)
 * @method mixed|false fetchScalar(PDO $pdo = null)
 * @method array fetchMap(array $option = null)
 *
 */
class QueryBuilder extends Criteria
{

    /**
     * @var string
     */
    public $select = '*';

    /**
     * @var string
     */
    public $table;

    /**
     * @var string
     */
    public $forceIndex;

    /**
     * @var array
     */
    public $join = [];

    /**
     * 需要 insert 或 update 的值。关联数组。
     * @var array
     */
    protected $values = [];

    /**
     * 此次查询语句的命令。select|insert|update|delete
     * @var string
     */
    protected $command = 'select';

    /**
     * 是否使用SQL缓存。
     * 若为true，则会在select之后加上 SQL_CACHE 选项；
     * 若为false，则会在select之后加上 SQL_NO_CACHE 选项。
     * 否则不加。
     *
     * @var null
     */
    private $useSqlCache = null;

    /**
     * @var PDO
     */
    private $withPdo;

    /**
     * @var array
     */
    private $queryOptions = [];

    private $sqlText = null;

    /**
     * 工厂方法。覆盖了父类的工厂方法，支持传入"pdo"参数。
     *
     * @param array|null $config
     * @return static
     */
    public static function factory(array $config = null)
    {
        if (isset($config['pdo'])) {
            $pdo = $config['pdo'];
            unset($config['pdo']);
        }
        /** @var static $qb */
        $qb = parent::factory($config);
        return isset($pdo) ? $qb->withPDO($pdo) : $qb;
    }

    /**
     * 指定查询要使用的PDO连接。
     *
     * @param PDO $pdo
     * @return $this
     */
    public function withPDO(PDO $pdo)
    {
        $this->withPdo = $pdo;
        return $this;
    }

    /**
     * 魔术方法。
     * 当在QueryBuilder上调用fetch开头的方法时，则认为是要构建出Query对象后再在其上调用此方法。
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (substr_compare($name, 'fetch', 0, 5) === 0) {
            // 如果是Query上的fetchXXX的方法，就隐式调用build后再调用对应方法。
            return $this->build()->{$name}(...$arguments);
        }
        throw new \BadMethodCallException();
    }

    /**
     * 构建出Query对象
     * @return Query
     */
    public function build()
    {
        $params = $this->params;
        if ($this->havingParams) {
            $params = array_merge($params, $this->havingParams);
        }
        $sqlText = $this->getSqlText($params);
        return new Query($sqlText, $params, $this->withPdo, $this->queryOptions);
    }

    /**
     * 获取Sql文本
     * @param array $params 引用传参
     * @return string
     */
    public function getSqlText(array &$params = [])
    {
        if ($this->sqlText !== null) {
            return $this->sqlText;
        }

        if (!$this->table) {
            throw new \InvalidArgumentException('表名不能为空！');
        }

        switch (strtoupper($this->command)) {

            case 'SELECT':
                $sql = 'SELECT';
                if ($this->useSqlCache !== null) {
                    $sql .= $this->useSqlCache ? ' SQL_CACHE' : ' SQL_NO_CACHE';
                }
                if ($this->select === null || $this->select === '') {
                    $sql .= ' *';
                } else {
                    $sql .= ' '. $this->select;
                }
                $sql .= ' FROM '. $this->table;
                if ($this->forceIndex) {
                    $sql .= " FORCE INDEX ({$this->forceIndex})";
                }
                if ($this->join) {
                    foreach ($this->join as list($type, $table, $on)) {
                        if (is_array($on)) {
                            $sql .= " $type $table ON " . $this->parseConditionList($on, $params);
                        } else {
                            $sql .= " $type $table ON $on";
                        }
                    }
                }
                return $sql . parent::getSqlText($params);

            case 'INSERT':
                if (empty($this->values)) {
                    throw new \InvalidArgumentException('插入的值不能为空！');
                }
                $sql = "INSERT INTO {$this->table} ";

                $fields = [];
                $values = [];
                foreach ($this->values as $key => $val) {
                    if (is_string($key)) {
                        $fields[] = $key;
                        $values[] = $this->placeValue($val, $params);
                    } else {
                        if (!is_string($val)) {
                            throw new \InvalidArgumentException('INSERT的value设置不合法。必须是字符串。' . $key . ' is ' . gettype($val));
                        }
                        list($f, $v) = explode('=', $val, 2);
                        $f = trim($f);
                        $v = trim($v);
                        if (strlen($v)) {
                            $fields[] = $f;
                            if ($v[0] == ':') {
                                // 是一个placeholder
                                $values[] = $v;
                            } elseif (substr_compare($v, '{:', 0, 2) === 0) {
                                // 以 {: 开头，引导一个SQL表达式
                                $values[] = substr($v, 2);
                            } elseif ($v != '?') {
                                // 只是一个普通的值
                                $values[] = $this->placeValue(trim($v, '\'"'), $params);
                            } else {
                                throw new \InvalidArgumentException('不支持问号占位符。请使用 :name 格式代替。');
                            }
                        }
                    }
                }

                $sql .= '(' . implode(',', $fields) . ') VALUES (' . implode(',', $values) . ')';
                return $sql;

            case 'UPDATE':
                if (empty($this->values)) {
                    throw new \InvalidArgumentException('更新的值不能为空！');
                }
                $sql = "UPDATE {$this->table}";
                if ($this->join) {
                    foreach ($this->join as list($type, $table, $on)) {
                        if (is_array($on)) {
                            $sql .= " $type $table ON " . $this->parseConditionList($on, $params);
                        } else {
                            $sql .= " $type $table ON $on";
                        }
                    }
                }
                $sql .= " SET ";
                $tmp = [];
                foreach ($this->values as $column => $value) {
                    if (is_string($column)) {
                        if ($value === null) {
                            $tmp[] = "$column = NULL";
                            continue;
                        }
                        if (!is_string($value) && !is_numeric($value)) {
                            throw new \RuntimeException('UPDATE的值必须是一个字符串或数字。' . $column . ' is ' . gettype($value) . '.');
                        }
                        $tmp[] = "$column = " . $this->placeValue(trim($value, '\'"'), $params);
                    } else {
                        $tmp[] = $value;
                    }
                }
                $sql .= implode(',', $tmp);
                return $sql . parent::getSqlText($params);
                break;

            case 'DELETE':
                return "DELETE FROM {$this->table}" . parent::getSqlText($params);

            default:
                throw new \DomainException('不支持的SQL命令 "' . $this->command . '".');
        }

    }

    /**
     * 按指定的PDO连接来构建Query对象
     *
     * @param PDO $pdo
     * @return Query
     */
    public function buildWithPDO(PDO $pdo)
    {
        return $this->withPDO($pdo)->build();
    }

    /**
     * select一个map。此方法需要与Query::fetchMap联用。也可以在调用fetchMap时再传入map选项
     *
     * selectMap(['id' => 'name'])  则结果会是 以 id字段为key， name字段为value的一个一维数组
     * selectMap(['id' => ['name', 'age'])  则结果会是以 id字段为key, name和age字段组成的关联数组为值的一个二维数组
     * selectMap(['age[]' => 'name'])  则结果会是 以 age字段为key， name字段为list的一个二维数组
     *
     * 也可以指定复杂的条件：
     * selectMap(['room_id' => 'COUNT(`item_id`)']
     *
     * {@see Query::fetchMap}
     *
     * @param array $mapOption
     * @return QueryBuilder
     */
    public function selectMap(array $mapOption)
    {

        if (empty($mapOption)) {
            throw new \InvalidArgumentException('Argument #1 should contains one key-value pair.');
        }

        $select = [];
        list($key, $val) = each($mapOption);
        if (substr($key, -2) == '[]') {
            $select[] = substr($key, 0, -2);
        } else {
            $select[] = $key;
        }
        $fetchMapKey = $this->resolveColumnNameOfSelect($key);
        $fetchMapField = null;
        if (is_array($val)) {
            $select = array_merge($select, $val);
            $fetchMapField = array_map([$this, 'resolveColumnNameOfSelect'], $val);
        } else {
            $select[] = $val;
            $fetchMapField = $this->resolveColumnNameOfSelect($val);
        }

        $this->queryOptions['fetchMap'] = [$fetchMapKey => $fetchMapField];
        return $this->select(array_unique($select));
    }

    /**
     * 从select中得到查询结果集中的列名
     *
     * @param string $select
     * @return string
     */
    protected function resolveColumnNameOfSelect($select)
    {
        if (false !== ($pos = strripos($select, ' AS '))) {
            return trim(substr($select, $pos + 4), '` ');
        }
        if (preg_match('/^distinct[ \(]/i', $select)) {
            // 将 distinct(xxx) 或 distinct xxx 剥离成 xxx
            $select = trim(substr($select, 8), '` ()');
        }
        if (false !== ($pos = strripos($select, '.')) && preg_match('/^[\._\w`]+$/', $select)) {
            return trim(substr($select, $pos + 1), '` ');
        }
        return $select;
    }

    /**
     * 设置select
     * $queryBuilder->select('1');
     * $queryBuilder->select('distinct name, age');
     * $queryBuilder->select('max(age) as max_age, min(age) as min_age');
     * $queryBuilder->select('id', 'name', 'max(age)')
     * $queryBuilder->select(['count(id) as total_user', 'max(age) as max_age, min(age) as min_age'])
     *
     * @param string|array ...$select 如果第一个是数组，则只考虑第一个。
     * @return $this
     */
    public function select(...$select)
    {
        if (is_array($select[0])) {
            $select = implode(',', $select[0]);
        } else {
            $select = implode(',', $select);
        }
        $this->select = trim($select);
        return $this->setCommand('select');
    }

    /**
     * 设置要执行的命令
     *
     * @param string $command
     * @return QueryBuilder
     */
    public function setCommand($command)
    {
        $this->command = $command;
        return $this;
    }

    /**
     * 方法table的别名
     *
     * @param string $table
     * @param string $alias
     * @return $this
     */
    public function from($table, $alias = null)
    {
        return $this->table($table, $alias);
    }

    /**
     * 设置CURD命令的主表以及其别名
     * $queryBuilder->table('user');
     * $queryBuilder->table('user', 't');
     *
     * @param string $table
     * @param null|string $alias 别名默认为空。如果$table中有空格，那么此参数被忽略。
     * @return $this
     */
    public function table($table, $alias = null)
    {
        if ($alias === null || strpos($table, ' ') !== false) {
            $this->table = $table;
        } else {
            $this->table = "$table $alias";
        }
        return $this;
    }

    /**
     * 强制使用索引
     *
     * @param string $indexName
     * @return $this
     */
    public function forceIndex($indexName)
    {
        $this->forceIndex = $indexName;
        return $this;
    }

    /**
     * 左连接表
     * @param string $table
     * @param string $on
     * @param null $alias
     * @return $this
     */
    public function leftJoin($table, $on, $alias = null)
    {
        $this->join('LEFT JOIN', $table, $on, $alias);
        return $this;
    }

    /**
     * 连接表. 下列四种写法等价。
     * join('LEFT JOIN', 'my_table', 'my_table.ref_id = t.id')
     * join('LEFT JOIN', 'my_table', 'mt.ref_id = t.id', 'mt')
     * join('LEFT JOIN', 'my_table', 'mt', 'mt.ref_id = t.id')
     * join('LEFT JOIN', 'my_table', 'mt', ['mt.ref_id = t.id'])
     *
     * @param string $type join类型（e.g. "inner join", "left join"）
     * @param string $table 要join的表名
     * @param string|array $on join条件
     * @param null|string $alias 别名
     * @return $this
     */
    public function join($type, $table, $on, $alias = null)
    {
        if ($alias && strpos($alias, '=') !== false) {
            $t = $on;
            $on = $alias;
            $alias = $t;
            unset($t);
        }

        $this->join[] = [strtoupper($type), $alias ? "$table $alias" : $table, $on];
        return $this;
    }

    /**
     * 内连接表
     * @param string $table
     * @param string $on
     * @param null $alias
     * @return $this
     */
    public function innerJoin($table, $on, $alias = null)
    {
        $this->join('INNER JOIN', $table, $on, $alias);
        return $this;
    }

    /**
     * 右连接表
     *
     * @param string $table
     * @param string $on
     * @param null $alias
     * @return $this
     */
    public function rightJoin($table, $on, $alias = null)
    {
        $this->join('RIGHT JOIN', $table, $on, $alias);
        return $this;
    }

    /**
     * 指明这是一个UPDATE操作。也可以顺便设置要更新的表、列值以及条件。
     *
     * @param string $table
     * @param array $values
     * @param string|array $where
     * @param int $limit
     * @return static
     */
    public function update($table = null, $values = null, $where = null, $limit = null)
    {
        if (null !== $table) $this->table($table);
        if (null !== $values) $this->values($values);
        if (null !== $where) $this->where($where);
        if (null !== $limit) $this->limit($limit);
        return $this->setCommand('update');
    }

    /**
     * 设置要更新或插入的列值。仅对UPDATE和INSERT操作有效。
     *
     * @param array $values
     * @param bool $merge
     * @return $this
     */
    public function values($values = null, $merge = false)
    {
        if ($merge) {
            if (is_string($values)) {
                $this->values[] = [$values];
            } else {
                $this->values = array_merge($this->values, $values);
            }
        } else {
            if (is_string($values)) {
                $this->values = [$values];
            } else {
                $this->values = $values;
            }
        }
        return $this;
    }

    /**
     * 指明这是一个INSERT操作，同时可以顺便设置要插入的表和列值。
     *
     * @param string $table
     * @param array $values 键值对。暂不支持一次插入多行数据。
     * @return static
     */
    public function insert($table = null, $values = null)
    {
        if (null !== $table) $this->table($table);
        if (null !== $values) $this->values($values);
        return $this->setCommand('insert');
    }

    /**
     * 指明这是一个DELETE操作，同时可以设置要从哪个表里DELETE，以及删除条件
     *
     * @param string $table
     * @param string|array $where
     * @param int $limit
     * @param string|array $sort
     * @return $this
     */
    public function delete($table = null, $where = null, $limit = null, $sort = null)
    {
        if (null !== $table) $this->table($table);
        if (null !== $where) $this->where($where);
        if (null !== $limit) $this->limit($limit);
        if (null !== $sort) $this->sort($sort);
        return $this->setCommand('delete');
    }

    /**
     * Alias of values()
     *
     * @param null $values
     * @param bool $merge
     * @return QueryBuilder
     */
    public function set($values = null, $merge = false)
    {
        return $this->values($values, $merge);
    }

    /**
     * 是否使用SQL缓存。
     * 设置true来在select之后加入 SQL_CACHE 选项；
     * 设置false来在select之后加入 SQL_NO_CACHE 选项；
     * 如果设置为null或任何其他类型，将会使用数据库默认设置。
     *
     * @param bool $flag 非布尔值将被视为null，表示不使用数据库默认设置。
     * @return $this
     */
    public function cache($flag = true)
    {
        if (is_bool($flag)) {
            $this->useSqlCache = $flag;
        } else {
            $this->useSqlCache = null;
        }
        return $this;
    }

    /**
     * 快捷方法：构建并执行此查询。
     *
     * @param PDO|null $pdo
     * @return int
     */
    public function execute(PDO $pdo = null)
    {
        if ($this->command === 'select') {
            throw new \LogicException('execute方法不能用于构建查询类的Query。');
        }
        return $this->build()->execute($pdo);
    }

    /**
     * 快捷方法：查询符合条件的是否存在。
     *
     * @param PDO $pdo
     * @return bool
     */
    public function exists(PDO $pdo = null)
    {
        // 先暂存属性
        $select = $this->select;
        $limit = $this->limit;

        $ret = $this->select(1)->limit(1)->build()->fetchScalar($pdo) !== false;
        // 还原
        $this->select = $select;
        $this->limit = $limit;

        return $ret;
    }

    /**
     * 快捷方法：查询符合条件的数量。
     * Query::builder()->table('user')->where('type=1')->withPDO($pdo)->count(); // 默认count(1)
     * Query::builder()->table('user')->where('type=1')->count($pdo); // 默认count(1)时可以第一个参数传pdo对象
     * Query::builder()->table('user')->where('type=1')->count('DISTINCT `name`', $pdo); // COUNT(DISTINCT `name`)
     * @param string $column
     * @param PDO|null $pdo
     * @return int
     */
    public function count($column = '1', PDO $pdo = null)
    {
        if ($column instanceof PDO) {
            $pdo = $column;
            $column = '1';
        }

        $select = $this->select; // 暂存属性
        $ret = $this->select("COUNT({$column})")->build()->fetchScalar($pdo);
        $this->select = $select; // 还原
        if ($ret === false) {
            throw new \RuntimeException('查询失败！');
        }
        return intval($ret);
    }

}