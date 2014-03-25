<?php

/**
 * Jmysql Operation Class
 * Jmysql IS USED TO OPERAT MYSQL
 * 需要PHP的PDO拓展支持
 *
 * @package     xxtime/Jmysql.class
 * @author      joe@xxtime.com
 * @link        https://github.com/thendfeel/xxtime
 * @example     http://dev.xxtime.com
 * @copyright   xxtime.com
 * @created     2014-02-26
 */

/*
$config = array(
    'host' => '127.0.0.1',
    'port' => '3306',
    'dbname' => 'db_test',
    'user' => 'root',
    'password' => '123456'
);
*/
// $db = new Jmysql($config);
// USE EXAMPLE NO.1 # FIND
// $ret1 = $db->fetchAll('user', array('id' => 1), 'id, name');
// $ret2 = $db->fetchAll('user', array('name' => 'Joe'));
// $ret3 = $db->fetchOne('user', array('id' => 1), array('id', 'name'));
// $ret4 = $db->fetchOne('user', 'id = 1', array('id', 'name'));

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

    private $db;

    public function __construct($config = array())
    {
        foreach ($config as $key => $value) {
            $this->$key = $value;
        }
        $this->init();
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
     * 执行SQL查询语句 返回一条数据
     *
     * @param string $table            
     * @param array|string $conditions            
     * @param array|string $field            
     */
    public function fetchOne($table = '', $conditions = array(), $field = array())
    {
        $where = $this->where($conditions);
        $field = $this->field($field);
        $sql = "SELECT " . $field . " FROM `$table` WHERE " . $where;
        $result = $this->db->query($sql);
        $result->setFetchMode(PDO::FETCH_ASSOC);
        return $result->fetch();
    }

    /**
     * 执行SQL查询语句 返回多条数据
     *
     * @param string $table            
     * @param array|string $conditions            
     * @param array|string $field            
     */
    public function fetchAll($table = '', $conditions = array(), $field = array())
    {
        $where = $this->where($conditions);
        $field = $this->field($field);
        $sql = "SELECT " . $field . " FROM `$table` WHERE " . $where;
        return $this->query($sql);
    }

    public function insert($table, $input = array())
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
        $result = $this->db->query($sql);
        $result->setFetchMode(PDO::FETCH_ASSOC);
        return $result->fetchAll();
    }

    /**
     * 执行SQL语句
     *
     * @param string $sql            
     */
    public function execute($sql)
    {
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
        if (is_int($conditions)) {
            $where = "`$this->pk`='$conditions'";
        } elseif (is_array($conditions)) {
            $where = $this->built($conditions);
        } else {
            $where = $conditions;
        }
        return $where;
    }
}


/* End of file Jmysql.php */