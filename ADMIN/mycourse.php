<?php
/* ============================================================
   MY COURSES — LOGIC FILE (mycourse.php) - PDO Version
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
$courses = [];

try {
    $sql = "SELECT sc.course_code, sc.ac_year, sc.semester, sc.reg_type,
                   sc.remark, sc.grade, sc.grade_point, sc.grade_status,
                   sc.submitted_at, sc.approved_at,
                   a.course_id, a.course_name, a.credit_hour, a.category
            FROM student_courses sc
            INNER JOIN addcourse a ON a.course_code = sc.course_code
            WHERE sc.student_id = :sid
            ORDER BY sc.ac_year DESC, sc.semester DESC, sc.course_code ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute(['sid' => $student_id]);
    
    // Fetch all rows into an array
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // If there's an error, you can handle it here. 
    // For now, we just leave $courses empty.
    $db_error = $e->getMessage();
}

// Include the view file (mycoursef.php)
require 'mycoursef.php';
?>