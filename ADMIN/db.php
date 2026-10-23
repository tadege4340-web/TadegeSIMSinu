<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "mysql-6dee70c-tadege4340-a4ab.e.aivencloud.com";
$user = "avnadmin";
$pass = getenv('DB_PASSWORD'); // The password will be loaded from Render securely
$db   = "defaultdb";
$port = 15670;

// Aiven requires an SSL connection. 
mysqli_ssl_set($conn, NULL, NULL, '/var/www/html/ca.pem', NULL, NULL);

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>