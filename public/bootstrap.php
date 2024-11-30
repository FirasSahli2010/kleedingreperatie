<?php
// Load configuration
$dbConfig = require __DIR__ . '/../config/database.php';

try {
    // Establish a connection to the database server
    $pdo = new PDO(
        "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['name']}",
        $dbConfig['user'],
        $dbConfig['pass']
    );

    //echo "Database connection established." . PHP_EOL;
} catch (PDOException $e) {
    echo "Database connection error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

// Make $pdo available globally or use dependency injection
$GLOBALS['pdo'] = $pdo;