<?php

/**
 * Package  pdoClass.php
 * Author:  joe@xxtime.com
 * Date:    2015-08-02
 * Time:    下午9:13
 * Link:    http://www.xxtime.com
 */
class pdo_class
{
    private $db;
    private $host;
    private $port = 3306;
    private $charset = 'utf8';
    private $database;
    private $username;
    private $password;


    function pdo_class($config = array())
    {
        foreach ($config as $key => $value) {
            $this->{$key} = $value;
        }
        $dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->database;
        try {
            $this->db = new PDO($dsn, $this->username, $this->password);
            $this->db->query('set names ' . $this->charset);
        } catch (PDOException $e) {
            exit('database init error');
            return false;
        }
    }


    function fetchOne($sql = '')
    {
        $query = $this->db->query($sql);
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $result = $query->fetch();
        return $result;
    }


    function fetchAll($sql = '')
    {
        $query = $this->db->query($sql);
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $result = $query->fetchAll();
        return $result;
    }


    function execute($sql = '')
    {
        return $this->db->exec($sql);
    }
}