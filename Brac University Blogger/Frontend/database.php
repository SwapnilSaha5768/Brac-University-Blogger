<?php

$hostName = getenv("DB_HOST") ?: "localhost";
$dbUser = getenv("DB_USER") ?: "root";
$dbPassword = getenv("DB_PASSWORD") ?: "";
$dbName = getenv("DB_NAME") ?: "bracuniversityblogger";
$dbPort = getenv("DB_PORT") ?: 3306;

$conn = mysqli_init();
if (!$conn) {
    die("mysqli_init failed");
}

// Automatically use SSL, disable strict cert verification for ease of deployment
mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// Try connecting with SSL
if (!mysqli_real_connect($conn, $hostName, $dbUser, $dbPassword, $dbName, $dbPort, NULL, MYSQLI_CLIENT_SSL)) {
    // If failed and we are local, try without SSL
    if ($hostName === 'localhost') {
        $conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName, $dbPort);
    } 
    
    if (!$conn) {
        die("Connect Error: " . mysqli_connect_error());
    }
}

?>