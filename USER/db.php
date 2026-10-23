<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ... rest of your db.php below
$host = "localhost";
$user = "root";
$pass = "root";
$db   = "lab";
$port = 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>