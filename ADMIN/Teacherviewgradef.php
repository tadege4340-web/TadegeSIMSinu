<?php
session_start();
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: loginf.php");
    exit();
}

// 2. Get Teacher's Database ID
$teacher_user_id = $_SESSION['user_id'] ?? 0;
$my_courses = [];
$grades_list = [];
$f_course_id = (int)($_GET['course_id'] ?? 0);

try {
    // 3. Fetch Teacher's Assigned Courses
    $stmt = $conn->prepare("
        SELECT c.course_id, c.course_code, c.course_name 
        FROM addcourse c
        JOIN teacher_courses tc ON c.course_id = tc.course_id
        WHERE tc.teacher_id = :tid
        ORDER BY c.course_code ASC
    ");
    $stmt->execute(['tid' => $teacher_user_id]);
    $my_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Fetch Grades for the Selected Course
    if ($f_course_id > 0) {
        $stmt_g = $conn->prepare("
            SELECT sc.student_id, u.firstname, u.father_name,
                   sc.ac_year, sc.semester, sc.grade, sc.grade_point, 
                   sc.status, sc.grade_status
            FROM student_courses sc
            JOIN users2 u ON sc.student_id = u.student_id
            WHERE sc.course_id = :cid
            ORDER BY u.firstname ASC
        ");
        $stmt_g->execute(['cid' => $f_course_id]);
        $grades_list = $stmt_g->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

// 5. Load the View
include 'Teacherviewgradef.php';
?>