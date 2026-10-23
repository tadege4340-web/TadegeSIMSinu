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

// Fetch Assigned Courses for this Teacher
$my_courses = [];
if ($role === 'teacher') {
    $stmt = $conn->prepare("
        SELECT c.course_id, c.course_code, c.course_name, c.course_year, c.course_semester 
        FROM addcourse c
        JOIN teacher_courses tc ON c.course_id = tc.course_id
        WHERE tc.teacher_id = ?
    ");
    $stmt->bind_param("i", $teacher_user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) { $my_courses[] = $row; }
    $stmt->close();
} else {
    $res = $conn->query("SELECT course_id, course_code, course_name, course_year, course_semester FROM addcourse");
    while ($row = $res->fetch_assoc()) { $my_courses[] = $row; }
}

// 2. Handle Grade Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_grades') {
    $course_code = trim($_POST['course_code'] ?? '');
    $semester    = (int)($_POST['semester']   ?? 0);
    $grades      = $_POST['grades']   ?? [];
    
    if ($course_code === '' || $semester === 0 || empty($grades)) {
        $message = "⚠️ Missing data or no grades submitted.";
        $message_type = "error";
    } else {
        $saved = 0; $skipped = 0; $locked = 0;
        $conn->begin_transaction();

        try {
            foreach ($grades as $student_id => $data) {
                $grade = strtoupper(trim($data['grade'] ?? ''));
                $gp    = (float)($data['gp'] ?? 0);

                if ($grade === '') { $skipped++; continue; }

                $chk = $conn->prepare("SELECT id, grade_status FROM student_courses WHERE student_id = ? AND course_code = ? LIMIT 1");
                $chk->bind_param("ss", $student_id, $course_code);
                $chk->execute();
                $existing = $chk->get_result()->fetch_assoc();
                $chk->close();

                if (!$existing) { $skipped++; continue; }

                // 🔥 BACKEND LOCK RULE
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

                $upd = $conn->prepare("UPDATE student_courses SET grade = ?, grade_point = ?, status = ?, grade_status = 'Pending', last_edited_at = NOW(), last_edited_by = ? WHERE id = ?");
                $upd->bind_param("sdssi", $grade, $gp, $status, $teacher_username, $existing['id']);

                if ($upd->execute()) $saved++;
                else $skipped++;
                $upd->close();
            }

            $conn->commit();
            $message = "✅ <strong>Grades saved!</strong><br>• Saved: <b>$saved</b><br>• Skipped: <b>$skipped</b><br>" . ($locked > 0 ? "• Locked (Already Approved): <b>$locked</b>" : "");
            $message_type = "success";
        } catch (Exception $e) {
            $conn->rollback();
            $message = "❌ Error: " . htmlspecialchars($e->getMessage());
            $message_type = "error";
        }
    }
}

// 3. Handle Filters to Load Students
$f_course_id = (int)($_GET['course_id'] ?? 0);

$students_with_grades = [];

if ($f_course_id > 0) {
    $stmt_c = $conn->prepare("SELECT course_code FROM addcourse WHERE course_id = ?");
    $stmt_c->bind_param("i", $f_course_id);
    $stmt_c->execute();
    $c_info = $stmt_c->get_result()->fetch_assoc();
    $target_code = $c_info['course_code'] ?? '';
    $stmt_c->close();

    $stmt = $conn->prepare("
        SELECT u.student_id, u.firstname, u.father_name,
               sc.id AS enrollment_id, sc.grade, sc.grade_point,
               sc.grade_status, sc.reg_type
        FROM student_courses sc
        JOIN users2 u ON u.student_id = sc.student_id
        WHERE sc.course_id = ? OR sc.course_code = ?
        ORDER BY u.firstname ASC
    ");
    $stmt->bind_param("is", $f_course_id, $target_code);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) { $students_with_grades[] = $row; }
    $stmt->close();
}

include 'Teacheraddgradef.php';
?>