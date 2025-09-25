<?php
class Database {
    private $host = "db";
    private $user = "user";
    private $pass = "userpassword";
    private $port = 3306;
    private $dbname = "mydb";
    private static $instance = null;
    public $conn;

    private function __construct() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, "", $this->port);

        if ($this->conn->connect_error) {
            die("Error de conexión (" . $this->conn->connect_errno . "): " . $this->conn->connect_error);
        }

        if (!$this->conn->select_db($this->dbname)) {
            $sql = "CREATE DATABASE `{$this->dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            if (!$this->conn->query($sql)) {
                die("Error al crear la base de datos: " . $this->conn->error);
            }
            $this->conn->select_db($this->dbname);
        } else {
            $this->conn->select_db($this->dbname);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->conn;
    }
}
