<?php
/* ============================================================
   REGISTRAR — CHANGE GRADE (PDO Version)
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

/* ============================================================
   ACCESS CONTROL
   ============================================================ */
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'registrar'])) {
    header("Location: loginf.php");
    exit();
}

/* ============================================================
   DEFAULT STATE
   ============================================================ */
$message      = "";
$message_type = "";

/* ============================================================
   HANDLE POST
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $student_id  = trim($_POST['student_id']  ?? '');
    $course_code = trim($_POST['course_code'] ?? '');
    $ac_year     = trim($_POST['ac_year']     ?? '');
    $semester    = (int)($_POST['semester']   ?? 0);
    $new_grade   = trim($_POST['new_grade']   ?? '');
    $new_gp      = (float)($_POST['new_grade_point'] ?? 0);
    $reason      = trim($_POST['reason']      ?? '');

    /* ---------- Per-field validation ---------- */
    $missing = [];
    if ($student_id  === '') $missing[] = "Student ID";
    if ($course_code === '') $missing[] = "Course Code";
    if ($ac_year     === '') $missing[] = "Academic Year";
    if ($semester    === 0)  $missing[] = "Semester";
    if ($new_grade   === '') $missing[] = "New Grade";
    if ($reason      === '') $missing[] = "Reason for Change";

    if (!empty($missing)) {
        $fieldList = implode(", ", $missing);
        $message = "⚠️ Please fill the following field"
                 . (count($missing) > 1 ? "s" : "")
                 . ": <strong>$fieldList</strong>";
        $message_type = "error";
    } else {

        try {
            /* ---------- Fetch existing enrollment (PDO) ---------- */
            $fetch = $conn->prepare("
                SELECT id, grade, grade_point, grade_status 
                FROM student_courses 
                WHERE student_id = :sid 
                  AND course_code = :ccode 
                  AND ac_year = :ayear 
                  AND semester = :sem
                LIMIT 1");
            $fetch->execute([
                'sid' => $student_id,
                'ccode' => $course_code,
                'ayear' => $ac_year,
                'sem' => $semester
            ]);
            $existing = $fetch->fetch(PDO::FETCH_ASSOC);

            if (!$existing) {
                $message = "❌ <strong>Enrollment not found.</strong><br>"
                         . "Student: " . htmlspecialchars($student_id) . "<br>"
                         . "Course: " . htmlspecialchars($course_code) . "<br>"
                         . "Year: " . htmlspecialchars($ac_year) . "<br>"
                         . "Semester: " . htmlspecialchars($semester) . "<br><br>"
                         . "📌 Assign the course to the student first.";
                $message_type = "error";
            } else {

                $status    = $new_gp >= 2.00 ? 'Pass' : 'Fail';
                $registrar = $_SESSION['username'] ?? $_SESSION['full_name'] ?? 'registrar';

                /* ============================================================
                   UPDATE
                   ============================================================ */
                $upd = $conn->prepare("
                    UPDATE student_courses 
                    SET grade           = :new_grade, 
                        grade_point     = :new_gp, 
                        status          = :status,
                        grade_status    = 'Approved',
                        approved_at     = NOW(),
                        approved_by     = :registrar1,
                        last_edited_at  = NOW(),
                        last_edited_by  = :registrar2,
                        edit_reason     = :reason
                    WHERE id = :id");

                $upd->execute([
                    'new_grade' => $new_grade,
                    'new_gp' => $new_gp,
                    'status' => $status,
                    'registrar1' => $registrar,
                    'registrar2' => $registrar,
                    'reason' => $reason,
                    'id' => $existing['id']
                ]);

                /* ---------- Log to audit table (PDO) ---------- */
                $audit = $conn->prepare("
                    INSERT INTO grade_audit_log 
                        (student_course_id, student_id, course_code,
                         ac_year, semester, old_grade, new_grade,
                         old_grade_point, new_grade_point,
                         action, action_by, reason)
                    VALUES (:sc_id, :sid, :ccode, :ayear, :sem, :old_grade, :new_grade, 
                            :old_gp, :new_gp, 'registrar_change', :action_by, :reason)");

                $audit->execute([
                    'sc_id' => $existing['id'],
                    'sid' => $student_id,
                    'ccode' => $course_code,
                    'ayear' => $ac_year,
                    'sem' => $semester,
                    'old_grade' => $existing['grade'],
                    'new_grade' => $new_grade,
                    'old_gp' => $existing['grade_point'],
                    'new_gp' => $new_gp,
                    'action_by' => $registrar,
                    'reason' => $reason
                ]);

                $message = "✅ <strong>Grade changed successfully.</strong><br>"
                         . "Student: <strong>" . htmlspecialchars($student_id) . "</strong><br>"
                         . "Course: <strong>" . htmlspecialchars($course_code) . "</strong><br>"
                         . "Old Grade: <strong>" . htmlspecialchars($existing['grade'] ?: '—') 
                         . "</strong> (" . htmlspecialchars($existing['grade_point'] ?: '—') . ")<br>"
                         . "New Grade: <strong>" . htmlspecialchars($new_grade) 
                         . "</strong> (" . htmlspecialchars($new_gp) . ")<br>"
                         . "Reason: <em>" . htmlspecialchars($reason) . "</em>";
                $message_type = "success";
            }
        } catch (PDOException $e) {
            $message = "❌ Database Error: " . htmlspecialchars($e->getMessage());
            $message_type = "error";
        }
    }
}

include 'header.php';
include 'mygradechangerf.php';
include 'footer.php';
?>