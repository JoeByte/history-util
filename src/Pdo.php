<?php
namespace Xxtime;

class Pdo
{
    private $db;
    private $host;
    private $port = 3306;
    private $charset = 'utf8';
    private $database;
    private $username;
    private $password;

    function Pdo($config = array())
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

    function lastInsertId()
    {
        return $this->db->lastInsertId();
    }
}