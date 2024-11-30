<?php
require_once 'vendor/autoload.php';

use PowerLite\PDO\Connection;

function createDatabase($dbName, $host = 'localhost', $username = 'root', $password = '') {
    try {
        // Connect to MySQL server
        $pdo = new PDO("mysql:host=$host", $username, $password);

        // Create database if it doesn't exist
        $sql = "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $pdo->exec($sql);
        echo "Database '$dbName' checked/created successfully." . PHP_EOL;

    } catch (PDOException $e) {
        echo "Database creation error: " . $e->getMessage() . PHP_EOL;
        exit(1);
    }
}

// Example usage
createDatabase('my_new_project');