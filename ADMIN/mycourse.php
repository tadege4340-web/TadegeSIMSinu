<?php
/* ============================================================
   MY COURSES — LOGIC FILE (mycourse.php)
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['student_id'])) {
    header("Location: loginf.php");
    exit();
}

require 'db.php';

$student_id = $_SESSION['student_id'];

$sql = "SELECT sc.course_code, sc.ac_year, sc.semester, sc.reg_type,
               sc.remark, sc.grade, sc.grade_point, sc.grade_status,
               sc.submitted_at, sc.approved_at,
               a.course_id, a.course_name, a.credit_hour, a.category
        FROM student_courses sc
        INNER JOIN addcourse a ON a.course_code = sc.course_code
        WHERE sc.student_id = ?
        ORDER BY sc.ac_year DESC, sc.semester DESC, sc.course_code ASC";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("SQL prepare error: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "s", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Include the view file (mycoursef.php)
require 'mycoursef.php';
?>