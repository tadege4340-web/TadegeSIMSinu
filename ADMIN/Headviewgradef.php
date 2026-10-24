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

// 2. Get Filters
$student_id      = trim($_GET['student_id'] ?? '');
$filter_year     = $_GET['year'] ?? '';
$filter_semester = $_GET['semester'] ?? '';

// 3. Build Query (PDO)
$sql = "SELECT 
            sc.student_id, 
            sc.course_code, 
            sc.semester,
            sc.grade, 
            sc.grade_point, 
            sc.status,
            u.firstname, 
            u.father_name,
            a.course_name, 
            a.credit_hour,
            a.course_year
        FROM student_courses sc
        INNER JOIN users2 u ON sc.student_id = u.student_id
        LEFT JOIN addcourse a ON sc.course_code = a.course_code
        WHERE 1=1";

$params = [];

// Filter by Department for Head
if ($_SESSION['role'] !== 'admin' && $hod_department !== '') {
    $sql .= " AND u.department = :dept";
    $params['dept'] = $hod_department;
}

// Filter by specific Student ID
if ($student_id !== '') {
    $sql .= " AND sc.student_id = :sid";
    $params['sid'] = $student_id;
}

// Filter by Year
if ($filter_year !== '') {
    $sql .= " AND a.course_year = :year";
    $params['year'] = $filter_year;
}

// Filter by Semester
if ($filter_semester !== '') {
    $sql .= " AND sc.semester = :sem";
    $params['sem'] = $filter_semester;
}

$sql .= " ORDER BY sc.student_id ASC, a.course_year ASC, sc.semester ASC, sc.course_code ASC";

$students_grades = [];

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $students_grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

// 4. Load the View
include 'Headviewgradef.php';
?>