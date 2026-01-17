<?php
class Database {
    private static $instance = null;
    private $mysqli;
    
    private function __construct() {
        $this->mysqli = new mysqli(
            'db',  // Docker service name
            'root',
            'root',
            'events_db'
        );
        
        if ($this->mysqli->connect_error) {
            die('Connection failed: ' . $this->mysqli->connect_error);
        }
        
        // Set charset to UTF-8
        $this->mysqli->set_charset("utf8mb4");
        
        // Set SQL mode for UTF-8 queries
        $this->mysqli->query("SET NAMES utf8mb4");
        $this->mysqli->query("SET CHARACTER SET utf8mb4");
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->mysqli;
    }
    
    public function prepare($sql) {
        return $this->mysqli->prepare($sql);
    }
    
    public function query($sql) {
        return $this->mysqli->query($sql);
    }
    
    public function escape($string) {
        return $this->mysqli->real_escape_string($string);
    }
    
    public function getLastInsertId() {
        return $this->mysqli->insert_id;
    }
    
    public function close() {
        if ($this->mysqli) {
            $this->mysqli->close();
        }
    }
}