<?php
/* ============================================================
   REGISTRAR — CHANGE GRADE (LOGIC) - PDO Version
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

/* ============================================================
   ADMIN / REGISTRAR CHECK
   ============================================================ */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginf.php");
    exit();
}

$message      = "";
$message_type = "";

/* ============================================================
   HANDLE POST — CHANGE GRADE
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id  = trim($_POST['student_id'] ?? '');
    $course_code = trim($_POST['course_code'] ?? '');
    $ac_year     = trim($_POST['ac_year'] ?? '');
    $semester    = (int)($_POST['semester'] ?? 0);
    $new_grade   = strtoupper(trim($_POST['new_grade'] ?? ''));
    $new_gp      = (float)($_POST['new_grade_point'] ?? 0);
    $reason      = trim($_POST['reason'] ?? '');
    $changed_by  = $_SESSION['username'] ?? 'admin';

    if ($student_id === '' || $course_code === '' || $ac_year === '' || $semester === 0 || $new_grade === '') {
        $message = "⚠️ Please fill in all required fields.";
        $message_type = "error";
    } else {
        try {
            /* ---------- 1. Check if the grade record exists and get the old grade ---------- */
            $stmt = $conn->prepare("
                SELECT id, grade, grade_point 
                FROM student_courses 
                WHERE student_id = :sid 
                  AND course_code = :ccode 
                  AND ac_year = :ayear 
                  AND semester = :sem 
                LIMIT 1");
            $stmt->execute([
                'sid' => $student_id,
                'ccode' => $course_code,
                'ayear' => $ac_year,
                'sem' => $semester
            ]);
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$record) {
                $message = "❌ No grade record found for this student, course, year, and semester.";
                $message_type = "error";
            } else {
                $old_grade = $record['grade'];
                $old_gp    = $record['grade_point'];

                /* ---------- 2. Determine Pass/Fail status ---------- */
                $status = ($new_gp >= 2.00) ? 'Pass' : 'Fail';

                /* ---------- 3. Update the grade in student_courses ---------- */
                $upd = $conn->prepare("
                    UPDATE student_courses 
                    SET grade = :grade, 
                        grade_point = :gp, 
                        status = :status, 
                        grade_status = 'Approved', 
                        last_edited_by = :changed_by, 
                        last_edited_at = NOW(),
                        edit_reason = :reason
                    WHERE id = :id");
                $upd->execute([
                    'grade' => $new_grade,
                    'gp' => $new_gp,
                    'status' => $status,
                    'changed_by' => $changed_by,
                    'reason' => $reason,
                    'id' => $record['id']
                ]);

                /* ---------- 4. Log the change in grade_audit_log ---------- */
                $log = $conn->prepare("
                    INSERT INTO grade_audit_log 
                    (student_course_id, student_id, course_code, ac_year, semester, 
                     old_grade, new_grade, old_grade_point, new_grade_point, action, action_by, reason) 
                    VALUES (:sc_id, :sid, :ccode, :ayear, :sem, :old_grade, :new_grade, :old_gp, :new_gp, 'registrar_change', :changed_by, :reason)");
                $log->execute([
                    'sc_id' => $record['id'],
                    'sid' => $student_id,
                    'ccode' => $course_code,
                    'ayear' => $ac_year,
                    'sem' => $semester,
                    'old_grade' => $old_grade,
                    'new_grade' => $new_grade,
                    'old_gp' => $old_gp,
                    'new_gp' => $new_gp,
                    'changed_by' => $changed_by,
                    'reason' => $reason
                ]);

                $message = "✅ Grade changed successfully from <b>" . htmlspecialchars($old_grade) . "</b> to <b>" . htmlspecialchars($new_grade) . "</b>!";
                $message_type = "success";
            }
        } catch (PDOException $e) {
            $message = "❌ Database Error: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

/* ============================================================
   LOAD HEADER + VIEW
   ============================================================ */
include 'header.php';
include 'mygradechangerf.php';
include 'footer.php';
?>