<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php"); exit();
}

$hod_department = trim($_SESSION['department'] ?? '');

// Get Filters (Student ID is now optional)
$student_id = $_GET['student_id'] ?? '';
$filter_year = $_GET['year'] ?? '';
$filter_semester = $_GET['semester'] ?? '';

$students_grades = [];

// Only run the query if at least one filter is selected
if ($student_id !== '' || $filter_year !== '' || $filter_semester !== '') {
    
    // Querying student_courses directly since that's where the grades are
    $sql = "SELECT sc.student_id, u.firstname, u.father_name, 
                   c.course_code, c.course_name, c.credit_hour, c.course_year, sc.semester, 
                   sc.grade, sc.grade_point, sc.status 
            FROM student_courses sc
            LEFT JOIN users2 u ON sc.student_id = u.student_id
            LEFT JOIN addcourse c ON sc.course_code = c.course_code
            WHERE 1=1";
            
    $params = [];
    $types = "";

    // Apply Department Filter for Head (assuming Head only sees their dept)
    if ($_SESSION['role'] === 'head' && $hod_department !== '') {
        $sql .= " AND u.department = ?";
        $params[] = $hod_department;
        $types .= "s";
    }

    // Apply Student ID Filter (Optional)
    if ($student_id !== '') {
        $sql .= " AND sc.student_id = ?";
        $params[] = $student_id;
        $types .= "s";
    }

    // Apply Year Filter (using course_year from addcourse)
    if ($filter_year !== '') {
        $sql .= " AND c.course_year = ?";
        $params[] = $filter_year;
        $types .= "i";
    }

    // Apply Semester Filter (using semester from student_courses)
    if ($filter_semester !== '') {
        $sql .= " AND sc.semester = ?";
        $params[] = $filter_semester;
        $types .= "i";
    }

    $sql .= " ORDER BY sc.student_id ASC, c.course_year ASC, sc.semester ASC, c.course_code ASC";
            
    $stmt = $conn->prepare($sql);
    if ($types !== '') {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    
    while ($row = $res->fetch_assoc()) {
        $students_grades[] = $row;
    }
    $stmt->close();
}

include 'Headviewgradef.php';
?>