<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php"); exit();
}

$hod_department = trim($_SESSION['department'] ?? '');
$hod_college    = trim($_SESSION['college'] ?? '');

// 2. Fetch Statistics
$stats = [
    'students' => 0,
    'teachers' => 0,
    'courses'  => 0,
    'pending_grades' => 0
];

// Total Students in Department
$stmt = $conn->prepare("SELECT COUNT(*) AS c FROM users2 WHERE department = ?");
$stmt->bind_param("s", $hod_department);
$stmt->execute();
$stats['students'] = $stmt->get_result()->fetch_assoc()['c'];
$stmt->close();

// Total Teachers in Department
$stmt = $conn->prepare("SELECT COUNT(*) AS c FROM teachers WHERE department = ?");
$stmt->bind_param("s", $hod_department);
$stmt->execute();
$stats['teachers'] = $stmt->get_result()->fetch_assoc()['c'];
$stmt->close();

// Total Courses in Department
$stmt = $conn->prepare("SELECT COUNT(*) AS c FROM addcourse WHERE department = ?");
$stmt->bind_param("s", $hod_department);
$stmt->execute();
$stats['courses'] = $stmt->get_result()->fetch_assoc()['c'];
$stmt->close();

// Pending Grades for Department
$stmt = $conn->prepare("
    SELECT COUNT(sc.id) AS c 
    FROM student_courses sc 
    JOIN addcourse c ON sc.course_code = c.course_code 
    WHERE c.department = ? AND sc.grade_status = 'Pending'
");
$stmt->bind_param("s", $hod_department);
$stmt->execute();
$stats['pending_grades'] = $stmt->get_result()->fetch_assoc()['c'] ?? 0;
$stmt->close();

// 3. Load the View
include 'Headdashboardf.php';
?>