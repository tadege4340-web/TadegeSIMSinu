<?php
session_start();
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: loginf.php");
    exit();
}

$my_courses = [];

if ($_SESSION['role'] === 'teacher') {
    $session_id = $_SESSION['user_id']; 

    try {
        // 2. The direct JOIN approach (PDO)
        $sql = "SELECT c.course_id, c.course_code, c.course_name, c.credit_hour, c.course_year, c.course_semester 
                FROM addcourse c
                JOIN teacher_courses tc ON c.course_id = tc.course_id
                JOIN teachers t ON tc.teacher_id = t.user_id
                WHERE t.user_id = :uid OR t.id = :tid
                ORDER BY c.course_year ASC, c.course_semester ASC, c.course_code ASC";

        $stmt_courses = $conn->prepare($sql);
        $stmt_courses->execute([
            'uid' => $session_id,
            'tid' => $session_id
        ]);
        
        $my_courses = $stmt_courses->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $db_error = $e->getMessage();
    }
}

// Include the View
include 'Teacherviewcoursef.php';
?>