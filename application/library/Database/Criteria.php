<?php
/**
 *
 *
 * @author: xieyong <qxieyongp@163.com>
 * @date: 2017/8/21
 * @time: 17:55
 */

namespace Library\Database;

/**
 * Class Criteria 提供了对查询条件组装逻辑的封装。
 *
 */
class Criteria
{
    /**
     * @var array|null|string
     */
    public $where = null;
    /**
     * @var array
     */
    public $params = [];

    /**
     * @var int|array|null
     */
    public $limit = null;

    /**
     * @var array|string|null
     */
    public $sort = null;
    /**
     * @var string|null
     */
    public $group = null;

    /**
     * @var array
     */
    public $having = null;

    /**
     * @var array
     */
    public $havingParams = null;

    /**
     * @var int
     */
    protected $placeholderSequence = 0;

    /**
     * Condition constructor.
     * @param string $where
     * @param array $params
     * @param string|array $limit
     */
    public function __construct($where = '', array $params = null, $limit = null)
    {
        $this->where = $where;
        is_array($params) && $this->params = $params;
        if ($limit) {
            $this->limit($limit);
        }
    }

    /**
     * 几种格式：
     * $criteria->limit(1);
     * $criteria->limit("LIMIT 1");
     * $criteria->limit("LIMIT :limit"); 这么写可以支持后期传参数做绑定。
     * $criteria->limit(['limit' => 1, 'offset' => 20]);
     * $criteria->limit("LIMIT :limit OFFSET: offset");
     *
     * @param string|array $sort
     * @return $this
     */
    public function limit($sort)
    {
        $this->limit = $sort;
        return $this;
    }

    /**
     * 工厂方法
     * @param array $config
     * @return static
     */
    public static function factory(array $config = null)
    {
        $criteria = new static;
        if ($config) {
            foreach ($config as $key => $val) {
                $criteria->$key = $val;
            }
        }
        return $criteria;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getSqlText();
    }

    /**
     * 获取sql语句（片段）
     *
     * @param array $params 引用传参
     * @return string
     */
    public function getSqlText(array &$params = [])
    {
        $sql = '';
        if ($whereStr = $this->getWhereString($params)) {
            $sql .= ' WHERE ' . $whereStr;
        }
        if ($optionStr = $this->getOptionString($params)) {
            $sql .= ' ' . $optionStr;
        }
        return $sql;
    }

    /**
     * 获取where语句的片段。不包含"WHERE".
     *
     * @param array $params 引用传参
     * @return array|null|string
     */
    public function getWhereString(array &$params)
    {
        if (!$this->where) {
            return '';
        }
        if (is_string($this->where)) {
            return $this->where;
        }

        return $this->parseConditionList($this->where, $params);
    }

    /**
     * 解析条件列表。返回sql语句片段，并填充参数列表。
     * @param array $whereArr
     * @param array $params 引用传参
     * @return string
     */
    protected function parseConditionList(array $whereArr, array &$params)
    {
        /** @var array $whereArr */
        $str = '';
        $i = 1;
        foreach ($whereArr as $key => $value) {
            //特殊搜索条件的组装 如 (expire=0 or expire>xxx)
            if ('(' === $key || ')' === $key) {
                $str .= $key;
                continue;
            }
            if ($i++ != 1 && (stripos($key, ' OR ') === false)) {
                $str .= ' AND ';
            }
            if (is_int($key)) {
                $str .= $value;
                continue;
            }
            if (strpos($key, ':') == FALSE) {
                if (strpos($key, '`') == 0 || strpos($key, '.') != FALSE) {
                    $str .= $key . '= ' . $this->placeValue($value, $params); // 若$key 值为 id! ,可以拼接出 不等于查询，大小等于也可
                } else {
                    $str .= '`' . $key . '` = ' . $this->placeValue($value, $params);
                }
            } else {
                $key_arr = explode(':', $key);
                if (strpos($key_arr[0], '`') !== false || strpos($key_arr[0], '.') != FALSE) {
                    $field = $key_arr[0];
                } else {
                    $field = '`' . $key_arr[0] . '`';
                }

                $opt = strtoupper(trim($key_arr[1]));
                if (!$opt) { // 兼容处理
                    $str .= $field. ' = ' . $this->placeValue($value, $params);
                } else {
                    switch ($opt) {
                        case 'IN':
                            // 复数，要求值是一个列表
                            if (!is_array($value)) {
                                throw new \InvalidArgumentException('Value must be an array when using "IN", "NOT IN" or "BETWEEN" condition.');
                            }
                            if (count($value) === 0) {
                                $str .= '1=0';
                            } else {
                                $str .= $field. ' IN '. $this->placeValueList($value, $params);
                            }
                            break;
                        case 'NOT IN':
                            // 复数，要求值是一个列表
                            if (!is_array($value)) {
                                throw new \InvalidArgumentException('Value must be an array when using "IN", "NOT IN" or "BETWEEN" condition.');
                            }
                            if (count($value) === 0) {
                                $str .= '1=1';
                            } else {
                                $str .= $field. ' NOT IN '. $this->placeValueList($value, $params);
                            }
                            break;
                        case 'BETWEEN':
                            // 如果是BETWEEN，要求必须有两个值
                            if (!is_array($value) && count($value) !== 2) {
                                throw new \InvalidArgumentException('Value must be an array that contains 2 elements when using "BETWEEN" condition.');
                            }
                            $str .= ' ' . $opt . ' ' . $this->placeValue($value[0], $params). ' AND '. $this->placeValue($value[1], $params);
                            break;
                        default:
                            $str .= "{$field} {$opt} " . $this->placeValue($value, $params);
                            break;
                    }
                }
                $str .= isset($key_arr[2]) ? $key_arr[2] : '';
            }
        }
        return $str;
    }

    /**
     * 放置一组值。placeValueList的复数版本。返回逗号分隔的一组占位符。
     * @param array $valueList 值列表
     * @param array $params 引用传参
     * @param bool $wrapWithParenthesis 是否用圆括号包裹起来。默认true。
     * @return string
     */
    protected function placeValueList(array $valueList, array &$params, $wrapWithParenthesis=true)
    {
        $tmp = [];
        foreach ($valueList as $value) {
            $tmp[] = $this->placeValue($value, $params);
        }
        if ($wrapWithParenthesis) {
            return '(' . implode(',', $tmp) . ')';
        }
        return implode(',', $tmp);
    }

    /**
     * 放置一个值。这会返回一个占位符，并在所给的参数列表中增加一项。
     * @param mixed $value
     * @param array $params
     * @return string
     */
    protected function placeValue($value, array &$params)
    {
        $name = ':dy' . $this->placeholderSequence++;
        $params[$name] = $value;
        return $name;
    }

    /**
     * 获得选项的string
     * @param array $params 引用传递
     * @return string
     */
    public function getOptionString(&$params)
    {
        $optionStr = '';

        if (is_string($this->group) && $this->group != '') {
            $optionStr .= 'GROUP BY ' . $this->group . ' ';
        }

        if ($this->having) {
            if (is_string($this->having)) {
                $optionStr .= 'HAVING '. $this->having;
            } else {
                $optionStr .= 'HAVING '. $this->parseConditionList($this->having, $params);
            }
        }

        if ($this->sort) {
            if (is_array($this->sort)) { //如果是多个字段排序 array('status' => 'desc', 'create_time' => 'asc')
                $order_str = '';
                foreach ($this->sort as $s => $o) {
                    $order_str .= $s . ' ' . $o . ',';
                }
                $order_str = substr($order_str, 0, strlen($order_str) - 1);
                $optionStr .= 'ORDER BY ' . $order_str;
            } elseif (stripos($this->sort, 'ORDER BY') === false) {
                $optionStr .= 'ORDER BY ' . $this->sort;
            } else {
                $optionStr .= $this->sort;
            }
        }

        if ($this->limit) {
            if (is_int($this->limit)) {
                if ($this->limit > 0) {
                    $optionStr .= " LIMIT $this->limit";
                }
            } elseif (is_array($this->limit)) {
                if (isset($this->limit['limit'])) {
                    $optionStr .= " LIMIT {$this->limit['limit']}";
                    if (isset($this->limit['offset'])) {
                        $optionStr .= " OFFSET {$this->limit['offset']}";
                    }
                }
            } elseif (is_string($this->limit)) {
                $optionStr .= " $this->limit";
            }
        }

        return $optionStr;
    }

    /**
     * 设置where
     * $where可以是一个数组或一个字符串。
     * @param string|array $where
     * @param array|null $params
     * @return $this
     */
    public function where($where = '', array $params = null)
    {
        $this->where = $where;
        if ($params !== null) {
            $this->params = $params;
        }
        return $this;
    }

    /**
     * 连结一个where。
     * @param string $where
     * @param array|null $params
     * @return $this
     */
    public function andWhere($where = '', array $params = null)
    {
        if ($where) {
            if (!$this->where) {
                $this->where = $where;
            } else {
                if (is_array($this->where) && is_array($where)) {
                    // 对于两个数组，直接合并
                    $this->where = array_merge($this->where, $where);
                } elseif (is_string($this->where) && is_string($where)) {
                    // 对于两个字符串，直接用AND连接
                    $this->where .= ' AND (' . $where . ')';
                } else {
                    // 否则转化成数组后再合并
                    $this->where = array_merge((array)$this->where, (array)$where);
                }
            }
        }

        if ($params !== null) {
            $this->params = array_merge($this->params, $params);
        }
        return $this;
    }

    /**
     * 设置参数。
     *
     * @param array $params 键值对。通常是占位符作为key。占位符的前置冒号可以省略。
     * @param bool $merge 如果为true，表示与当前已有的参数进行合并。
     * @return $this
     */
    public function params(array $params, $merge = false)
    {
        if ($merge) {
            $this->params = array_merge($this->params, $params);
        } else {
            $this->params = $params;
        }
        return $this;
    }

    /**
     * 设置排序选项
     * $criteria->sort("id"); // 等于 ORDER BY id ASC
     * $criteria->sort("create_time DESC, name ASC"); // 等于 ORDER BY create_time DESC, name ASC
     * $criteria->sort(['create_time' => 'DESC', 'name' => 'asc']);  // 等于 ORDER BY create_time DESC, name ASC
     *
     * @param string|array $sort
     * @return $this
     */
    public function sort($sort)
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * 设置group选项。
     * $criteria->group("room_id"); // 等于 GROUP BY room_id
     * $criteria->group("room_id, user_id"); // 等于 GROUP BY room_id, user_id
     *
     * @param string $group
     * @return $this
     */
    public function group($group)
    {
        $this->group = $group;
        return $this;
    }

    /**
     * 设置having选项。格式与 where 相同。
     *
     * @param string|array $having
     * @param array $params
     * @return $this
     */
    public function having($having, array $params=null)
    {
        $this->having = $having;
        $this->havingParams = $params;
        return $this;
    }

}