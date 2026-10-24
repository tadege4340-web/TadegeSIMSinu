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
$message = "";
$message_type = "";

// 2. Handle Submission (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action      = $_POST['action'];
    $course_code = trim($_POST['course_code'] ?? '');
    $ac_year     = trim($_POST['ac_year'] ?? '');
    $semester    = (int)($_POST['semester'] ?? 0);
    $hod_name    = $_SESSION['full_name'] ?? 'Head';

    if ($course_code && $ac_year && $semester) {
        try {
            // Update the grade status to 'Submitted'
            $upd = $conn->prepare("
                UPDATE student_courses 
                SET grade_status = 'Submitted', 
                    submitted_at = NOW(), 
                    submitted_by = :submitted_by,
                    head_approved_at = NOW(),
                    head_approved_by = :approved_by
                WHERE course_code = :ccode 
                  AND ac_year = :ayear 
                  AND semester = :sem 
                  AND grade IS NOT NULL 
                  AND grade <> ''");
            
            $upd->execute([
                'submitted_by' => $hod_name,
                'approved_by' => $hod_name,
                'ccode' => $course_code,
                'ayear' => $ac_year,
                'sem' => $semester
            ]);

            $count = $upd->rowCount();
            $message = "✅ <strong>$count grade(s) submitted successfully!</strong><br>They are now waiting for Registrar approval.";
            $message_type = "success";

            // Log the action
            $log = $conn->prepare("
                INSERT INTO grade_audit_log 
                (student_id, course_code, ac_year, semester, action, action_by, reason)
                VALUES ('*', :ccode, :ayear, :sem, :action, :action_by, :reason)");
            
            $action_type = ($action === 'submit_partial') ? 'submitted_partial' : 'submitted';
            $reason_txt  = "$count grades submitted " . ($action === 'submit_partial' ? "(partial)" : "(full)");

            $log->execute([
                'ccode' => $course_code,
                'ayear' => $ac_year,
                'sem' => $semester,
                'action' => $action_type,
                'action_by' => $hod_name,
                'reason' => $reason_txt
            ]);

        } catch (PDOException $e) {
            $message = "❌ Database Error: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

// 3. Filters
$f_year     = (int)($_GET['year'] ?? 0);
$f_semester = (int)($_GET['semester'] ?? 0);

// 4. Fetch Pending Groups (PDO)
$pendingGroups = [];

try {
    $sql = "
        SELECT 
            sc.course_code,
            a.course_name,
            a.course_year,
            sc.ac_year,
            sc.semester,
            COUNT(*) AS total,
            SUM(CASE WHEN sc.grade IS NOT NULL AND sc.grade <> '' THEN 1 ELSE 0 END) AS graded,
            SUM(CASE WHEN sc.grade IS NULL OR sc.grade = '' THEN 1 ELSE 0 END) AS missing
        FROM student_courses sc
        LEFT JOIN addcourse a ON a.course_code = sc.course_code
        LEFT JOIN users2 u ON u.student_id = sc.student_id
        WHERE sc.grade_status = 'Pending'
          AND u.department = :dept";

    $params = ['dept' => $hod_department];

    if ($f_year > 0) {
        $sql .= " AND a.course_year = :year";
        $params['year'] = $f_year;
    }
    if ($f_semester > 0) {
        $sql .= " AND sc.semester = :sem";
        $params['sem'] = $f_semester;
    }

    $sql .= " GROUP BY sc.course_code, a.course_name, a.course_year, sc.ac_year, sc.semester
              ORDER BY sc.ac_year DESC, sc.semester DESC, sc.course_code ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $pendingGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $message = "Error loading pending grades: " . $e->getMessage();
    $message_type = "error";
}

// 5. Load the View
include 'Headsubmitgradef.php';
?>