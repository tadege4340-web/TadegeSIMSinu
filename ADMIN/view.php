<?php
session_start();
require 'db.php';

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
$f_semester   = (int)($_GET['semester']  ?? 0); // 🔥 SEMESTER FILTER
$f_search     = trim($_GET['search']     ?? '');

/* ============================================================
   LOAD COLLEGES (from DB — falls back to static map)
   ============================================================ */
$colleges = [];
$r = $conn->query("SELECT code, name FROM college ORDER BY name");
if ($r) {
    while ($row = $r->fetch_assoc()) $colleges[] = $row;
}
if (empty($colleges)) {
    foreach ($college_names as $code => $name) {
        $colleges[] = ['code' => $code, 'name' => $name];
    }
}

/* ============================================================
   LOAD DEPARTMENTS FOR SELECTED COLLEGE
   ============================================================ */
$departments = [];
if ($f_college !== '') {
    $stmt = $conn->prepare("
        SELECT name FROM departments 
        WHERE college_code = ? 
        ORDER BY name
    ");
    $stmt->bind_param("s", $f_college);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $departments[] = $row['name'];
    $stmt->close();
}

/* ============================================================
   BUILD STUDENT QUERY
   ============================================================ */
// 🔥 Added current_semester to the SELECT statement
$sql = "SELECT id, student_id, firstname, father_name, grandfather_name,
               age, gender, phone, region, secondary_school,
               college, department, `year`, current_semester, created_at
        FROM users2
        WHERE 1=1";

$params = [];
$types  = '';

if ($f_college !== '') {
    $sql .= " AND college = ?";
    $params[] = $f_college;
    $types .= 's';
}
if ($f_department !== '') {
    $sql .= " AND department = ?";
    $params[] = $f_department;
    $types .= 's';
}
if ($f_year > 0) {
    $sql .= " AND `year` = ?";
    $params[] = $f_year;
    $types .= 'i';
}
// 🔥 Added the actual SQL filter for semester
if ($f_semester > 0) {
    $sql .= " AND current_semester = ?";
    $params[] = $f_semester;
    $types .= 'i';
}
if ($f_search !== '') {
    $sql .= " AND (student_id LIKE ? 
                OR firstname   LIKE ? 
                OR father_name LIKE ?
                OR grandfather_name LIKE ?)";
    $like = '%' . $f_search . '%';
    $params[] = $like; $params[] = $like;
    $params[] = $like; $params[] = $like;
    $types .= 'ssss';
}

$sql .= " ORDER BY student_id ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

/* ============================================================
   COUNT TOTAL + FILTERED
   ============================================================ */
$totalAll      = $conn->query("SELECT COUNT(*) AS c FROM users2")->fetch_assoc()['c'];
$totalFiltered = $result->num_rows;

/* ============================================================
   NOW INCLUDE HEADER + VIEW + FOOTER
   ============================================================ */
include 'header.php';
include 'viewf.php';        
include 'footer.php';
?>