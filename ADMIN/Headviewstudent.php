<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php"); exit();
}

$hod_department = trim($_SESSION['department'] ?? '');
$hod_college    = trim($_SESSION['college'] ?? '');

// Get Filters
$filter_year  = $_GET['year'] ?? '';
$filter_semester = $_GET['semester'] ?? ''; // 🔥 ADDED
$search_query = trim($_GET['search'] ?? '');

// Build Query
$sql = "SELECT student_id, firstname, father_name, gender, year, current_semester 
        FROM users2 
        WHERE 1=1";
$params = [];
$types  = "";

// Filter by Department for Head
if ($_SESSION['role'] !== 'admin' && $hod_department !== '') {
    $sql .= " AND department = ?";
    $params[] = $hod_department;
    $types .= "s";
}

// Filter by Year
if ($filter_year !== '') {
    $sql .= " AND year = ?";
    $params[] = $filter_year;
    $types .= "i";
}

// 🔥 Filter by Semester
if ($filter_semester !== '') {
    $sql .= " AND current_semester = ?";
    $params[] = $filter_semester;
    $types .= "i";
}

// Search
if ($search_query !== '') {
    $sql .= " AND (student_id LIKE ? OR firstname LIKE ? OR father_name LIKE ?)";
    $search_like = "%$search_query%";
    $params[] = $search_like;
    $params[] = $search_like;
    $params[] = $search_like;
    $types .= "sss";
}

$sql .= " ORDER BY year ASC, firstname ASC";

$stmt = $conn->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
$stmt->close();

include 'Headviewstudentf.php';
?>