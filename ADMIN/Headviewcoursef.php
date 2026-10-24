<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php"); 
    exit();
}

$hod_department = trim($_SESSION['department'] ?? '');
$hod_college    = trim($_SESSION['college'] ?? '');

// 2. Fetch Courses Query (PDO)
$sql = "SELECT course_code, course_name, credit_hour, course_year, course_semester 
        FROM addcourse 
        WHERE 1=1";

$params = [];

// Filter by department if the user is a Head
if ($_SESSION['role'] !== 'admin' && $hod_department !== '') {
    $sql .= " AND department = :dept";
    $params['dept'] = $hod_department;
}

$sql .= " ORDER BY course_code ASC";

$courses = [];

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

// 3. Load the HTML View
include 'Headviewcoursef.php';
?>