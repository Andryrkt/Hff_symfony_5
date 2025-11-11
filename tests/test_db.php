<?php

require __DIR__.'/../vendor/autoload.php';

use Doctrine\DBAL\DriverManager;

$connectionParams = [
    'url' => 'sqlsrv://sa:Hff%40sql2024@192.168.0.28:1433/HFF_INTRANET_TEST_01?CharacterSet=UTF-8',
];

try {
    $conn = DriverManager::getConnection($connectionParams);
    $conn->connect();
    
    $sql = 'SELECT * FROM dom_sous_type_document WHERE id = 82';
    $stmt = $conn->prepare($sql);
    $result = $stmt->executeQuery();
    $row = $result->fetchAssociative();

    if ($row) {
        echo "\nRow found: " . print_r($row, true);
    } else {
        echo "\nRow with id 82 not found.";
    }

} catch (
Exception $e) {
    echo "Failed to connect: " . $e->getMessage();
}