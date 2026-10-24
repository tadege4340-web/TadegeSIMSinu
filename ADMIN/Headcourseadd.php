<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php"); 
    exit();
}

$hod_department = trim($_SESSION['department'] ?? '');
$hod_college    = trim($_SESSION['college'] ?? '');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_code     = trim($_POST['course_code'] ?? '');
    $course_name     = trim($_POST['course_name'] ?? '');
    $credit_hour     = trim($_POST['credit_hour'] ?? '');
    $course_year     = trim($_POST['course_year'] ?? '');
    $course_semester = trim($_POST['course_semester'] ?? '1'); 
    
    $category = "Major Course";
    
    if (!empty($course_code) && !empty($course_name)) {
        try {
            // Check for duplicate first
            $chk = $conn->prepare("SELECT course_id FROM addcourse WHERE course_code = :ccode");
            $chk->execute(['ccode' => $course_code]);
            
            if ($chk->fetch()) {
                $message = "<div style='color: red; margin-bottom: 15px; font-weight: bold;'>❌ Course code '$course_code' already exists.</div>";
            } else {
                // Insert new course (PDO)
                $sql = "INSERT INTO addcourse (course_code, course_name, credit_hour, category, college, department, course_year, course_semester) 
                        VALUES (:code, :name, :credit, :category, :college, :dept, :year, :sem)";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    'code' => $course_code,
                    'name' => $course_name,
                    'credit' => $credit_hour,
                    'category' => $category,
                    'college' => $hod_college,
                    'dept' => $hod_department,
                    'year' => $course_year,
                    'sem' => $course_semester
                ]);
                
                $message = "<div style='color: green; margin-bottom: 15px; font-weight: bold;'>✅ Course added successfully!</div>";
            }
        } catch (PDOException $e) {
            $message = "<div style='color: red; margin-bottom: 15px; font-weight: bold;'>❌ Error: " . $e->getMessage() . "</div>";
        }
    } else {
        $message = "<div style='color: red; margin-bottom: 15px; font-weight: bold;'>❌ Please fill in all required fields.</div>";
    }
}

include 'Headcourseaddf.php';
?>