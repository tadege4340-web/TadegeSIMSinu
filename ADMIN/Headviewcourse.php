<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php"); 
    exit();
}

$hod_department = trim($_SESSION['department'] ?? '');

// Build Query (PDO)
$sql = "SELECT course_id, course_code, course_name, credit_hour, course_year, course_semester 
        FROM addcourse 
        WHERE 1=1";

$params = [];

// Filter by department for Head
if ($_SESSION['role'] === 'head') {
    $sql .= " AND department = :dept";
    $params['dept'] = $hod_department;
}

$sql .= " ORDER BY course_year ASC, course_semester ASC, course_code ASC";

$courses = [];

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

include 'Headviewcoursef.php';
?>