<?php
// FIX: Allow session cookies to work inside iframes (required for Render)
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,      // Must be true for SameSite=None (Render uses HTTPS)
    'httponly' => true,
    'samesite' => 'None'   // Critical: allows the cookie to be sent inside the iframe
]);

if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php"); 
    exit();
}

$hod_department = trim($_SESSION['department'] ?? '');

// 2. Get Filters (Student ID is optional)
$student_id      = trim($_GET['student_id'] ?? '');
$filter_year     = trim($_GET['year'] ?? '');
$filter_semester = trim($_GET['semester'] ?? '');

$students_grades = [];

// 3. Only run the query if at least one filter is selected
if ($student_id !== '' || $filter_year !== '' || $filter_semester !== '') {
    
    // Querying student_courses directly where the grades are
    $sql = "SELECT sc.student_id, u.firstname, u.father_name, 
                   c.course_code, c.course_name, c.credit_hour, c.course_year, sc.semester, 
                   sc.grade, sc.grade_point, sc.status 
            FROM student_courses sc
            LEFT JOIN users2 u ON sc.student_id = u.student_id
            LEFT JOIN addcourse c ON sc.course_code = c.course_code
            WHERE 1=1";
            
    $params = [];

    // Apply Department Filter for Head
    if ($_SESSION['role'] === 'head' && $hod_department !== '') {
        $sql .= " AND u.department = :dept";
        $params['dept'] = $hod_department;
    }

    // Apply Student ID Filter (Optional)
    if ($student_id !== '') {
        $sql .= " AND sc.student_id = :sid";
        $params['sid'] = $student_id;
    }

    // Apply Year Filter (using course_year from addcourse)
    if ($filter_year !== '') {
        $sql .= " AND c.course_year = :year";
        $params['year'] = $filter_year;
    }

    // Apply Semester Filter (using semester from student_courses)
    if ($filter_semester !== '') {
        $sql .= " AND sc.semester = :sem";
        $params['sem'] = $filter_semester;
    }

    $sql .= " ORDER BY sc.student_id ASC, c.course_year ASC, sc.semester ASC, c.course_code ASC";
            
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $students_grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $db_error = $e->getMessage();
    }
}

// 4. Load the View
include 'Headviewgradef.php';
?>