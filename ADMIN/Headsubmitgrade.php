<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php"); exit();
}

$hod_department = trim($_SESSION['department'] ?? '');
$message = '';
$message_type = 'success';

// 2. Handle Grade Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $course_code = $_POST['course_code'] ?? '';
    $ac_year = $_POST['ac_year'] ?? '';
    $semester = $_POST['semester'] ?? '';
    $action_type = $_POST['action'];

    if (($action_type === 'submit' || $action_type === 'submit_partial') && $course_code !== '') {
        
        // Update status to 'Submitted'
        $sql = "UPDATE student_courses 
                SET grade_status = 'Submitted', submitted_at = NOW(), submitted_by = ? 
                WHERE course_code = ? AND ac_year = ? AND semester = ? AND grade_status = 'Pending'";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $_SESSION['user_id'], $course_code, $ac_year, $semester);
        
        if ($stmt->execute()) {
            $count = $stmt->affected_rows;
            $message = "✅ Successfully submitted $count grade(s) for $course_code.";
        } else {
            $message = "❌ Error submitting grades: " . $stmt->error;
            $message_type = 'error';
        }
        $stmt->close();
    } else {
        $message = "❌ Invalid request.";
        $message_type = 'error';
    }
}

// 3. Read Filters (Year and Semester only)
$f_year = $_GET['year'] ?? '';
$f_semester = $_GET['semester'] ?? '';

// 4. Build Query for Pending Grades
// Note: We use $hod_department to strictly filter by the Head's department
$sql = "SELECT sc.course_code, c.course_name, c.course_year, 
               sc.ac_year, sc.semester, 
               COUNT(sc.id) AS total, 
               SUM(CASE WHEN sc.grade IS NOT NULL AND sc.grade != '' THEN 1 ELSE 0 END) AS graded,
               SUM(CASE WHEN sc.grade IS NULL OR sc.grade = '' THEN 1 ELSE 0 END) AS missing
        FROM student_courses sc
        LEFT JOIN addcourse c ON sc.course_code = c.course_code
        WHERE sc.grade_status = 'Pending' AND c.department = ?";

$params = [$hod_department];
$types = "s";

// Apply Year Filter
if ($f_year !== '') {
    $sql .= " AND c.course_year = ?";
    $params[] = $f_year;
    $types .= "i";
}

// Apply Semester Filter
if ($f_semester !== '') {
    $sql .= " AND sc.semester = ?";
    $params[] = $f_semester;
    $types .= "i";
}

$sql .= " GROUP BY sc.course_code, sc.ac_year, sc.semester ORDER BY sc.course_code ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$pendingGroups = [];
while ($row = $result->fetch_assoc()) { $pendingGroups[] = $row; }
$stmt->close();

// 5. Load the HTML View
include 'Headsubmitgradef.php';
?>