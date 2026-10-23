<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

// 1. Security Check
$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['admin', 'teacher'])) {
    header("Location: loginf.php");
    exit();
}

$teacher_user_id = $_SESSION['user_id'] ?? 0;

// 2. Fetch Assigned Courses for this Teacher
$my_courses = [];
if ($role === 'teacher') {
    $stmt = $conn->prepare("
        SELECT c.course_id, c.course_code, c.course_name 
        FROM addcourse c
        JOIN teacher_courses tc ON c.course_id = tc.course_id
        WHERE tc.teacher_id = ?
    ");
    $stmt->bind_param("i", $teacher_user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) { $my_courses[] = $row; }
    $stmt->close();
} else {
    // Admin sees all
    $res = $conn->query("SELECT course_id, course_code, course_name FROM addcourse");
    while ($row = $res->fetch_assoc()) { $my_courses[] = $row; }
}

// 3. Handle Filter to Load Grades
$f_course_id = (int)($_GET['course_id'] ?? 0);
$grades_list = [];

if ($f_course_id > 0) {
    // Get the course_code for this ID
    $stmt_c = $conn->prepare("SELECT course_code FROM addcourse WHERE course_id = ?");
    $stmt_c->bind_param("i", $f_course_id);
    $stmt_c->execute();
    $c_info = $stmt_c->get_result()->fetch_assoc();
    $target_code = $c_info['course_code'] ?? '';
    $stmt_c->close();

    // Fetch students and their grades for this specific course
    $stmt = $conn->prepare("
        SELECT u.student_id, u.firstname, u.father_name, u.gender,
               sc.ac_year, sc.semester, sc.grade, sc.grade_point, 
               sc.status, sc.grade_status, sc.remark
        FROM student_courses sc
        JOIN users2 u ON u.student_id = sc.student_id
        WHERE sc.course_id = ? OR sc.course_code = ?
        ORDER BY u.firstname ASC
    ");
    $stmt->bind_param("is", $f_course_id, $target_code);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) { $grades_list[] = $row; }
    $stmt->close();
}

include 'Teacherviewgradef.php';
?>