<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php"); 
    exit();
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

try {
    // Total Students in Department
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users2 WHERE department = :dept");
    $stmt->execute(['dept' => $hod_department]);
    $stats['students'] = (int)$stmt->fetchColumn();

    // Total Teachers in Department
    $stmt = $conn->prepare("SELECT COUNT(*) FROM teachers WHERE department = :dept");
    $stmt->execute(['dept' => $hod_department]);
    $stats['teachers'] = (int)$stmt->fetchColumn();

    // Total Courses in Department
    $stmt = $conn->prepare("SELECT COUNT(*) FROM addcourse WHERE department = :dept");
    $stmt->execute(['dept' => $hod_department]);
    $stats['courses'] = (int)$stmt->fetchColumn();

    // Pending Grades for Department
    $stmt = $conn->prepare("
        SELECT COUNT(sc.id) 
        FROM student_courses sc 
        JOIN addcourse c ON sc.course_code = c.course_code 
        WHERE c.department = :dept AND sc.grade_status = 'Pending'
    ");
    $stmt->execute(['dept' => $hod_department]);
    $stats['pending_grades'] = (int)$stmt->fetchColumn();

} catch (PDOException $e) {
    // If there's a database error, stats will stay at 0
    $db_error = $e->getMessage();
}

// 3. Load the View
include 'Headdashboardf.php';
?>