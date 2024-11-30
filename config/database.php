<?php
// config/database.php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Define database configurations based on the environment
$env = $_ENV['APP_ENV'] ?? 'production';
$isDevelopment = $env === 'development';

return [
    'host' => $isDevelopment ? $_ENV['DB_HOST'] : $_ENV['PROD_DB_HOST'],
    'port' => $isDevelopment ? $_ENV['DB_PORT'] : $_ENV['PROD_DB_PORT'],
    'name' => $isDevelopment ? $_ENV['DB_NAME'] : $_ENV['PROD_DB_NAME'],
    'user' => $isDevelopment ? $_ENV['DB_USER'] : $_ENV['PROD_DB_USER'],
    'pass' => $isDevelopment ? $_ENV['DB_PASS'] : $_ENV['PROD_DB_PASS'],
];