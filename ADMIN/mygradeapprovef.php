<?php
/* ============================================================
   REGISTRAR — GRADE APPROVALS (LOGIC) - PDO Version
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

/* ============================================================
   ACCESS CONTROL
   ============================================================ */
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'registrar', 'head'])) {
    header("Location: loginf.php");
    exit();
}

$message      = "";
$message_type = "";

/* ============================================================
   FILTERS
   ============================================================ */
$f_college    = trim($_GET['college']    ?? '');
$f_department = trim($_GET['department'] ?? '');
$f_year       = (int)($_GET['year']      ?? 0);

/* ============================================================
   HANDLE APPROVE / REJECT ACTIONS (PDO)
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action      = $_POST['action'];
    $course_code = trim($_POST['course_code'] ?? '');
    $ac_year     = trim($_POST['ac_year'] ?? '');
    $semester    = (int)($_POST['semester'] ?? 0);
    $reason      = trim($_POST['reason'] ?? '');
    $registrar   = $_SESSION['username'] ?? 'registrar';

    if ($course_code && $ac_year && $semester) {
        try {
            if ($action === 'approve') {
                // Approve all pending grades for this course
                $upd = $conn->prepare("
                    UPDATE student_courses 
                    SET grade_status = 'Approved', 
                        approved_at = NOW(), 
                        approved_by = :approved_by 
                    WHERE course_code = :ccode 
                      AND ac_year = :ayear 
                      AND semester = :sem 
                      AND grade_status = 'Pending'");
                $upd->execute([
                    'approved_by' => $registrar,
                    'ccode' => $course_code,
                    'ayear' => $ac_year,
                    'sem' => $semester
                ]);
                
                $count = $upd->rowCount();
                $message = "✅ $count grade(s) approved successfully.";
                $message_type = "success";

                // Log the approval
                $log = $conn->prepare("
                    INSERT INTO grade_audit_log 
                    (student_course_id, student_id, course_code, ac_year, semester, action, action_by, reason)
                    VALUES (NULL, '*', :ccode, :ayear, :sem, 'approved', :action_by, :reason)");
                $log->execute([
                    'ccode' => $course_code,
                    'ayear' => $ac_year,
                    'sem' => $semester,
                    'action_by' => $registrar,
                    'reason' => "$count grades approved by registrar"
                ]);

            } elseif ($action === 'reject') {
                if ($reason === '') {
                    $message = "⚠️ A reason is required to reject grades.";
                    $message_type = "error";
                } else {
                    // Reject all pending grades for this course
                    $upd = $conn->prepare("
                        UPDATE student_courses 
                        SET grade_status = 'Pending', 
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
                    $message = "↩️ $count grade(s) returned to teacher.";
                    $message_type = "success";

                    // Log the rejection
                    $log = $conn->prepare("
                        INSERT INTO grade_audit_log 
                        (student_course_id, student_id, course_code, ac_year, semester, action, action_by, reason)
                        VALUES (NULL, '*', :ccode, :ayear, :sem, 'rejected', :action_by, :reason)");
                    $log->execute([
                        'ccode' => $course_code,
                        'ayear' => $ac_year,
                        'sem' => $semester,
                        'action_by' => $registrar,
                        'reason' => $reason
                    ]);
                }
            }
        } catch (PDOException $e) {
            $message = "❌ Database Error: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

/* ============================================================
   LOAD COLLEGES (FOR FILTER)
   ============================================================ */
$colleges = [];
try {
    $r = $conn->query("SELECT code, name FROM college ORDER BY name");
    $colleges = $r->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

/* ============================================================
   LOAD DEPARTMENTS (FOR FILTER)
   ============================================================ */
$departments = [];
try {
    $sqlD = "SELECT DISTINCT department FROM users2 WHERE department IS NOT NULL AND department <> ''";
    $paramsD = [];
    if ($f_college) {
        $sqlD .= " AND college = :college";
        $paramsD['college'] = $f_college;
    }
    $sqlD .= " ORDER BY department";
    $stmtD = $conn->prepare($sqlD);
    $stmtD->execute($paramsD);
    while ($row = $stmtD->fetch(PDO::FETCH_ASSOC)) {
        $departments[] = $row['department'];
    }
} catch (PDOException $e) {}

/* ============================================================
   FETCH SUBMITTED GROUPS
   ============================================================ */
$submittedGroups = [];
try {
    $sql = "
        SELECT 
            sc.course_code,
            a.course_name,
            sc.ac_year,
            sc.semester,
            u.department,
            COUNT(sc.id) AS total,
            SUM(CASE WHEN sc.grade_status = 'Pending' THEN 1 ELSE 0 END) AS graded,
            MAX(sc.submitted_by) AS submitted_by,
            MAX(sc.submitted_at) AS submitted_at
        FROM student_courses sc
        LEFT JOIN addcourse a ON a.course_code = sc.course_code
        LEFT JOIN users2 u ON u.student_id = sc.student_id
        WHERE sc.grade_status = 'Pending'
          AND sc.grade IS NOT NULL
          AND sc.grade <> ''";
    
    $params = [];
    
    if ($f_college) {
        $sql .= " AND u.college = :college";
        $params['college'] = $f_college;
    }
    if ($f_department) {
        $sql .= " AND u.department = :department";
        $params['department'] = $f_department;
    }
    if ($f_year) {
        $sql .= " AND a.course_year = :year";
        $params['year'] = $f_year;
    }
    
    $sql .= " GROUP BY sc.course_code, a.course_name, sc.ac_year, sc.semester, u.department
              ORDER BY sc.ac_year DESC, sc.semester DESC, sc.course_code ASC";
    
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