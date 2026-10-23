<?php
session_start();
require 'db.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: loginf.php");
    exit();
}

$my_courses = [];

if ($_SESSION['role'] === 'teacher') {
    
    // 1. Get the session ID (Whatever your login set it to)
    $session_id = $_SESSION['user_id']; 

    // 2. The direct JOIN approach
    // We join teachers -> teacher_courses -> addcourse
    // We match the teacher's user_id to the session ID, OR match the session ID to the teacher's primary id.
    $sql = "SELECT c.course_id, c.course_code, c.course_name, c.credit_hour, c.course_year, c.course_semester 
            FROM addcourse c
            JOIN teacher_courses tc ON c.course_id = tc.course_id
            JOIN teachers t ON tc.teacher_id = t.user_id
            WHERE t.user_id = ? OR t.id = ?
            ORDER BY c.course_year ASC, c.course_semester ASC, c.course_code ASC";

    $stmt_courses = $conn->prepare($sql);
    $stmt_courses->bind_param("ii", $session_id, $session_id);
    $stmt_courses->execute();
    $result_courses = $stmt_courses->get_result();

    while ($c = $result_courses->fetch_assoc()) {
        $my_courses[] = $c;
    }
    $stmt_courses->close();
}

// Include the View
include 'Teacherviewcoursef.php';
?>