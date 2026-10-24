<?php
/* ============================================================
   MY GRADE APPROVE — REGISTRAR APPROVAL PAGE (PDO Version)
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
       ACTION 1: APPROVE ALL (PDO)
       ============================================================ */
    if ($action === 'approve') {

        if ($course_code === '' || $ac_year === '' || $semester === 0) {
            $message = "⚠️ Missing parameters.";
            $message_type = "error";
        } else {
            try {
                // Update only Pending grades
                $upd = $conn->prepare("
                    UPDATE student_courses 
                    SET grade_status = 'Approved',
                        approved_at = NOW(),
                        approved_by = :registrar
                    WHERE course_code = :ccode 
                      AND ac_year = :ayear 
                      AND semester = :sem
                      AND grade_status = 'Pending'");
                $upd->execute([
                    'registrar' => $registrar,
                    'ccode' => $course_code,
                    'ayear' => $ac_year,
                    'sem' => $semester
                ]);
                
                $count = $upd->rowCount();

                // Log the approval
                $audit = $conn->prepare("
                    INSERT INTO grade_audit_log 
                        (student_id, course_code, ac_year, semester,
                         action, action_by, reason)
                    VALUES ('*', :ccode, :ayear, :sem, 'approved', :registrar, :reason)");
                $reason_txt = "$count grades approved by registrar";
                $audit->execute([
                    'ccode' => $course_code,
                    'ayear' => $ac_year,
                    'sem' => $semester,
                    'registrar' => $registrar,
                    'reason' => $reason_txt
                ]);

                $message = "✅ <strong>Approved $count grades!</strong><br>"
                         . "Course: <strong>" . htmlspecialchars($course_code) . "</strong><br>"
                         . "Year: <strong>" . htmlspecialchars($ac_year) . "</strong> — "
                         . "Semester <strong>" . htmlspecialchars($semester) . "</strong>";
                $message_type = "success";
            } catch (PDOException $e) {
                $message = "❌ Error: " . $e->getMessage();
                $message_type = "error";
            }
        }
    }

    /* ============================================================
       ACTION 2: REJECT / RETURN TO TEACHER (PDO)
       ============================================================ */
    elseif ($action === 'reject') {

        if ($course_code === '' || $ac_year === '' || $semester === 0) {
            $message = "⚠️ Missing parameters.";
            $message_type = "error";
        } elseif ($reason === '') {
            $message = "⚠️ Please provide a reason for rejection.";
            $message_type = "error";
        } else {
            try {
                $upd = $conn->prepare("
                    UPDATE student_courses 
                    SET grade_status = 'Pending',
                        submitted_at = NULL,
                        submitted_by = NULL,
                        rejection_reason = :reason
                    WHERE course_code = :ccode 
                      AND ac_year = :ayear 
                      AND semester = :sem
                      AND grade_status = 'Pending'");
                $upd->execute([
                    'reason' => $reason,
                    'ccode' => $course_code,
                    'ayear' => $ac_year,
                    'sem' => $semester
                ]);

                $count = $upd->rowCount();

                $audit = $conn->prepare("
                    INSERT INTO grade_audit_log 
                        (student_id, course_code, ac_year, semester,
                         action, action_by, reason)
                    VALUES ('*', :ccode, :ayear, :sem, 'rejected', :registrar, :reason)");
                $audit->execute([
                    'ccode' => $course_code,
                    'ayear' => $ac_year,
                    'sem' => $semester,
                    'registrar' => $registrar,
                    'reason' => $reason
                ]);

                $message = "↩️ <strong>Returned $count grades to teacher.</strong><br>"
                         . "Course: <strong>" . htmlspecialchars($course_code) . "</strong><br>"
                         . "Reason: " . htmlspecialchars($reason);
                $message_type = "success";
            } catch (PDOException $e) {
                $message = "❌ Error: " . $e->getMessage();
                $message_type = "error";
            }
        }
    }
}

/* ============================================================
   LOAD COLLEGES (FOR FILTER) - PDO
   ============================================================ */
$colleges = [];
try {
    $r = $conn->query("SELECT code, name FROM college ORDER BY name");
    $colleges = $r->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

/* ============================================================
   LOAD DEPARTMENTS (FOR SELECTED COLLEGE) - PDO
   ============================================================ */
$departments = [];
if ($f_college !== '') {
    try {
        $stmt = $conn->prepare("SELECT name FROM departments WHERE college_code = :cc ORDER BY name");
        $stmt->execute(['cc' => $f_college]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $departments[] = $row['name'];
        }
    } catch (PDOException $e) {}
}

/* ============================================================
   FETCH SUBMITTED GROUPS (WAITING FOR APPROVAL) - PDO
   ============================================================ */
$submittedGroups = [];

try {
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
        WHERE sc.grade_status = 'Pending'";

    $params = [];

    if ($f_college !== '') {
        $sql .= " AND a.college = :college";
        $params['college'] = $f_college;
    }
    if ($f_department !== '') {
        $sql .= " AND a.department = :department";
        $params['department'] = $f_department;
    }
    if ($f_year > 0) {
        $sql .= " AND a.course_year = :year";
        $params['year'] = $f_year;
    }

    $sql .= " GROUP BY sc.course_code, sc.ac_year, sc.semester, 
                     a.course_name, a.department, a.course_year, a.college,
                     sc.submitted_by, sc.submitted_at
              ORDER BY sc.submitted_at DESC, sc.ac_year DESC, sc.semester DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $submittedGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error loading grades: " . $e->getMessage();
}

/* ============================================================
   LOAD HEADER + VIEW + FOOTER
   ============================================================ */
include 'header.php';
include 'mygradeapprovef.php';
include 'footer.php';
?>