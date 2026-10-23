<?php
/* ============================================================
   ADMIN — VIEW ALL STUDENT GRADES (LOGIC)
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
   STEP 1 — GET ALL SEMESTERS (for SGPA/CGPA computation)
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
$types  = '';

if ($filter_college !== '') {
    $sqlSem .= " AND u.college = ?";
    $params[] = $filter_college;
    $types .= 's';
}
if ($filter_dept !== '') {
    $sqlSem .= " AND u.department = ?";
    $params[] = $filter_dept;
    $types .= 's';
}
if ($filter_year !== '') {
    $sqlSem .= " AND sc.ac_year = ?";
    $params[] = $filter_year;
    $types .= 's';
}

$stmt = $conn->prepare($sqlSem);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$semRows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* ============================================================
   STEP 2 — GROUP BY (student, year, semester) & COMPUTE SGPA
   ============================================================ */
$sgpaByKey = [];  // key: "student_id|ac_year|semester" => sgpa

// Group rows
$grouped = [];
foreach ($semRows as $r) {
    $key = $r['student_id'] . '|' . $r['ac_year'] . '|' . $r['semester'];
    $grouped[$key][] = $r;
}

// Compute SGPA for each group
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
   STEP 3 — COMPUTE CGPA PER STUDENT (cumulative across semesters)
   ============================================================ */
$cgpaByStudent = [];  // key: student_id => cgpa

// Group SGPA-relevant data by student
$studentSemesters = [];
foreach ($grouped as $key => $rows) {
    list($sid, $year, $sem) = explode('|', $key);
    $studentSemesters[$sid][$year . '|' . $sem] = $rows;
}

// For each student, compute cumulative GPA (order by year + sem ascending)
foreach ($studentSemesters as $sid => $semesters) {
    ksort($semesters); // ascending order

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
   STEP 4 — MAIN QUERY: fetch all rows for display
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
$types  = '';

if ($filter_college !== '') {
    $sql .= " AND u.college = ?";
    $params[] = $filter_college;
    $types .= 's';
}
if ($filter_dept !== '') {
    $sql .= " AND u.department = ?";
    $params[] = $filter_dept;
    $types .= 's';
}
if ($filter_year !== '') {
    $sql .= " AND sc.ac_year = ?";
    $params[] = $filter_year;
    $types .= 's';
}
if ($search !== '') {
    $sql .= " AND (sc.student_id LIKE ? OR u.firstname LIKE ? OR u.father_name LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
}

$sql .= " ORDER BY sc.student_id ASC, sc.ac_year DESC, sc.semester DESC, sc.course_code ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$grades = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

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
   LOAD HEADER (opens <div class="main-content">)
   ============================================================ */
include 'header.php';

/* ============================================================
   LOAD VIEW
   ============================================================ */
define('FROM_MYGRADEVIEW', true);
include 'mygradeviewf.php';

/* ============================================================
   LOAD FOOTER (closes </div>)
   ============================================================ */
include 'footer.php';