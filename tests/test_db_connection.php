<?php
$serverName = "192.168.0.28, 1433"; // Host, Port
$connectionOptions = array(
    "Database" => "HFF_INTRANET_TEST_01",
    "Uid" => "sa",
    "PWD" => "Hff@sql2024"
);

// Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn) {
    echo "Connection established successfully.\n";
    sqlsrv_close($conn);
} else {
    echo "Connection could not be established.\n";
    die(print_r(sqlsrv_errors(), true));
}
?>