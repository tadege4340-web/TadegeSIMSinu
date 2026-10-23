<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php"); exit();
}

$hod_department = trim($_SESSION['department'] ?? '');
$hod_college    = trim($_SESSION['college'] ?? '');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_code = trim($_POST['course_code'] ?? '');
    $course_name = trim($_POST['course_name'] ?? '');
    $credit_hour = trim($_POST['credit_hour'] ?? '');
    $course_year = trim($_POST['course_year'] ?? '');
    $course_semester = trim($_POST['course_semester'] ?? '1'); 
    
    $category = "Major Course";
    
    if (!empty($course_code) && !empty($course_name)) {
        $sql = "INSERT INTO addcourse (course_code, course_name, credit_hour, category, college, department, course_year, course_semester) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssissssi", $course_code, $course_name, $credit_hour, $category, $hod_college, $hod_department, $course_year, $course_semester);
        
        if ($stmt->execute()) {
            $message = "<div style='color: green; margin-bottom: 15px; font-weight: bold;'>✅ Course added successfully!</div>";
        } else {
            $message = "<div style='color: red; margin-bottom: 15px; font-weight: bold;'>❌ Error: Could not add course. " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        $message = "<div style='color: red; margin-bottom: 15px; font-weight: bold;'>❌ Please fill in all required fields.</div>";
    }
}

include 'Headcourseaddf.php';
?>