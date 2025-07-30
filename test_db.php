<?php

require __DIR__.'/vendor/autoload.php';

use Doctrine\DBAL\DriverManager;

$connectionParams = [
    'url' => getenv('DATABASE_URL'),
];

try {
    $conn = DriverManager::getConnection($connectionParams);
    $conn->connect();
    echo "Connected successfully";
} catch (\Exception $e) {
    echo "Failed to connect: " . $e->getMessage();
}