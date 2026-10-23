<?php
/* ============================================================
   MY GRADE APPROVE — REGISTRAR APPROVAL PAGE
   Approves/Rejects submitted grades from teachers
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

$message      = "";
$message_type = "";

/* ============================================================
   FILTERS
   ============================================================ */
$f_college    = trim($_GET['college']    ?? '');
$f_department = trim($_GET['department'] ?? '');
$f_year       = (int)($_GET['year']      ?? 0);

/* ============================================================
   HANDLE POST — APPROVE / REJECT
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    $action      = $_POST['action'];
    $course_code = trim($_POST['course_code'] ?? '');
    $ac_year     = trim($_POST['ac_year']     ?? '');
    $semester    = (int)($_POST['semester']   ?? 0);
    $registrar   = $_SESSION['username'] ?? $_SESSION['full_name'] ?? 'registrar';
    $reason      = trim($_POST['reason'] ?? '');

    /* ============================================================
       ACTION 1: APPROVE ALL
       ============================================================ */
    if ($action === 'approve') {

        if ($course_code === '' || $ac_year === '' || $semester === 0) {
            $message = "⚠️ Missing parameters.";
            $message_type = "error";
        } else {

            $upd = $conn->prepare("
                UPDATE student_courses 
                SET grade_status = 'Approved',
                    approved_at = NOW(),
                    approved_by = ?
                WHERE course_code = ? AND ac_year = ? AND semester = ?
                  AND grade_status = 'Submitted'");
            $upd->bind_param("sssi", $registrar, $course_code, $ac_year, $semester);

            if ($upd->execute()) {
                $count = $upd->affected_rows;

                $audit = $conn->prepare("
                    INSERT INTO grade_audit_log 
                        (student_id, course_code, ac_year, semester,
                         action, action_by, reason)
                    VALUES ('*', ?, ?, ?, 'approved', ?, ?)");
                $reason_txt = "$count grades approved by registrar";
                $audit->bind_param("ssiss", $course_code, $ac_year, $semester, $registrar, $reason_txt);
                $audit->execute();
                $audit->close();

                $message = "✅ <strong>Approved $count grades!</strong><br>"
                         . "Course: <strong>" . htmlspecialchars($course_code) . "</strong><br>"
                         . "Year: <strong>" . htmlspecialchars($ac_year) . "</strong> — "
                         . "Semester <strong>" . htmlspecialchars($semester) . "</strong>";
                $message_type = "success";
            } else {
                $message = "❌ Error: " . $conn->error;
                $message_type = "error";
            }
            $upd->close();
        }
    }

    /* ============================================================
       ACTION 2: REJECT / RETURN TO TEACHER
       ============================================================ */
    elseif ($action === 'reject') {

        if ($course_code === '' || $ac_year === '' || $semester === 0) {
            $message = "⚠️ Missing parameters.";
            $message_type = "error";
        } elseif ($reason === '') {
            $message = "⚠️ Please provide a reason for rejection.";
            $message_type = "error";
        } else {

            $upd = $conn->prepare("
                UPDATE student_courses 
                SET grade_status = 'Pending',
                    submitted_at = NULL,
                    submitted_by = NULL,
                    rejection_reason = ?
                WHERE course_code = ? AND ac_year = ? AND semester = ?
                  AND grade_status = 'Submitted'");
            $upd->bind_param("sssi", $reason, $course_code, $ac_year, $semester);

            if ($upd->execute()) {
                $count = $upd->affected_rows;

                $audit = $conn->prepare("
                    INSERT INTO grade_audit_log 
                        (student_id, course_code, ac_year, semester,
                         action, action_by, reason)
                    VALUES ('*', ?, ?, ?, 'rejected', ?, ?)");
                $audit->bind_param("ssiss", $course_code, $ac_year, $semester, $registrar, $reason);
                $audit->execute();
                $audit->close();

                $message = "↩️ <strong>Returned $count grades to teacher.</strong><br>"
                         . "Course: <strong>" . htmlspecialchars($course_code) . "</strong><br>"
                         . "Reason: " . htmlspecialchars($reason);
                $message_type = "success";
            } else {
                $message = "❌ Error: " . $conn->error;
                $message_type = "error";
            }
            $upd->close();
        }
    }
}

/* ============================================================
   LOAD COLLEGES (for filter)
   ============================================================ */
$colleges = [];
$r = $conn->query("SELECT code, name FROM college ORDER BY name");
if ($r) while ($row = $r->fetch_assoc()) $colleges[] = $row;

/* ============================================================
   LOAD DEPARTMENTS (for selected college)
   ============================================================ */
$departments = [];
if ($f_college !== '') {
    $stmt = $conn->prepare("
        SELECT name FROM departments 
        WHERE college_code = ? 
        ORDER BY name");
    $stmt->bind_param("s", $f_college);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $departments[] = $row['name'];
    $stmt->close();
}

/* ============================================================
   FETCH SUBMITTED GROUPS (waiting for approval)
   ============================================================ */
$submittedGroups = [];

$sql = "
    SELECT 
        sc.course_code,
        COALESCE(a.course_name, sc.course_code) AS course_name,
        sc.ac_year,
        sc.semester,
        a.department,
        a.course_year,
        a.college,
        sc.submitted_by,
        sc.submitted_at,
        COUNT(*) AS total,
        SUM(CASE WHEN sc.grade IS NULL THEN 1 ELSE 0 END) AS missing,
        SUM(CASE WHEN sc.grade IS NOT NULL THEN 1 ELSE 0 END) AS graded
    FROM student_courses sc
    LEFT JOIN addcourse a ON a.course_code = sc.course_code
    WHERE sc.grade_status = 'Submitted'";

$params = [];
$types  = '';

if ($f_college !== '') {
    $sql .= " AND a.college = ?";
    $params[] = $f_college;
    $types .= 's';
}
if ($f_department !== '') {
    $sql .= " AND a.department = ?";
    $params[] = $f_department;
    $types .= 's';
}
if ($f_year > 0) {
    $sql .= " AND a.course_year = ?";
    $params[] = $f_year;
    $types .= 'i';
}

$sql .= " GROUP BY sc.course_code, sc.ac_year, sc.semester, 
                 a.course_name, a.department, a.course_year, a.college,
                 sc.submitted_by, sc.submitted_at
          ORDER BY sc.submitted_at DESC, sc.ac_year DESC, sc.semester DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $submittedGroups[] = $row;
}
$stmt->close();

/* ============================================================
   LOAD HEADER + VIEW + FOOTER
   ============================================================ */
include 'header.php';
include 'mygradeapprovef.php';
include 'footer.php';