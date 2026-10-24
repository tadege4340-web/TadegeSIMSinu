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
$my_courses = [];
$grades_list = [];

try {
    // 2. Fetch Assigned Courses for this Teacher (PDO)
    if ($role === 'teacher') {
        $stmt = $conn->prepare("
            SELECT c.course_id, c.course_code, c.course_name 
            FROM addcourse c
            JOIN teacher_courses tc ON c.course_id = tc.course_id
            WHERE tc.teacher_id = :tid
        ");
        $stmt->execute(['tid' => $teacher_user_id]);
        $my_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Admin sees all
        $stmt = $conn->query("SELECT course_id, course_code, course_name FROM addcourse");
        $my_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Handle Filter to Load Grades (PDO)
    $f_course_id = (int)($_GET['course_id'] ?? 0);

    if ($f_course_id > 0) {
        // Get the course_code for this ID
        $stmt_c = $conn->prepare("SELECT course_code FROM addcourse WHERE course_id = :cid");
        $stmt_c->execute(['cid' => $f_course_id]);
        $c_info = $stmt_c->fetch(PDO::FETCH_ASSOC);
        $target_code = $c_info['course_code'] ?? '';

        // Fetch students and their grades for this specific course
        $stmt = $conn->prepare("
            SELECT u.student_id, u.firstname, u.father_name, u.gender,
                   sc.ac_year, sc.semester, sc.grade, sc.grade_point, 
                   sc.status, sc.grade_status, sc.remark
            FROM student_courses sc
            JOIN users2 u ON u.student_id = sc.student_id
            WHERE sc.course_id = :cid OR sc.course_code = :ccode
            ORDER BY u.firstname ASC
        ");
        $stmt->execute([
            'cid' => $f_course_id,
            'ccode' => $target_code
        ]);
        $grades_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

include 'Teacherviewgradef.php';
?>