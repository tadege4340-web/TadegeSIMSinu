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

// 2. Handle Grade Submission (PDO)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $course_code = $_POST['course_code'] ?? '';
    $ac_year     = $_POST['ac_year'] ?? '';
    $semester    = (int)($_POST['semester'] ?? 0);
    $action_type = $_POST['action'];
    $hod_id      = $_SESSION['user_id'];

    if (($action_type === 'submit' || $action_type === 'submit_partial') && $course_code !== '') {
        
        try {
            // Update status to 'Submitted' (PDO)
            $sql = "UPDATE student_courses 
                    SET grade_status = 'Submitted', 
                        submitted_at = NOW(), 
                        submitted_by = :submitted_by 
                    WHERE course_code = :ccode 
                      AND ac_year = :ayear 
                      AND semester = :sem 
                      AND grade_status = 'Pending'";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                'submitted_by' => $hod_id,
                'ccode' => $course_code,
                'ayear' => $ac_year,
                'sem' => $semester
            ]);
            
            $count = $stmt->rowCount();
            $message = "✅ Successfully submitted $count grade(s) for $course_code.";

            // Log the action in grade_audit_log
            $log = $conn->prepare("
                INSERT INTO grade_audit_log 
                (student_id, course_code, ac_year, semester, action, action_by, reason)
                VALUES ('*', :ccode, :ayear, :sem, :action, :action_by, :reason)");
            
            $reason_txt = "$count grades submitted " . ($action_type === 'submit_partial' ? "(partial)" : "(full)");
            
            $log->execute([
                'ccode' => $course_code,
                'ayear' => $ac_year,
                'sem' => $semester,
                'action' => $action_type,
                'action_by' => $hod_id,
                'reason' => $reason_txt
            ]);

        } catch (PDOException $e) {
            $message = "❌ Error submitting grades: " . $e->getMessage();
            $message_type = 'error';
        }
    } else {
        $message = "❌ Invalid request.";
        $message_type = 'error';
    }
}

// 3. Read Filters
$f_year     = $_GET['year'] ?? '';
$f_semester = $_GET['semester'] ?? '';

// 4. Build Query for Pending Grades (PDO)
$sql = "SELECT sc.course_code, c.course_name, c.course_year, 
               sc.ac_year, sc.semester, 
               COUNT(sc.id) AS total, 
               SUM(CASE WHEN sc.grade IS NOT NULL AND sc.grade != '' THEN 1 ELSE 0 END) AS graded,
               SUM(CASE WHEN sc.grade IS NULL OR sc.grade = '' THEN 1 ELSE 0 END) AS missing
        FROM student_courses sc
        LEFT JOIN addcourse c ON sc.course_code = c.course_code
        WHERE sc.grade_status = 'Pending' AND c.department = :dept";

$params = ['dept' => $hod_department];

// Apply Year Filter
if ($f_year !== '') {
    $sql .= " AND c.course_year = :year";
    $params['year'] = $f_year;
}

// Apply Semester Filter
if ($f_semester !== '') {
    $sql .= " AND sc.semester = :sem";
    $params['sem'] = $f_semester;
}

$sql .= " GROUP BY sc.course_code, sc.ac_year, sc.semester ORDER BY sc.course_code ASC";

$pendingGroups = [];

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $pendingGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error loading pending grades: " . $e->getMessage();
    $message_type = 'error';
}

// 5. Load the HTML View
include 'Headsubmitgradef.php';
?>