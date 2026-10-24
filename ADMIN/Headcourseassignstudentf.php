<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php"); 
    exit();
}

$hod_department = trim($_SESSION['department'] ?? '');
$message = ''; 
$message_type = 'success';

$filter_year     = $_GET['year'] ?? '';
$filter_semester = $_GET['semester'] ?? '';

// 2. Handle Assignment (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign'])) {
    $student_ids = $_POST['student_ids'] ?? []; 
    $course_ids  = $_POST['course_ids'] ?? [];
    
    if (!empty($student_ids) && !empty($course_ids)) {
        $success_count = 0; 
        $error_count = 0;
        $ac_year = '2024/2025'; // Default academic year
        
        try {
            $conn->beginTransaction();

            foreach ($student_ids as $s_id) {
                foreach ($course_ids as $c_id) {
                    
                    // Fetch the exact course details first (PDO)
                    $stmt_c = $conn->prepare("SELECT course_code, course_semester FROM addcourse WHERE course_id = :cid");
                    $stmt_c->execute(['cid' => $c_id]);
                    $course_info = $stmt_c->fetch(PDO::FETCH_ASSOC);
                    
                    $course_code = $course_info['course_code'] ?? '';
                    $course_sem  = $course_info['course_semester'] ?? $filter_semester;

                    // Insert using PDO's INSERT IGNORE to skip duplicates
                    $sql = "INSERT IGNORE INTO student_courses (student_id, course_code, course_id, ac_year, semester, grade_status) 
                            VALUES (:sid, :ccode, :cid, :ayear, :sem, 'Pending')";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        'sid' => $s_id,
                        'ccode' => $course_code,
                        'cid' => $c_id,
                        'ayear' => $ac_year,
                        'sem' => $course_sem
                    ]);
                    
                    // If the insert was successful (1 row affected), count it
                    if ($stmt->rowCount() > 0) { 
                        $success_count++; 
                    } else { 
                        $error_count++; 
                    }
                }
            }
            
            $conn->commit();
            $message = "✅ Successfully assigned $success_count course(s) to students.";
            if ($error_count > 0) { 
                $message .= " ⚠️ $error_count skipped (already assigned)."; 
                $message_type = 'warning'; 
            }
        } catch (PDOException $e) {
            $conn->rollBack();
            $message = "❌ Database Error: " . $e->getMessage();
            $message_type = 'error';
        }
    } else {
        $message = "❌ Please select students and courses."; 
        $message_type = 'error';
    }
}

// 3. Fetch Students (PDO)
$students = [];
if ($filter_year !== '') {
    try {
        $stmt = $conn->prepare("SELECT student_id, firstname, father_name FROM users2 WHERE department = :dept AND `year` = :year ORDER BY firstname ASC");
        $stmt->execute([
            'dept' => $hod_department,
            'year' => $filter_year
        ]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

// 4. Fetch Courses (PDO)
$courses = [];
if ($filter_year !== '' && $filter_semester !== '') {
    try {
        $sql_c = "SELECT course_id, course_code, course_name FROM addcourse WHERE department = :dept AND course_year = :year AND course_semester = :sem ORDER BY course_code ASC";
        $stmt = $conn->prepare($sql_c);
        $stmt->execute([
            'dept' => $hod_department,
            'year' => $filter_year,
            'sem' => $filter_semester
        ]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}

// 5. Load the View
include 'Headcourseassignstudentf.php';
?>