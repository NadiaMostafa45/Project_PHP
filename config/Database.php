<?php

namespace Config;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $conn;

    private $host = "localhost";
    private $db_name = "cafeteria";
    private $username = "root";
    private $password = "";

    private function __construct()
    {
        try {

            $this->conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");

            $checkDb = $this->conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$this->db_name}'");

            if ($checkDb->fetchColumn() == 0) {

                $this->conn->exec("CREATE DATABASE IF NOT EXISTS `{$this->db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $this->conn->exec("USE `{$this->db_name}`");

                $sqlFile = __DIR__ . '/database_setup.sql';
                if (file_exists($sqlFile)) {
                    $sql = file_get_contents($sqlFile);
                    $this->conn->exec($sql);
                }
            } else {
                $this->conn->exec("USE `{$this->db_name}`");
            }

            $this->ensureProductsSchema();
        } catch (PDOException $e) {
            die("❌ Connection Error: " . $e->getMessage());
        }
    }

    private function ensureProductsSchema()
    {
        $tableExistsStmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'products'"
        );
        $tableExistsStmt->execute([$this->db_name]);

        if ((int)$tableExistsStmt->fetchColumn() === 0) {
            return;
        }

        $columnExistsStmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'products' AND COLUMN_NAME = 'available'"
        );
        $columnExistsStmt->execute([$this->db_name]);

        if ((int)$columnExistsStmt->fetchColumn() === 0) {
            $this->conn->exec("ALTER TABLE products ADD COLUMN available TINYINT(1) NOT NULL DEFAULT 1");
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }

    private function __clone() {}
}
