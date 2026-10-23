<?php
/* ============================================================
   REGISTRAR — CHANGE GRADE
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

        /* ---------- Fetch existing enrollment ---------- */
        $fetch = $conn->prepare("
            SELECT id, grade, grade_point, grade_status 
            FROM student_courses 
            WHERE student_id = ? 
              AND course_code = ? 
              AND ac_year = ? 
              AND semester = ?
            LIMIT 1");
        $fetch->bind_param("sssi", $student_id, $course_code, $ac_year, $semester);
        $fetch->execute();
        $existing = $fetch->get_result()->fetch_assoc();
        $fetch->close();

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
               UPDATE — 7 placeholders = "sdssssi"
               ============================================================ */
            $upd = $conn->prepare("
                UPDATE student_courses 
                SET grade           = ?, 
                    grade_point     = ?, 
                    status          = ?,
                    grade_status    = 'Approved',
                    approved_at     = NOW(),
                    approved_by     = ?,
                    last_edited_at  = NOW(),
                    last_edited_by  = ?,
                    edit_reason     = ?
                WHERE id = ?");

            /* Type letters:
                 s → $new_grade
                 d → $new_gp
                 s → $status
                 s → $registrar (approved_by)
                 s → $registrar (last_edited_by)
                 s → $reason
                 i → $existing['id']
               Total: "sdssssi"  (7 characters)
            */
            $upd->bind_param("sdssssi",
                $new_grade,
                $new_gp,
                $status,
                $registrar,
                $registrar,
                $reason,
                $existing['id']);

            try {
                if ($upd->execute()) {

                    /* ---------- Log to audit table ---------- */
                    /* Count: i s s s i s s d s s s = "isssissdsss" (11 letters) */
                    $audit = $conn->prepare("
                        INSERT INTO grade_audit_log 
                            (student_course_id, student_id, course_code,
                             ac_year, semester, old_grade, new_grade,
                             old_grade_point, new_grade_point,
                             action, action_by, reason)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'registrar_change', ?, ?)");

                    /* Count placeholders: 9 ? before 'registrar_change', then 2 more = 11 */
                    /* Type letters:
                         i → $existing['id']
                         s → $student_id
                         s → $course_code
                         s → $ac_year
                         i → $semester
                         s → $existing['grade']
                         s → $new_grade
                         d → $existing['grade_point']
                         d → $new_gp
                         s → $registrar
                         s → $reason
                       Total: "isssissddss" (11 letters)
                    */
                    $audit->bind_param("isssissddss",
                        $existing['id'],
                        $student_id,
                        $course_code,
                        $ac_year,
                        $semester,
                        $existing['grade'],
                        $new_grade,
                        $existing['grade_point'],
                        $new_gp,
                        $registrar,
                        $reason);
                    $audit->execute();
                    $audit->close();

                    $message = "✅ <strong>Grade changed successfully.</strong><br>"
                             . "Student: <strong>" . htmlspecialchars($student_id) . "</strong><br>"
                             . "Course: <strong>" . htmlspecialchars($course_code) . "</strong><br>"
                             . "Old Grade: <strong>" . htmlspecialchars($existing['grade'] ?: '—') 
                             . "</strong> (" . htmlspecialchars($existing['grade_point'] ?: '—') . ")<br>"
                             . "New Grade: <strong>" . htmlspecialchars($new_grade) 
                             . "</strong> (" . htmlspecialchars($new_gp) . ")<br>"
                             . "Reason: <em>" . htmlspecialchars($reason) . "</em>";
                    $message_type = "success";

                } else {
                    $message = "❌ Database error: " . htmlspecialchars($conn->error);
                    $message_type = "error";
                }
            } catch (mysqli_sql_exception $e) {
                $message = "❌ Exception: " . htmlspecialchars($e->getMessage());
                $message_type = "error";
            }
            $upd->close();
        }
    }
}

include 'header.php';
include 'mygradechangerf.php';
include 'footer.php';