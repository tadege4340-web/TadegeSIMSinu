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

// Handle Assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign'])) {
    $student_ids = $_POST['student_ids'] ?? []; 
    $course_ids = $_POST['course_ids'] ?? [];
    
    if (!empty($student_ids) && !empty($course_ids)) {
        $success_count = 0; $error_count = 0;
        
        foreach ($student_ids as $s_id) {
            foreach ($course_ids as $c_id) {
                
                // Get course code
                $stmt_c = $conn->prepare("SELECT course_code, course_semester FROM addcourse WHERE course_id = ?");
                $stmt_c->bind_param("i", $c_id);
                $stmt_c->execute();
                $course_info = $stmt_c->get_result()->fetch_assoc();
                $stmt_c->close();
                
                $course_code = $course_info['course_code'] ?? '';
                $course_sem = $course_info['course_semester'] ?? $filter_semester;

                // 🔥 FIX: Save 'N' for reg_type to prevent the "Data too long" error
                $sql = "INSERT IGNORE INTO student_courses 
                        (student_id, course_code, course_id, ac_year, semester, reg_date, reg_type, grade_status) 
                        VALUES (?, ?, ?, '2024/2025', ?, NOW(), 'N', 'Pending')";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssii", $s_id, $course_code, $c_id, $course_sem);
                
                if (@$stmt->execute()) { $success_count++; } else { $error_count++; }
                $stmt->close();
            }
        }
        $message = "✅ Successfully assigned $success_count course(s).";
        if ($error_count > 0) { $message .= " ⚠️ $error_count skipped."; $message_type = 'warning'; }
    } else {
        $message = "❌ Please select students and courses."; $message_type = 'error';
    }
}

// Fetch Students Filtered by Year and Semester
$students = [];
if ($filter_year !== '' && $filter_semester !== '') {
    // We join with student_courses to find students registered for this semester
    $sql_s = "
        SELECT DISTINCT u.student_id, u.firstname, u.father_name 
        FROM users2 u
        JOIN student_courses sc ON u.student_id = sc.student_id
        WHERE u.department = ? 
          AND u.year = ? 
          AND sc.semester = ?
        ORDER BY u.firstname ASC
    ";
    $stmt = $conn->prepare($sql_s);
    $stmt->bind_param("sii", $hod_department, $filter_year, $filter_semester);
    $stmt->execute();
    $res_s = $stmt->get_result();
    while ($row = $res_s->fetch_assoc()) { $students[] = $row; }
    $stmt->close();
    
    // Fallback: If no students have registered for that semester yet, show all students in that year
    if (empty($students) && $filter_year !== '') {
        $sql_fallback = "SELECT student_id, firstname, father_name FROM users2 WHERE department = ? AND year = ? ORDER BY firstname ASC";
        $stmt_f = $conn->prepare($sql_fallback);
        $stmt_f->bind_param("si", $hod_department, $filter_year);
        $stmt_f->execute();
        $res_f = $stmt_f->get_result();
        while ($row = $res_f->fetch_assoc()) { $students[] = $row; }
        $stmt_f->close();
    }
}

// Fetch Courses Filtered by Year and Semester
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

include 'Headcourseassignstudentf.php';
?>