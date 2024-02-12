<?php

namespace config;

use PDO;
use PDOException;

class Database
{
    private $host = 'localhost';
    private $db_name = 'crud-pdo-php';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection()
    {
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");

            return $this->conn;
        } catch (PDOException $exception) {
            die("Error de conexión: " . $exception->getMessage());
        }
    }

    public function closeConnection()
    {
        $this->conn = null;
    }

    public function setCredentials($host, $db_name, $username, $password)
    {
        $this->host = $host;
        $this->db_name = $db_name;
        $this->username = $username;
        $this->password = $password;
    }
}
