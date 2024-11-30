<?php
function checkRequirements() {
    $requirements = [
        'php_version' => '7.4',
        'extensions' => ['pdo', 'pdo_mysql'],
        'libraries' => [
            'migliori/power-lite-pdo' => 'PowerLite\\PDO',
            'twig/twig' => 'Twig\\Environment'
        ],
    ];

    // Check PHP version
    if (version_compare(PHP_VERSION, $requirements['php_version'], '<')) {
        echo "PHP version must be {$requirements['php_version']} or higher. Current version: " . PHP_VERSION . PHP_EOL;
        exit(1);
    }
    echo "PHP version is compatible." . PHP_EOL;

    // Check required extensions
    foreach ($requirements['extensions'] as $extension) {
        if (!extension_loaded($extension)) {
            echo "Extension $extension is required but not installed." . PHP_EOL;
            exit(1);
        }
    }
    echo "All required PHP extensions are installed." . PHP_EOL;

    // Check if libraries are installed
    foreach ($requirements['libraries'] as $package => $namespace) {
        if (!class_exists($namespace)) {
            echo "Library $package is missing. Try running `composer require $package`." . PHP_EOL;
            exit(1);
        }
    }
    echo "All required libraries are installed." . PHP_EOL;
}

checkRequirements();