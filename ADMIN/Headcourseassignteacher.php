<?php
// FIX: Allow session cookies to work inside iframes (required for Render)
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,      // Must be true for SameSite=None (Render uses HTTPS)
    'httponly' => true,
    'samesite' => 'None'   // Critical: allows the cookie to be sent inside the iframe
]);

if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php"); 
    exit();
}

$hod_department = trim($_SESSION['department'] ?? '');
$hod_college    = trim($_SESSION['college'] ?? '');
$message = '';
$message_type = '';

// 2. Read Filters
$filter_year     = $_GET['year'] ?? '';
$filter_semester = $_GET['semester'] ?? '';

// 3. Handle POST (Assign)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign'])) {
    $teacher_ids = $_POST['teacher_ids'] ?? [];
    $course_ids  = $_POST['course_ids'] ?? [];
    $ac_year     = '2024/2025'; // Set to your active academic year

    if (empty($teacher_ids) || empty($course_ids)) {
        $message = "⚠️ Please select at least one teacher and one course.";
        $message_type = 'error';
    } else {
        try {
            $conn->beginTransaction();
            $assigned_count = 0;
            $skipped_count = 0;

            foreach ($teacher_ids as $t_id) {
                foreach ($course_ids as $c_id) {
                    // Get course details for the mapping
                    $c_stmt = $conn->prepare("SELECT course_code FROM addcourse WHERE course_id = :cid");
                    $c_stmt->execute(['cid' => $c_id]);
                    $course = $c_stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($course) {
                        // Check if already assigned to avoid duplicates
                        $chk = $conn->prepare("SELECT id FROM teacher_courses WHERE teacher_id = :tid AND course_id = :cid AND ac_year = :year AND semester = :sem");
                        $chk->execute([
                            'tid' => $t_id, 
                            'cid' => $c_id, 
                            'year' => $ac_year, 
                            'sem' => $filter_semester
                        ]);

                        if (!$chk->fetch()) {
                            $insert = $conn->prepare("
                                INSERT INTO teacher_courses (teacher_id, course_code, course_id, ac_year, semester, assign_date) 
                                VALUES (:tid, :ccode, :cid, :year, :sem, NOW())
                            ");
                            $insert->execute([
                                'tid' => $t_id,
                                'ccode' => $course['course_code'],
                                'cid' => $c_id,
                                'year' => $ac_year,
                                'sem' => $filter_semester
                            ]);
                            $assigned_count++;
                        } else {
                            $skipped_count++;
                        }
                    }
                }
            }
            $conn->commit();
            $message = "✅ Successfully assigned $assigned_count teacher-course pair(s)!";
            if ($skipped_count > 0) {
                $message .= " ⚠️ $skipped_count skipped (already assigned).";
                $message_type = 'warning';
            } else {
                $message_type = 'success';
            }
        } catch (PDOException $e) {
            $conn->rollBack();
            $message = "❌ Database Error: " . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// 4. Fetch Teachers and Courses for the selected Year/Semester (PDO)
$teachers = [];
$courses = [];

if ($filter_year !== '' && $filter_semester !== '') {
    try {
        // Fetch teachers in this department
        $stmt = $conn->prepare("SELECT user_id, full_name FROM teachers WHERE department = :dept ORDER BY full_name ASC");
        $stmt->execute(['dept' => $hod_department]);
        $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch courses for this year/semester in this department
        $stmt = $conn->prepare("
            SELECT course_id, course_code, course_name 
            FROM addcourse 
            WHERE department = :dept 
              AND course_year = :year 
              AND course_semester = :sem
            ORDER BY course_code ASC
        ");
        $stmt->execute([
            'dept' => $hod_department,
            'year' => $filter_year,
            'sem' => $filter_semester
        ]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $message = "Error loading data: " . $e->getMessage();
        $message_type = 'error';
    }
}

// 5. Load the View
include 'Headcourseassignteacherf.php';
?>