<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $config = require CONFIG_DIR . '/database.php';
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['db']};charset={$config['charset']}";
            
            try {
                self::$instance = new PDO($dsn, $config['user'], $config['pass'], $config['options']);
            } catch (PDOException $e) {
                // If database doesn't exist yet, attempt connecting without dbname
                try {
                    $dsnNoDb = "mysql:host={$config['host']};port={$config['port']};charset={$config['charset']}";
                    $pdo = new PDO($dsnNoDb, $config['user'], $config['pass'], $config['options']);
                    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['db']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                    self::$instance = new PDO($dsn, $config['user'], $config['pass'], $config['options']);
                } catch (PDOException $ex) {
                    die("Database Connection Error: " . $ex->getMessage());
                }
            }
        }
        return self::$instance;
    }
}
