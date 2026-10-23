<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php"); exit();
}

$hod_department = trim($_SESSION['department'] ?? '');
$message = ''; $message_type = 'success';

$filter_year = $_GET['year'] ?? '';
$filter_semester = $_GET['semester'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign'])) {
    $teacher_ids = $_POST['teacher_ids'] ?? []; 
    $course_ids = $_POST['course_ids'] ?? [];
    
    if (!empty($teacher_ids) && !empty($course_ids)) {
        $success_count = 0; $error_count = 0;
        
        foreach ($teacher_ids as $t_id) {
            foreach ($course_ids as $c_id) {
                
                // Fetch the exact course details first
                $stmt_c = $conn->prepare("SELECT course_code, course_semester FROM addcourse WHERE course_id = ?");
                $stmt_c->bind_param("i", $c_id);
                $stmt_c->execute();
                $course_info = $stmt_c->get_result()->fetch_assoc();
                $stmt_c->close();
                
                $course_code = $course_info['course_code'] ?? '';
                $course_sem = $course_info['course_semester'] ?? $filter_semester;

                // Save to database
                $sql = "INSERT IGNORE INTO teacher_courses (teacher_id, course_code, course_id, ac_year, semester, role) 
                        VALUES (?, ?, ?, '2024/2025', ?, 'Lecturer')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isii", $t_id, $course_code, $c_id, $course_sem);
                
                if (@$stmt->execute()) { 
                    $success_count++; 
                } else { 
                    $error_count++; 
                }
                $stmt->close();
            }
        }
        $message = "✅ Successfully assigned $success_count course(s).";
        if ($error_count > 0) { $message .= " ⚠️ $error_count skipped."; $message_type = 'warning'; }
    } else {
        $message = "❌ Please select teachers and courses."; $message_type = 'error';
    }
}

// Fetch Teachers
$teachers = [];
$sql_t = "SELECT user_id, full_name FROM teachers WHERE department = ? ORDER BY full_name ASC";
$stmt = $conn->prepare($sql_t);
$stmt->bind_param("s", $hod_department);
$stmt->execute();
$res_t = $stmt->get_result();
while ($row = $res_t->fetch_assoc()) { $teachers[] = $row; }
$stmt->close();

// Fetch Courses
$courses = [];
if ($filter_year !== '' && $filter_semester !== '') {
    $sql_c = "SELECT course_id, course_code, course_name FROM addcourse WHERE department = ? AND course_year = ? AND course_semester = ? ORDER BY course_code ASC";
    $stmt = $conn->prepare($sql_c);
    $stmt->bind_param("sis", $hod_department, $filter_year, $filter_semester); 
    $stmt->execute();
    $res_c = $stmt->get_result();
    while ($row = $res_c->fetch_assoc()) { $courses[] = $row; }
    $stmt->close();
}

include 'Headcourseassignteacherf.php';
?>