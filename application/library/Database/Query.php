<?php


/**
 * Class Query
 *
 * $pdo = new PDO();
 *
 * // fetchAll, "?" as placeholder
 * $query = new Query('select * from `food` where `calorie` < ?', [200], $pdo);
 * $healthyFoodList = $query->fetchAll();
 *
 * // fetchColumn, bindParams, named placeholder
 * $cities = ['Wuhan', 'Beijing', 'Shanghai'];
 * $query = new Query('select `name` from `company` where `city` = :city and `staff_num` > :num', ['num' => 500], $pdo);
 * $query->bindParams(':city', $city); // bind variable
 * foreach ($cities as $city) {
 *      $names = $query->fetchColumn();
 *      printf("Big companies in %s are: %s \n", $city, implode(', ', $names);
 * }
 *
 * // fetchRow, omitting PDO object on construction, default fetch style.
 * $query = new Query('select * from `player` where `nick` = :nick', [':nick' => 'yyf'], $pdo);
 * $user = $query->fetchRow();
 *
 * // fetchScalar
 * $query = new Query('select count(1) from `car` where `brand` IN (?,?,?)', ['BMW', 'FORD', 'LOTUS'], $pdo);
 * $count = $query->fetchScalar();
 *
 * // fetchObject
 * $query = new Query('select * from `player` where `nick` = :nick', [':nick' => 'yyf'], $pdo);
 * // $player will be an instance of \app\model\Player. Or null if not found.
 * $player = $query->fetchObject(\app\model\Player::class);
 *
 * // fetchMap
 * $query = new Query('select `id`,`name` from `car` where `brand` IN (?,?,?)', ['BMW', 'FORD', 'LOTUS'], $pdo);
 * $id2Name = $query->fetchMap(['id' => 'name']);
 *
 * </code>
 *
 */
class Query
{

    /**
     * @var string
     */
    protected $sqlText;

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * 记录引用的参数
     * @var array
     */
    protected $refParams = [];

    /**
     * @var PDOStatement
     */
    private $_statement;

    /**
     * @var PDO
     */
    private $_lastPDO;

    private $_queryOptions = [];

    /**
     * Query constructor.
     * @param string $sqlText
     * @param array $params
     * @param PDO|null $pdo
     * @param array $queryOptions
     */
    public function __construct($sqlText, array $params = null, PDO $pdo = null, array $queryOptions = null)
    {
        $this->sqlText = $sqlText;
        $this->params = $params ?: [];
        $this->pdo = $pdo;
        if ($queryOptions) {
            $this->_queryOptions = $queryOptions;
        }
    }

    /**
     * 获取一个QueryBuilder实例
     * @param array $config
     * @return QueryBuilder
     */
    public static function builder(array $config = null)
    {
        return QueryBuilder::factory($config);
    }

    /**
     * 获得当前参数的数量
     * @return int
     */
    public function getParamCount()
    {
        return count($this->params);
    }

    /**
     * 获得当前的参数值
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * 获得当前的SQL文本
     * @return string
     */
    public function getSqlText()
    {
        return $this->sqlText;
    }

    /**
     * 获取当前的PDO对象
     * @return null|PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * 设置PDO
     * @param PDO $pdo
     * @return $this
     */
    public function setPdo(PDO $pdo)
    {
        $this->pdo = $pdo;
        return $this;
    }

    /**
     * 获取最近一次执行时所用的PDO对象
     * @return PDO
     */
    public function getLastPdo()
    {
        return $this->_lastPDO;
    }

    /**
     * 设置需要绑定的参数
     * 调用此方法会清除已存在的绑定参数和查询语句。
     * @param $params
     * @return $this
     */
    public function setBindValues(array $params)
    {
        $this->params = $params;
        $this->close();
        return $this;
    }

    /**
     * 关闭当前的查询句柄
     */
    public function close()
    {
        if ($this->_statement) {
            $this->_statement->closeCursor();
            $this->_statement = null;
        }
    }

    /**
     * 绑定整形值
     * @param string|int $parameter
     * @param int $value
     * @return $this
     */
    public function bindIntValue($parameter, $value)
    {
        return $this->bindValue($parameter, $value, PDO::PARAM_INT);
    }

    /**
     * 绑定值
     * @param string|int $parameter
     * @param mixed $value
     * @param int $data_type
     * @return $this
     *
     * @see \PDOStatement::bindValue()
     */
    public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR)
    {
        if ($this->_statement) {
            $this->_statement->closeCursor();
            $this->_statement->bindValue($parameter, $value, $data_type);
        }
        if (is_int($parameter)) {
            $this->params[$parameter - 1] = $value;
        } else {
            $this->params[$parameter] = $value;
        }
        return $this;
    }

    /**
     * 绑定整形变量
     * @param string|int $parameter
     * @param mixed $variable
     * @return $this
     */
    public function bindIntParam($parameter, &$variable)
    {
        return $this->bindParam($parameter, $variable, PDO::PARAM_INT);
    }

    /**
     * 绑定变量
     * @param string|int $parameter
     * @param mixed $variable
     * @param int $data_type
     * @return $this
     *
     * @see \PDOStatement::bindParam()
     */
    public function bindParam($parameter, &$variable, $data_type = PDO::PARAM_STR)
    {
        if ($this->_statement) {
            $this->_statement->closeCursor();
            $this->_statement->bindParam($parameter, $variable, $data_type);
        }
        if (is_int($parameter)) {
            $this->params[$parameter - 1] = &$variable;
            $this->refParams[$parameter - 1] = &$variable;
        } else {
            $this->params[$parameter] = &$variable;
            $this->refParams[$parameter] = &$variable;
        }
        return $this;
    }

    /**
     * 执行语句，并返回受影响的行数
     * @param PDO|null $pdo
     * @return int
     */
    public function execute(PDO $pdo = null)
    {
        $this->ensureStatement($pdo)->execute();
        return $this->_statement->rowCount();
    }

    /**
     * 生成查询语句.
     * 初次生成时会自动绑定参数。再次调用此方法时不会更改参数。
     * @param PDO|null $pdo
     * @return PDOStatement
     */
    protected function ensureStatement(PDO $pdo = null)
    {

        if (!$pdo) {
            $pdo = $this->pdo;
        }
        if (!$pdo instanceof PDO) {
            throw new \InvalidArgumentException('An active PDO is required to create PDOStatement.');
        }

        if ($this->isAnotherPdo($pdo)) {
            $this->close();
        }

        if (!$this->_statement) {
            $this->_statement = $statement = $pdo->prepare($this->sqlText);
            foreach ($this->params as $key => &$value) {
                if (is_int($key)) {
                    $parameter = $key + 1;
                } else {
                    if ($key[0] !== ':') {
                        $parameter = ':' . $key;
                    } else {
                        $parameter = $key;
                    }
                }
                $data_type = is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR;

                if (isset($this->refParams[$key])) {
                    $statement->bindParam($parameter, $value, $data_type);
                } else {
                    $statement->bindValue($parameter, $value, $data_type);
                }
            }
            $this->recordUsedPdo($pdo);
        } else {
            $this->_statement->closeCursor();
        }

        return $this->_statement;
    }

    /**
     * 判断是否是另一个PDO对象。
     * @param PDO $pdo
     * @return bool
     */
    private function isAnotherPdo($pdo)
    {
        return $this->_lastPDO && $this->_lastPDO !== $pdo;
    }

    /**
     * 记录本次使用的pdo对象，以便将来能够进行对比。
     *
     * @param PDO $pdo
     */
    private function recordUsedPdo($pdo)
    {
        $this->_lastPDO = $pdo;
    }

    /**
     * 获得上一个被插入的记录的ID
     * @return bool|string
     */
    public function getInsertedId()
    {
        if ($this->_lastPDO) {
            return $this->_lastPDO->lastInsertId();
        }
        return 0;
    }

    /**
     * 返回结果集中的所有行。此方法封装了 \PDOStatement::fetchAll()。
     *
     * @param PDO|null $pdo
     * @param int $fetchStyle 默认为PDO::FETCH_BOTH
     * @param null $fetchArgument
     * @param array $constructArgs
     * @return array
     * @see \PDOStatement::fetchAll()
     */
    public function fetchAll(PDO $pdo = null, $fetchStyle = PDO::FETCH_BOTH, $fetchArgument = null, array $constructArgs = null)
    {
        $this->ensureStatement($pdo)->execute();
        $args = array_slice(func_get_args(), 1);
        return $this->_statement->fetchAll(...$args);
    }

    /**
     * 返回结果集中的所有行，以自然索引数组的形式。
     *
     * @param PDO|null $pdo
     * @return array
     */
    public function fetchAllNum(PDO $pdo = null)
    {
        return $this->fetchAll($pdo, PDO::FETCH_NUM);
    }

    /**
     * 返回结果集中的所有行，以关联数组的形式。
     *
     * @param PDO|null $pdo
     * @return array
     */
    public function fetchAllAssoc(PDO $pdo = null)
    {
        return $this->fetchAll($pdo, PDO::FETCH_ASSOC);
    }

    /**
     * 返回每一行作为指定类的实例。返回一个数组。
     *
     * @param string $className 要实例化的类名
     * @param array|null $constructArgs 传递给类的构造器的参数。
     * @param PDO|null $pdo
     * @return array
     */
    public function fetchAllObject($className, array $constructArgs=null, PDO $pdo = null)
    {
        $this->ensureStatement($pdo)->execute();
        return $this->_statement->fetchAll(\PDO::FETCH_CLASS, $className, $constructArgs);
    }

    /**
     * 返回结果集中的第一行。若结果集为空，返回false.
     *
     * @param PDO|null $pdo
     * @param int $fetchStyle 支持 PDO::FETCH_ASSOC, PDO::FETCH_NUM, PDO::FETCH_BOTH. 默认为PDO::FETCH_BOTH
     * @return array|false
     */
    public function fetchRow(PDO $pdo = null, $fetchStyle = PDO::FETCH_BOTH)
    {
        $this->ensureStatement($pdo)->execute();
        return $this->_statement->fetch($fetchStyle);
    }

    /**
     * 返回结果集中的第一行，以自然索引数组的形式。若结果集为空，返回false.
     *
     * @param PDO|null $pdo
     * @return array|false
     */
    public function fetchRowNum(PDO $pdo = null)
    {
        return $this->fetchRow($pdo, PDO::FETCH_NUM);
    }

    /**
     * 返回结果集中的第一行，以关联数组的形式。若结果集为空，返回false.
     *
     * @param PDO|null $pdo
     * @return array|false
     */
    public function fetchRowAssoc(PDO $pdo = null)
    {
        return $this->fetchRow($pdo, PDO::FETCH_ASSOC);
    }

    /**
     * 返回结果集中第一行，作为指定类的实例。若结果集为空，返回false。
     *
     * @param string $className 要实例化的类名
     * @param array|null $constructArgs 传递给类的构造器的参数。
     * @param PDO|null $pdo
     * @return Object|null
     */
    public function fetchRowObject($className, array $constructArgs=null, PDO $pdo = null)
    {
        $this->ensureStatement($pdo)->execute();
        return $this->_statement->fetchObject($className, $constructArgs);
    }

    /**
     * 返回结果集中每一行的第一列的值。
     * @param PDO|null $pdo
     * @return array
     */
    public function fetchColumn(PDO $pdo = null)
    {
        $this->ensureStatement($pdo)->execute();
        return $this->_statement->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * 返回结果集中第一行第一列的值。若结果集为空，返回false。
     * @param PDO|null $pdo
     * @return string|false
     */
    public function fetchScalar(PDO $pdo = null)
    {
        $this->ensureStatement($pdo)->execute();
        return $this->_statement->fetchColumn();
    }


    /**
     * 以一个关联数组的形式返回结果集。
     * 参数是一个键值对，以键名所对应的列作为返回数组的key，以值所对应的列作为返回数组的值。
     * 键名必须是一个字符串，值可以是一个字符串或字符串数组。如果值是数组，那么返回的数组的每个下标所对应的也是一个二维数组。
     *
     * fetchMap(['id' => 'name'])  则结果会是以id字段为key， name字段为value的一个一维数组：
     * [
     *    2 => 'John', 3 => 'Ash', ...
     * ]
     * fetchMap(['id' => ['name', 'weight'])  则结果会是以id字段为key, name和weight字段组成的关联数组为value的一个二维数组：
     * [
     *    2 => ['name' => 'John', 'weight' => '55KG'],
     *    3 => ['name' => 'Ash', 'weight' => '65KG'],
     * ]
     * fetchMap(['id' => '*']) 则结果会是以id字段为key，所有字段组成的关联数组为value的一个二维数组：
     * [
     *    2 => ['id' => 2, 'age' => 35, 'name' => 'John', 'weight' => '55KG', ...(其他select的字段)],
     *    3 => ['id' => 3, 'age' => 35, 'name' => 'Ash', 'weight' => '65KG', ...(其他select的字段)],
     * ]
     * fetchMap(['age[]' => 'name']) 则结果会是以age字段为key，name字段的列表为value的二维数组：
     * [
     *    35 => ['John', 'Ash'],
     *    ...
     * ]
     * fetchMap(['age[]' => ['name', 'weight']) 则结果会是以age字段为key，name和weight字段组成的关联数组的列表的一个三维数组：
     * [
     *    35 => [
     *      ['name' => 'John', 'weight' => '55KG'],
     *      ['name' => 'Ash', 'weight' => '65KG'],
     *    ],
     *    ...
     * ]
     * fetchMap(['age[]' => '*']) 则结果会是以age字段为key，所有字段组成的关联数组的列表的一个三维数组：
     * [
     *    35 => [
     *      ['id' => 2, 'age' => 35, 'name' => 'John', 'weight' => '55KG', ...(其他select的字段)],
     *      ['id' => 3, 'age' => 35, 'name' => 'Ash', 'weight' => '65KG', ...(其他select的字段)],
     *    ],
     *    ...
     * ]
     *
     * 如果在QueryBuilder的selectMap方法中指定了，那么此参数可以省略。
     *
     * @param array|null $option
     * @param PDO $pdo
     * @return array
     */
    public function fetchMap(array $option = null, PDO $pdo = null)
    {

        if (!$option) {
            if (!isset($this->_queryOptions['fetchMap'])) {
                throw new \InvalidArgumentException('Should specify a map on invoke or construction.');
            }
            $option = $this->_queryOptions['fetchMap'];
        }

        if (!is_array($option) || empty($option)) {
            throw new \InvalidArgumentException('fetchMap options should be a key-value pair.');
        }

        list($fetchMapKey, $fetchMapField) = each($option);

        $map = [];
        $rows = $this->fetchAllAssoc($pdo);

        if ($valueAsList = (substr($fetchMapKey, -2) === '[]')) {
            $fetchMapKey = substr($fetchMapKey, 0, -2);
        }

        if (is_array($fetchMapField)) {
            if ($valueAsList) {
                foreach ($rows as $row) {
                    $item = [];
                    foreach ($fetchMapField as $field) {
                        $item[$field] = $row[$field];
                    }
                    $map[$row[$fetchMapKey]][] = $item;
                }
            } else {
                foreach ($rows as $row) {
                    $item = [];
                    foreach ($fetchMapField as $field) {
                        $item[$field] = $row[$field];
                    }
                    $map[$row[$fetchMapKey]] = $item;
                }
            }
        } else {
            if ($valueAsList) {
                if ($fetchMapField === '*') {
                    foreach ($rows as $row) {
                        $map[$row[$fetchMapKey]][] = $row;
                    }
                } else {
                    foreach ($rows as $row) {
                        $map[$row[$fetchMapKey]][] = $row[$fetchMapField];
                    }
                }
            } else {
                if ($fetchMapField === '*') {
                    foreach ($rows as $row) {
                        $map[$row[$fetchMapKey]] = $row;
                    }
                } else {
                    $map = array_column($rows, $fetchMapField, $fetchMapKey);
                }
            }
        }

        return $map;
    }

    /**
     * 析构时关闭查询句柄
     */
    public function __destruct()
    {
        $this->close();
    }
}