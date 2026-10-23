<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load your actual database connection file
require 'db.php'; 

if ($conn) {
    echo "<h1>Database connected successfully!</h1>";
} else {
    echo "<h1>Database connection failed.</h1>";
}
?>