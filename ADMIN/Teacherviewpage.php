<?php
session_start();
require 'db.php';

// 1. Admin Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginf.php");
    exit();
}

/* ============================================================
   COLLEGE NAME MAP
   ============================================================ */
$college_names = [
    'technology' => 'Engineering and Technology',
    'medicine'   => 'Medicine and Health Sciences',
    'economics'  => 'Business and Economics',
    'naturals'   => 'Natural and Computational Sciences',
    'food'       => 'Agriculture, Food, Environment',
    'social'     => 'Social Science and Humanities',
    'behavioral' => 'Education and Behavioral Science',
    'law'        => 'School of Law',
];

/* ============================================================
   READ FILTERS
   ============================================================ */
$f_college    = trim($_GET['college']    ?? '');
$f_department = trim($_GET['department'] ?? '');
$f_year       = (int)($_GET['year']      ?? 0);
$f_semester   = (int)($_GET['semester']  ?? 0);
$f_search     = trim($_GET['search']     ?? '');

/* ============================================================
   LOAD COLLEGES (PDO)
   ============================================================ */
$colleges = [];
try {
    $r = $conn->query("SELECT code, name FROM college ORDER BY name");
    $colleges = $r->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

if (empty($colleges)) {
    foreach ($college_names as $code => $name) {
        $colleges[] = ['code' => $code, 'name' => $name];
    }
}

/* ============================================================
   LOAD DEPARTMENTS FOR SELECTED COLLEGE (PDO)
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
   BUILD STUDENT QUERY (PDO)
   ============================================================ */
$sql = "SELECT id, student_id, firstname, father_name, grandfather_name,
               age, gender, phone, region, secondary_school,
               college, department, `year`, current_semester, created_at
        FROM users2
        WHERE 1=1";

$params = [];

if ($f_college !== '') {
    $sql .= " AND college = :college";
    $params['college'] = $f_college;
}
if ($f_department !== '') {
    $sql .= " AND department = :department";
    $params['department'] = $f_department;
}
if ($f_year > 0) {
    $sql .= " AND `year` = :year";
    $params['year'] = $f_year;
}
if ($f_semester > 0) {
    $sql .= " AND current_semester = :semester";
    $params['semester'] = $f_semester;
}
if ($f_search !== '') {
    $sql .= " AND (student_id LIKE :search1 
                OR firstname   LIKE :search2 
                OR father_name LIKE :search3
                OR grandfather_name LIKE :search4)";
    $like = '%' . $f_search . '%';
    $params['search1'] = $like;
    $params['search2'] = $like;
    $params['search3'] = $like;
    $params['search4'] = $like;
}

$sql .= " ORDER BY student_id ASC";

$students = [];
try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

/* ============================================================
   COUNT TOTAL + FILTERED (PDO)
   ============================================================ */
$totalAll = 0;
try {
    $totalAll = (int)$conn->query("SELECT COUNT(*) FROM users2")->fetchColumn();
} catch (PDOException $e) {}

$totalFiltered = count($students);

/* ============================================================
   NOW INCLUDE HEADER + VIEW + FOOTER
   ============================================================ */
include 'header.php';
include 'viewf.php';
include 'footer.php';
?>