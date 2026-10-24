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
    $teacher_ids = $_POST['teacher_ids'] ?? []; 
    $course_ids  = $_POST['course_ids'] ?? [];
    
    if (!empty($teacher_ids) && !empty($course_ids)) {
        $success_count = 0; 
        $error_count = 0;
        
        try {
            $conn->beginTransaction();

            foreach ($teacher_ids as $t_id) {
                foreach ($course_ids as $c_id) {
                    
                    // Fetch the exact course details first (PDO)
                    $stmt_c = $conn->prepare("SELECT course_code, course_semester FROM addcourse WHERE course_id = :cid");
                    $stmt_c->execute(['cid' => $c_id]);
                    $course_info = $stmt_c->fetch(PDO::FETCH_ASSOC);
                    
                    $course_code = $course_info['course_code'] ?? '';
                    $course_sem  = $course_info['course_semester'] ?? $filter_semester;

                    // Insert using PDO's INSERT IGNORE to skip duplicates
                    $sql = "INSERT IGNORE INTO teacher_courses (teacher_id, course_code, course_id, ac_year, semester, role) 
                            VALUES (:tid, :ccode, :cid, '2024/2025', :sem, 'Lecturer')";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        'tid' => $t_id,
                        'ccode' => $course_code,
                        'cid' => $c_id,
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
            $message = "✅ Successfully assigned $success_count course(s).";
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
        $message = "❌ Please select teachers and courses."; 
        $message_type = 'error';
    }
}

// 3. Fetch Teachers (PDO)
$teachers = [];
try {
    $stmt = $conn->prepare("SELECT user_id, full_name FROM teachers WHERE department = :dept ORDER BY full_name ASC");
    $stmt->execute(['dept' => $hod_department]);
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

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
include 'Headcourseassignteacherf.php';
?>