<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "mysql-6dee70c-tadege4340-a4ab.e.aivencloud.com";
$user = "avnadmin";
$pass = getenv('DB_PASSWORD'); 
$db   = "defaultdb";
$port = 15670;

// 1. Create the connection FIRST
$conn = new mysqli($host, $user, $pass, $db, $port);

// 2. Then set the SSL certificate
mysqli_ssl_set($conn, NULL, NULL, '/var/www/html/ca.pem', NULL, NULL);

// 3. Check for errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>