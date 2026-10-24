<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php"); 
    exit();
}

$hod_department = trim($_SESSION['department'] ?? '');
$hod_college    = trim($_SESSION['college'] ?? '');
$message = '';

// 2. Handle Form Submission (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_code     = trim($_POST['course_code'] ?? '');
    $course_name     = trim($_POST['course_name'] ?? '');
    $credit_hour     = (int)($_POST['credit_hour'] ?? 0);
    $course_year     = (int)($_POST['course_year'] ?? 0);
    $course_semester = (int)($_POST['course_semester'] ?? 0);

    if ($course_code === '' || $course_name === '' || $credit_hour <= 0 || $course_year <= 0 || $course_semester <= 0) {
        $message = "<p style='color:red; font-weight:bold;'>⚠️ Please fill in all fields correctly.</p>";
    } else {
        try {
            // Check for duplicate course code first
            $chk = $conn->prepare("SELECT course_id FROM addcourse WHERE course_code = :ccode");
            $chk->execute(['ccode' => $course_code]);
            
            if ($chk->fetch()) {
                $message = "<p style='color:red; font-weight:bold;'>❌ Course code '$course_code' already exists.</p>";
            } else {
                // Insert the new course (PDO)
                $sql = "INSERT INTO addcourse 
                        (course_code, course_name, credit_hour, college, department, course_year, course_semester) 
                        VALUES (:code, :name, :credit, :college, :dept, :year, :sem)";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    'code' => $course_code,
                    'name' => $course_name,
                    'credit' => $credit_hour,
                    'college' => $hod_college,
                    'dept' => $hod_department,
                    'year' => $course_year,
                    'sem' => $course_semester
                ]);

                $message = "<p style='color:green; font-weight:bold;'>✅ Course '$course_code' added successfully!</p>";
            }
        } catch (PDOException $e) {
            $message = "<p style='color:red; font-weight:bold;'>❌ Database Error: " . $e->getMessage() . "</p>";
        }
    }
}

// 3. Load the View
include 'Headcourseaddf.php';
?>