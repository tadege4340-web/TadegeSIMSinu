<?php
/* ============================================================
   ADMIN — VIEW ALL STUDENT GRADES (LOGIC) - PDO Version
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

/* ============================================================
   ADMIN CHECK
   ============================================================ */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginf.php");
    exit();
}

/* ============================================================
   FILTERS
   ============================================================ */
$filter_college = trim($_GET['college'] ?? '');
$filter_dept    = trim($_GET['dept']    ?? '');
$filter_year    = trim($_GET['ac_year'] ?? '');
$search         = trim($_GET['q']       ?? '');

/* ============================================================
   STEP 1 — GET ALL SEMESTERS (PDO)
   ============================================================ */
$sqlSem = "SELECT 
                sc.student_id,
                sc.ac_year,
                sc.semester,
                sc.grade_point,
                a.credit_hour
            FROM student_courses sc
            JOIN addcourse a ON a.course_code = sc.course_code
            LEFT JOIN users2 u ON u.student_id = sc.student_id
            WHERE sc.grade_point IS NOT NULL
              AND sc.ac_year IS NOT NULL
              AND sc.semester IS NOT NULL";

$params = [];

if ($filter_college !== '') {
    $sqlSem .= " AND u.college = :college";
    $params['college'] = $filter_college;
}
if ($filter_dept !== '') {
    $sqlSem .= " AND u.department = :dept";
    $params['dept'] = $filter_dept;
}
if ($filter_year !== '') {
    $sqlSem .= " AND sc.ac_year = :ac_year";
    $params['ac_year'] = $filter_year;
}

try {
    $stmt = $conn->prepare($sqlSem);
    $stmt->execute($params);
    $semRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $semRows = [];
}

/* ============================================================
   STEP 2 — GROUP BY (student, year, semester) & COMPUTE SGPA
   ============================================================ */
$sgpaByKey = [];  

$grouped = [];
foreach ($semRows as $r) {
    $key = $r['student_id'] . '|' . $r['ac_year'] . '|' . $r['semester'];
    $grouped[$key][] = $r;
}

foreach ($grouped as $key => $rows) {
    $totalPoints = 0;
    $totalCredits = 0;
    foreach ($rows as $r) {
        $totalPoints += $r['grade_point'] * $r['credit_hour'];
        $totalCredits += $r['credit_hour'];
    }
    $sgpaByKey[$key] = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;
}

/* ============================================================
   STEP 3 — COMPUTE CGPA PER STUDENT
   ============================================================ */
$cgpaByStudent = [];

$studentSemesters = [];
foreach ($grouped as $key => $rows) {
    list($sid, $year, $sem) = explode('|', $key);
    $studentSemesters[$sid][$year . '|' . $sem] = $rows;
}

foreach ($studentSemesters as $sid => $semesters) {
    ksort($semesters); 
    $cumPoints = 0;
    $cumCredits = 0;
    foreach ($semesters as $semKey => $rows) {
        foreach ($rows as $r) {
            $cumPoints += $r['grade_point'] * $r['credit_hour'];
            $cumCredits += $r['credit_hour'];
        }
    }
    $cgpaByStudent[$sid] = $cumCredits > 0 ? round($cumPoints / $cumCredits, 2) : 0;
}

/* ============================================================
   STEP 4 — MAIN QUERY: fetch all rows for display (PDO)
   ============================================================ */
$sql = "SELECT 
            sc.id,
            sc.student_id,
            sc.course_code,
            sc.ac_year,
            sc.semester,
            sc.grade,
            sc.grade_point,
            sc.status,
            u.firstname,
            u.father_name,
            u.college,
            u.department,
            a.course_name,
            a.credit_hour,
            a.category
        FROM student_courses sc
        LEFT JOIN users2 u ON u.student_id = sc.student_id
        LEFT JOIN addcourse a ON a.course_code = sc.course_code
        WHERE 1=1";

$params = [];

if ($filter_college !== '') {
    $sql .= " AND u.college = :college";
    $params['college'] = $filter_college;
}
if ($filter_dept !== '') {
    $sql .= " AND u.department = :dept";
    $params['dept'] = $filter_dept;
}
if ($filter_year !== '') {
    $sql .= " AND sc.ac_year = :ac_year";
    $params['ac_year'] = $filter_year;
}
if ($search !== '') {
    $sql .= " AND (sc.student_id LIKE :search OR u.firstname LIKE :search OR u.father_name LIKE :search)";
    $params['search'] = "%$search%";
}

$sql .= " ORDER BY sc.student_id ASC, sc.ac_year DESC, sc.semester DESC, sc.course_code ASC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $grades = [];
}

/* ============================================================
   STEP 5 — ATTACH SGPA + CGPA TO EACH ROW
   ============================================================ */
foreach ($grades as &$g) {
    $key = $g['student_id'] . '|' . $g['ac_year'] . '|' . $g['semester'];
    $g['sgpa'] = $sgpaByKey[$key] ?? 0;
    $g['cgpa'] = $cgpaByStudent[$g['student_id']] ?? 0;
}
unset($g);

$total_rows = count($grades);

/* ============================================================
   LOAD HEADER
   ============================================================ */
include 'header.php';

/* ============================================================
   LOAD VIEW
   ============================================================ */
define('FROM_MYGRADEVIEW', true);
include 'mygradeviewf.php';

/* ============================================================
   LOAD FOOTER
   ============================================================ */
include 'footer.php';
?>