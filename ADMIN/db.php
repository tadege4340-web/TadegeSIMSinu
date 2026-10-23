<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "mysql-6dee70c-tadege4340-a4ab.e.aivencloud.com";
$user = "avnadmin";
$pass = getenv('DB_PASSWORD'); 
$db   = "defaultdb";
$port = 15670;

// Use PDO with SSL verification (This is the correct way for Aiven)
$dsn = "mysql:host=$host;port=$port;dbname=$db;sslmode=verify-ca;sslrootcert=/var/www/html/ca.pem";

try {
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>