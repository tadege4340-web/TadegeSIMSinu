<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require 'db.php';

$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['admin', 'teacher'])) {
    header("Location: loginf.php");
    exit();
}

$message = "";
$message_type = "";

// 1. Get Teacher Info
$teacher_username = $_SESSION['username'] ?? '';
$teacher_user_id = $_SESSION['user_id'] ?? 0;

// Fetch Assigned Courses for this Teacher (PDO)
$my_courses = [];
try {
    if ($role === 'teacher') {
        $stmt = $conn->prepare("
            SELECT c.course_id, c.course_code, c.course_name, c.course_year, c.course_semester 
            FROM addcourse c
            JOIN teacher_courses tc ON c.course_id = tc.course_id
            WHERE tc.teacher_id = :tid
        ");
        $stmt->execute(['tid' => $teacher_user_id]);
        $my_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $conn->query("SELECT course_id, course_code, course_name, course_year, course_semester FROM addcourse");
        $my_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $message = "Error loading courses: " . $e->getMessage();
    $message_type = "error";
}

// 2. Handle Grade Submission (PDO)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_grades') {
    $course_code = trim($_POST['course_code'] ?? '');
    $semester    = (int)($_POST['semester']   ?? 0);
    $grades      = $_POST['grades']   ?? [];
    
    if ($course_code === '' || $semester === 0 || empty($grades)) {
        $message = "⚠️ Missing data or no grades submitted.";
        $message_type = "error";
    } else {
        $saved = 0; $skipped = 0; $locked = 0;
        
        try {
            $conn->beginTransaction();

            foreach ($grades as $student_id => $data) {
                $grade = strtoupper(trim($data['grade'] ?? ''));
                $gp    = (float)($data['gp'] ?? 0);

                if ($grade === '') { $skipped++; continue; }

                // Check if record exists
                $chk = $conn->prepare("SELECT id, grade_status FROM student_courses WHERE student_id = :sid AND course_code = :cc LIMIT 1");
                $chk->execute(['sid' => $student_id, 'cc' => $course_code]);
                $existing = $chk->fetch(PDO::FETCH_ASSOC);

                if (!$existing) { $skipped++; continue; }

                // BACKEND LOCK RULE
                if (in_array($existing['grade_status'] ?? 'Pending', ['Approved', 'Locked'])) {
                    $locked++; continue;
                }

                // Auto-calculation for GP
                $grade_points_map = [
                    'A+' => 4.00, 'A' => 4.00, 'A-' => 3.75,
                    'B+' => 3.50, 'B' => 3.00, 'B-' => 2.75,
                    'C+' => 2.50, 'C' => 2.00, 'C-' => 1.75,
                    'D' => 1.00, 'F' => 0.00
                ];

                if (array_key_exists($grade, $grade_points_map)) {
                    $gp = $grade_points_map[$grade];
                }

                $status = ($gp >= 2.00) ? 'Pass' : 'Fail';

                $upd = $conn->prepare("UPDATE student_courses SET grade = :grade, grade_point = :gp, status = :status, grade_status = 'Pending', last_edited_at = NOW(), last_edited_by = :edited_by WHERE id = :id");
                $upd->execute([
                    'grade' => $grade, 
                    'gp' => $gp, 
                    'status' => $status, 
                    'edited_by' => $teacher_username, 
                    'id' => $existing['id']
                ]);

                if ($upd->rowCount() > 0) $saved++;
                else $skipped++;
            }

            $conn->commit();
            $message = "✅ <strong>Grades saved!</strong><br>• Saved: <b>$saved</b><br>• Skipped: <b>$skipped</b><br>" . ($locked > 0 ? "• Locked (Already Approved): <b>$locked</b>" : "");
            $message_type = "success";
        } catch (Exception $e) {
            $conn->rollBack();
            $message = "❌ Error: " . htmlspecialchars($e->getMessage());
            $message_type = "error";
        }
    }
}

// 3. Handle Filters to Load Students (PDO)
$f_course_id = (int)($_GET['course_id'] ?? 0);
$students_with_grades = [];

if ($f_course_id > 0) {
    try {
        // Get the course code first
        $stmt_c = $conn->prepare("SELECT course_code FROM addcourse WHERE course_id = :cid");
        $stmt_c->execute(['cid' => $f_course_id]);
        $c_info = $stmt_c->fetch(PDO::FETCH_ASSOC);
        $target_code = $c_info['course_code'] ?? '';

        // Fetch students with grades
        $stmt = $conn->prepare("
            SELECT u.student_id, u.firstname, u.father_name,
                   sc.id AS enrollment_id, sc.grade, sc.grade_point,
                   sc.grade_status, sc.reg_type
            FROM student_courses sc
            JOIN users2 u ON u.student_id = sc.student_id
            WHERE sc.course_id = :cid OR sc.course_code = :ccode
            ORDER BY u.firstname ASC
        ");
        $stmt->execute(['cid' => $f_course_id, 'ccode' => $target_code]);
        $students_with_grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message = "Error loading students: " . $e->getMessage();
        $message_type = "error";
    }
}

include 'Teacheraddgradef.php';
?>