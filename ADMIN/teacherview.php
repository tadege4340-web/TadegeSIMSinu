<?php
require 'db.php';

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
$f_position   = trim($_GET['position']   ?? '');
$f_status     = trim($_GET['status']     ?? '');
$f_search     = trim($_GET['search']     ?? '');

/* ============================================================
   LOAD COLLEGES
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
   LOAD DISTINCT POSITIONS (for filter dropdown)
   ============================================================ */
$positions = [];
$r = $conn->query("SELECT DISTINCT position FROM teachers 
                   WHERE position IS NOT NULL AND position <> '' 
                   ORDER BY position");
if ($r) while ($row = $r->fetch_assoc()) $positions[] = $row['position'];

/* ============================================================
   LOAD DISTINCT STATUSES (for filter dropdown)
   ============================================================ */
$statuses = [];
$r = $conn->query("SELECT DISTINCT status FROM teachers 
                   WHERE status IS NOT NULL AND status <> '' 
                   ORDER BY status");
if ($r) while ($row = $r->fetch_assoc()) $statuses[] = $row['status'];

/* ============================================================
   BUILD TEACHER QUERY
   ============================================================ */
$sql = "SELECT id, full_name, age, gender, email, phone, address,
               college, department, qualification, position,
               hire_date, status
        FROM teachers
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
if ($f_position !== '') {
    $sql .= " AND position = ?";
    $params[] = $f_position;
    $types .= 's';
}
if ($f_status !== '') {
    $sql .= " AND status = ?";
    $params[] = $f_status;
    $types .= 's';
}
if ($f_search !== '') {
    $sql .= " AND (full_name LIKE ? 
                OR email     LIKE ? 
                OR phone     LIKE ?
                OR id        LIKE ?)";
    $like = '%' . $f_search . '%';
    $params[] = $like; $params[] = $like;
    $params[] = $like; $params[] = $like;
    $types .= 'ssss';
}

$sql .= " ORDER BY id ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

/* ============================================================
   COUNT TOTALS
   ============================================================ */
$totalAll      = $conn->query("SELECT COUNT(*) AS c FROM teachers")->fetch_assoc()['c'];
$totalFiltered = $result->num_rows;

/* ============================================================
   NOW INCLUDE HEADER + VIEW + FOOTER
   ============================================================ */
include 'header.php';
include 'teacherviewf.php';
include 'footer.php';