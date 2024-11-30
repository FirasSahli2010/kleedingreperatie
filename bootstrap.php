<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use PowerLite\PDO\Connection;

// Example usage for database connection
function createDatabaseDevelopment() {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    // Validation: ensure required environment variables are set
    // , 'DB_PASS'
    $dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER'])->notEmpty();

    if ($_ENV['APP_DEBUG'] === 'true') {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
    }

    $appName = $_ENV['APP_NAME'];

    $dbName = $_ENV['DB_NAME'];
    $host = $_ENV['DB_HOST'];
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASS'];

    try {
        $pdo = new PDO("mysql:host=$host", $username, $password);
        

        $sql = "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $pdo->exec($sql);
        echo "Database '$dbName' checked/created successfully." . PHP_EOL;
        echo "Welcome to " . $appName . PHP_EOL;

        // Check database connection
        $pdo = connectDatabaseDevelopment();

        // Create users table and insert admin user
        createUsersTable($pdo);
        insertDefaultAdmin($pdo);
    } catch (PDOException $e) {
        echo "Database creation error: " . $e->getMessage() . PHP_EOL;
        exit(1);
    }
}

function createDatabaseProduction() {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    // Validation: ensure required environment variables are set
    // , 'DB_PASS'
    $dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER'])->notEmpty();

    if ($_ENV['APP_DEBUG'] === 'true') {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
    }

    $appName = $_ENV['APP_NAME'];

    $dbName = $_ENV['DB_NAME_PROD'];
    $host = $_ENV['DB_HOST_PROD'];
    $username = $_ENV['DB_USER_PROD'];
    $password = $_ENV['DB_PASS_PROD'];

    try {
        $pdo = new PDO("mysql:host=$host", $username, $password);
        

        $sql = "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $pdo->exec($sql);
        echo "Database '$dbName' checked/created successfully." . PHP_EOL;
        echo "Welcome to " . $appName . PHP_EOL;

        // Check database connection
        $pdo = connectDatabaseProduction();
        // Create users table and insert admin user
        createUsersTable($pdo);
        insertDefaultAdmin($pdo);
    } catch (PDOException $e) {
        echo "Database creation error: " . $e->getMessage() . PHP_EOL;
        exit(1);
    }
}

function connectDatabaseDevelopment() {
    $host = $_ENV['DB_HOST'];
    $dbName = $_ENV['DB_NAME'];
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASS'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
        return $pdo;
    } catch (PDOException $e) {
        echo "Connection error: " . $e->getMessage() . PHP_EOL;
        exit(1);
    }
}

function connectDatabaseProduction() {
    $host = $_ENV['DB_HOST_PROD'];
    $dbName = $_ENV['DB_NAME_PROD'];
    $username = $_ENV['DB_USER_PROD'];
    $password = $_ENV['DB_PASS_PROD'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
        return $pdo;
    } catch (PDOException $e) {
        echo "Connection error: " . $e->getMessage() . PHP_EOL;
        exit(1);
    }
}

// Function to create users table
function createUsersTable($pdo) {
    $tableSQL = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            user_level ENUM('admin', 'user') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=INNODB;
    ";
    $pdo->exec($tableSQL);
    echo "Users table created or already exists." . PHP_EOL;
}

// Function to insert a default admin user
function insertDefaultAdmin($pdo) {
    $adminUsername = 'admin';
    $adminPassword = '1234';
    $adminLevel = 'admin';

    // Check if the admin user already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $stmt->execute(['username' => $adminUsername]);
    $exists = $stmt->fetchColumn();

    if (!$exists) {
        // Hash the password
        $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

        // Insert the admin user
        $insertSQL = "INSERT INTO users (username, password, user_level, email) VALUES (:username, :password, :user_level, 'admin@atiler.nl')";
        $stmt = $pdo->prepare($insertSQL);
        $stmt->execute([
            'username' => $adminUsername,
            'password' => $hashedPassword,
            'user_level' => $adminLevel,
        ]);
        echo "Default admin user created." . PHP_EOL;
    } else {
        echo "Admin user already exists." . PHP_EOL;
    }
}

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$appEnv = $_ENV['APP_ENV'];
$isDevelopment = $appEnv === 'development';


if ($isDevelopment && $_ENV['APP_DEBUG'] === 'true') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
}
if ($isDevelopment) {
//  'DB_PASS'
    $dotenv->required(['APP_ENV', 'DB_HOST', 'DB_NAME', 'DB_USER'])->notEmpty();
} else {
    $dotenv->required(['APP_ENV_PROD', 'DB_HOS_PRODT', 'DB_NAME_PROD', 'DB_USER_PROD', 'DB_PASS_PROD'])->notEmpty();
}



// Using the app name from .env
echo "Welcome to " . $_ENV['APP_NAME'] . PHP_EOL;
// Run the database creation function
if ($isDevelopment) {
    createDatabaseDevelopment();
    echo "Using development environment settings." . PHP_EOL;
} else {
    $host = $_ENV['DB_HOST_PROD'];
    $dbName = $_ENV['DB_NAME_PROD'];
    $username = $_ENV['DB_USER_PROD'];
    $password = $_ENV['DB_PASS_PROD'];
    createDatabaseProduction();
    echo "Using production environment settings." . PHP_EOL;
}
echo "Database connected successfully." . PHP_EOL;