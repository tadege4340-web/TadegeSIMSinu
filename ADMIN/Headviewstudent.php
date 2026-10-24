<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php"); exit();
}

$hod_department = trim($_SESSION['department'] ?? '');
$hod_college    = trim($_SESSION['college'] ?? '');

// Get Filters
$filter_year     = $_GET['year'] ?? '';
$filter_semester = $_GET['semester'] ?? ''; 
$search_query    = trim($_GET['search'] ?? '');

// Build Query (PDO)
$sql = "SELECT student_id, firstname, father_name, gender, `year`, current_semester 
        FROM users2 
        WHERE 1=1";
        
$params = [];

// Filter by Department for Head
if ($_SESSION['role'] !== 'admin' && $hod_department !== '') {
    $sql .= " AND department = :dept";
    $params['dept'] = $hod_department;
}

// Filter by Year
if ($filter_year !== '') {
    $sql .= " AND `year` = :year";
    $params['year'] = $filter_year;
}

// Filter by Semester
if ($filter_semester !== '') {
    $sql .= " AND current_semester = :sem";
    $params['sem'] = $filter_semester;
}

// Search
if ($search_query !== '') {
    $sql .= " AND (student_id LIKE :search1 OR firstname LIKE :search2 OR father_name LIKE :search3)";
    $search_like = "%$search_query%";
    $params['search1'] = $search_like;
    $params['search2'] = $search_like;
    $params['search3'] = $search_like;
}

$sql .= " ORDER BY `year` ASC, firstname ASC";

$students = [];

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

// Load the View
include 'Headviewstudentf.php';
?>