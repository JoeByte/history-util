<?php

/**
 * Jmysql Operation Class
 * Jmysql IS USED TO OPERAT MYSQL
 * Jmysql.php是一个mysql数据库操作类，需要PHP的PDO拓展支持
 *
 * @package     xxtime/Jmysql.php
 * @author      joe@xxtime.com
 * @link        https://github.com/thendfeel/xxtime
 * @link        http://git.oschina.net/thendfeel/xxtime
 * @example     http://dev.xxtime.com
 * @copyright   xxtime.com
 * @since       2014-02-26
 */

// $config = array( 'host' => '127.0.0.1', 'port' => '3306', 'dbname' => 'db_test', 'user' => 'root', 'password' => '123456' );
// $db = new Jmysql($config);
// USE EXAMPLE NO.1 # FIND
// $ret0 = $db->find('user', 'id > 2', 1, 20);
// $ret1 = $db->findAll('user', array('id' => 1), 'id ASC', 'id, name');
// $ret2 = $db->findAll('user', array('name' => 'Joe'));
// $ret3 = $db->findOne('user', array('id' => 1), 'id DESC', array('id', 'name'));
// $ret4 = $db->findOne('user', 'id = 1', 'id DESC', array('id', 'name'));

// USE EXAMPLE NO.2 # INSERT
// $ret5 = $db->insert('user', array('name' => 'xxtime', 'age' => 26));

// USE EXAMPLE NO.3 # UPDATE
// $db->pk = 'student_id'; // 设置主键 默认为id
// $ret6 = $db->update('user', 5, array('age'=>27)); // 推荐
// $ret7 = $db->update('user', array('name'=>'Joe', 'age'=>26), array('age'=>27)); // 推荐
// $ret8 = $db->update('user', 'name="Joe"', 'age=27, name=Joe'); // 不推荐

// USE EXAMPLE NO.4 # DELETE
// $ret9 = $db->delete('user', 3);
// $ret10 = $db->delete('user', array('name'=>'Joe', 'age'=> 26));
class Jmysql
{

    public $host = 'localhost';

    public $port = 3306;

    public $dbname = '';

    public $user = '';

    public $password = '';

    public $pk = 'id';

    public $debug = FALSE;

    private $db;

    private $sql;

    public function __construct($config = array())
    {
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
        $this->init();
    }

    public function __destruct()
    {
        $this->debug();
    }

    /**
     * PDO初始化连接
     */
    private function init()
    {
        if (! $this->db) {
            $dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->dbname;
            try {
                $this->db = new PDO($dsn, $this->user, $this->password);
                $this->db->query('set names utf8');
            } catch (PDOException $e) {
                die('Connection failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * 分页获取
     *
     * @param string $table            
     * @param array|string $conditions            
     * @param intval $page            
     * @param intval $size            
     * @param array|string $order            
     * @param array|string $field            
     */
    public function find($table = '', $conditions = array(), $page = 1, $size = 20, $order = '', $field = array())
    {
        $where = $this->where($conditions);
        $order = $this->order($order);
        $field = $this->field($field);
        $skin = ($page - 1) * $size;
        $sql = "SELECT " . $field . " FROM `$table` WHERE " . $where . $order . " LIMIT $skin , $size";
        return $this->query($sql);
    }

    /**
     * 执行SQL查询语句 返回一条数据
     *
     * @param string $table            
     * @param array|string $conditions            
     * @param array|string $order            
     * @param array|string $field            
     */
    public function findOne($table = '', $conditions = array(), $order = '', $field = array())
    {
        $where = $this->where($conditions);
        $field = $this->field($field);
        $order = $this->order($order);
        $sql = "SELECT " . $field . " FROM `$table` WHERE " . $where . $order;
        $this->sql = $sql;
        $result = $this->db->query($sql);
        if (! $result) {
            return array();
        }
        $result->setFetchMode(PDO::FETCH_ASSOC);
        return $result->find();
    }

    /**
     * 执行SQL查询语句 返回多条数据
     *
     * @param string $table            
     * @param array|string $conditions            
     * @param array|string $order            
     * @param array|string $field            
     */
    public function findAll($table = '', $conditions = array(), $order = '', $field = array())
    {
        $where = $this->where($conditions);
        $field = $this->field($field);
        $order = $this->order($order);
        $sql = "SELECT " . $field . " FROM `$table` WHERE " . $where . $order;
        return $this->query($sql);
    }

    public function add($table, $input = array())
    {
        $field = '';
        $value = '';
        if (! is_array($input)) {
            return FALSE;
        }
        foreach ($input as $k => $v) {
            $field .= "`$k`" . ',';
            $value .= "'$v',";
        }
        $field = trim($field, ',');
        $value = trim($value, ',');
        $sql = "INSERT INTO `$table` ($field) VALUES ($value)";
        return $this->execute($sql);
    }

    /**
     * 更新
     *
     * @param string $table            
     * @param array|string|int $conditions            
     * @param array|string $field            
     */
    public function update($table, $conditions, $data)
    {
        $where = $this->where($conditions);
        if (is_array($data)) {
            $set = $this->built($data, ',');
        } else {
            $set = '';
            $setArray = explode(',', $data);
            foreach ($setArray as $key => $value) {
                $v = explode('=', $value);
                $set .= "$v[0]='$v[1]',";
            }
            $set = trim($set, ',');
        }
        $sql = "UPDATE `$table` SET $set WHERE $where";
        return $this->execute($sql);
    }

    public function delete($table, $conditions)
    {
        $where = $this->where($conditions);
        $sql = "DELETE FROM `$table` WHERE $where";
        return $this->execute($sql);
    }

    /**
     * 执行SQL查询语句
     *
     * @param string $sql            
     */
    public function query($sql)
    {
        $this->sql = $sql;
        $result = $this->db->query($sql);
        if (! $result) {
            return array();
        }
        $result->setFetchMode(PDO::FETCH_ASSOC);
        return $result->findAll();
    }

    /**
     * 执行SQL语句
     *
     * @param string $sql            
     */
    public function execute($sql)
    {
        $this->sql = $sql;
        return $this->db->exec($sql);
    }

    /**
     * 特殊字符转义
     *
     * @param string $string            
     * @return string $string
     */
    public function escape($string)
    {
        $search = array(
            '\\',
            '/',
            '"',
            "'",
            '|',
            '-',
            ';',
            '[',
            ']'
        );
        $replace = array(
            '\\\\',
            '\\/',
            '\\"',
            "\\'",
            '\\|',
            '\\-',
            '\\;',
            '\\[',
            '\\]'
        );
        return str_replace($search, $replace, $string);
    }

    /**
     * 构造SQL
     *
     * @param array $conditions            
     * @param string $split            
     * @param string $pre            
     * @return string
     */
    private function built($conditions = array(), $split = 'AND', $pre = '')
    {
        $sql = '';
        if ($pre) {
            foreach ($conditions as $k => $v) {
                $sql .= " `$pre`.`$k` = '$v' " . $split;
            }
        } else {
            foreach ($conditions as $k => $v) {
                $sql .= " `$k` = '$v' " . $split;
            }
        }
        return rtrim($sql, $split);
    }

    /**
     * 构造查询字段field
     *
     * @param array|string $field            
     * @return string
     */
    private function field($field)
    {
        if (empty($field)) {
            return '*';
        }
        if (is_array($field)) {
            $field = '`' . implode('`,`', $field) . '`';
        } else {
            $field = $field;
        }
        return $field;
    }

    /**
     * 构造条件where
     *
     * @param array|string $conditions            
     * @return string
     */
    private function where($conditions)
    {
        if (empty($conditions)) {
            $where = '1=1';
        } elseif (is_int($conditions)) {
            $where = "`$this->pk`='$conditions'";
        } elseif (is_array($conditions)) {
            $where = $this->built($conditions);
        } else {
            $where = $conditions;
        }
        return $where;
    }

    /**
     * 构造order
     *
     * @param array|string $order            
     * @return string
     */
    private function order($conditions)
    {
        $order = ' ORDER BY ';
        if (empty($conditions)) {
            $order = '';
        } elseif (is_string($conditions)) {
            $order .= $conditions;
        } else {
            $order = '';
        }
        return $order;
    }

    /**
     * 调试模式
     */
    private function debug()
    {
        if ($this->debug) {
            echo '<pre>';
            echo $this->sql;
            echo '<pre>';
        }
    }
}


/* End of file Jmysql.php */