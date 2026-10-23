<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php"); exit();
}

$hod_department = trim($_SESSION['department'] ?? '');

$sql = "SELECT course_id, course_code, course_name, credit_hour, course_year, course_semester FROM addcourse WHERE 1=1";
$params = []; $types = "";

if ($_SESSION['role'] === 'head') {
    $sql .= " AND department = ?";
    $params[] = $hod_department;
    $types .= "s";
}
$sql .= " ORDER BY course_year ASC, course_semester ASC, course_code ASC";

$stmt = $conn->prepare($sql);
if ($types !== '') { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) { $courses[] = $row; }
$stmt->close();

include 'Headviewcoursef.php';
?>