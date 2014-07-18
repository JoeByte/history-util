<?php

/**
 * Joracle Operation Class
 * Joracle IS USED TO OPERAT ORACLE DATABASE
 * Joracle.class.php是一个oracle数据库操作类，需要PHP的oci拓展支持
 *
 * @package     xxtime/Jmysql.php
 * @author      joe@xxtime.com
 * @link        https://github.com/thendfeel/xxtime
 * @link        http://git.oschina.net/thendfeel/xxtime
 * @example     http://dev.xxtime.com
 * @copyright   xxtime.com
 * @since       2014-06-18
 */
class Joracle
{

    public $host = 'localhost';

    public $port = 1521;

    public $dbname = '';

    public $username = '';

    public $password = '';

    public $charset = 'utf8';

    public $pk = 'id';

    public $debug = FALSE;

    private $db;

    private $sql;

    private $stmt;

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

    public function init()
    {
        $constr = $this->host . ':' . $this->port . '/' . $this->dbname;
        $this->db = oci_connect($this->username, $this->password, $constr, $this->charset);
    }

    public function find($table = '', $conditions = array(), $page = 1, $size = 20, $order = '', $field = array())
    {}

    public function findOne($table = '', $conditions = array(), $order = '', $field = array())
    {}

    public function findAll($table = '', $conditions = array(), $order = '', $field = array())
    {
        $where = $this->where($conditions);
        $field = $this->field($field);
        $order = $this->order($order);
        $sql = "SELECT " . $field . " FROM $table WHERE " . $where . $order;
        $this->executeStatements($sql);
        $result = array();
        $rows = oci_fetch_all($this->stmt, $result, 0, - 1, OCI_FETCHSTATEMENT_BY_ROW);
        return $result;
    }

    /**
     *
     * @param string $table            
     * @param array $input            
     * @param array $type=array('key'=>'date');            
     * @return boolean
     */
    public function add($table, $input = array(), $type)
    {
        $field = '';
        $value = '';
        if (! is_array($input)) {
            return FALSE;
        }
        foreach ($input as $k => $v) {
            $field .= "$k" . ',';
            if (isset($type[$k])) {
                if ($type[$k] == 'date') {
                    $value .= "to_date('$v','yyyy-mm-dd hh24:mi:ss'),";
                }
            } else {
                $value .= "'$v',";
            }
        }
        $field = trim($field, ',');
        $value = trim($value, ',');
        $sql = "INSERT INTO $table ($field) VALUES ($value)";
        return $this->execute($sql);
    }

    /**
     *
     * @param string $table            
     * @param array $input            
     * @param array $type=array('key'=>'date');            
     * @return boolean
     */
    public function addAll($table, $input = array(), $type)
    {
        $field = '';
        $value = '';
        if (! is_array($input)) {
            return FALSE;
        }
        foreach ($input as $key => $value) {
            foreach ($value as $k => $v) {
                if (isset($type[$k])) {
                    if ($type[$k] == 'date') {
                        $value .= "to_date('$v','yyyy-mm-dd hh24:mi:ss'),";
                    }
                } else {
                    $value .= "'$v',";
                }
            }
        }
        $field = array_keys(array_pop($input));
        print_r($field);
        exit();
        $field = trim($field, ',');
        $value = trim($value, ',');
        $sql = "INSERT INTO $table ($field) VALUES ($value)";
        return $this->execute($sql);
    }

    public function update($table, $conditions, $data)
    {}

    public function delete($table, $conditions)
    {}

    public function query($sql)
    {
        $result = array();
        $this->executeStatements($sql);
        $rows = oci_fetch_all($this->stmt, $result, 0, - 1, OCI_FETCHSTATEMENT_BY_ROW);
        return $result;
    }

    public function execute($sql)
    {
        $this->executeStatements($sql);
        return oci_commit($this->db);
    }

    private function executeStatements($sql)
    {
        $this->sql = $sql;
        $this->stmt = oci_parse($this->db, $sql);
        return oci_execute($this->stmt);
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
                $sql .= " $pre.$k = '$v' " . $split;
            }
        } else {
            foreach ($conditions as $k => $v) {
                $sql .= " $k = '$v' " . $split;
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
            $field = '' . implode(',', $field) . '';
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
            $where = "$this->pk='$conditions'";
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