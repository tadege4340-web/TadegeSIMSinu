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
   LOAD COLLEGES (PDO)
   ============================================================ */
$colleges = [];
try {
    $r = $conn->query("SELECT code, name FROM college ORDER BY name");
    if ($r) {
        $colleges = $r->fetchAll(PDO::FETCH_ASSOC);
    }
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
   LOAD DISTINCT POSITIONS (PDO)
   ============================================================ */
$positions = [];
try {
    $r = $conn->query("SELECT DISTINCT position FROM teachers WHERE position IS NOT NULL AND position <> '' ORDER BY position");
    if ($r) {
        while ($row = $r->fetch(PDO::FETCH_ASSOC)) {
            $positions[] = $row['position'];
        }
    }
} catch (PDOException $e) {}

/* ============================================================
   LOAD DISTINCT STATUSES (PDO)
   ============================================================ */
$statuses = [];
try {
    $r = $conn->query("SELECT DISTINCT status FROM teachers WHERE status IS NOT NULL AND status <> '' ORDER BY status");
    if ($r) {
        while ($row = $r->fetch(PDO::FETCH_ASSOC)) {
            $statuses[] = $row['status'];
        }
    }
} catch (PDOException $e) {}

/* ============================================================
   BUILD TEACHER QUERY (PDO)
   ============================================================ */
$sql = "SELECT id, full_name, age, gender, email, phone, address,
               college, department, qualification, position,
               hire_date, status
        FROM teachers
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
if ($f_position !== '') {
    $sql .= " AND position = :position";
    $params['position'] = $f_position;
}
if ($f_status !== '') {
    $sql .= " AND status = :status";
    $params['status'] = $f_status;
}
if ($f_search !== '') {
    $sql .= " AND (full_name LIKE :search 
                OR email     LIKE :search 
                OR phone     LIKE :search
                OR id        LIKE :search)";
    $params['search'] = '%' . $f_search . '%';
}

$sql .= " ORDER BY id ASC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $teachers = [];
    $db_error = $e->getMessage();
}

/* ============================================================
   COUNT TOTALS (PDO)
   ============================================================ */
try {
    $totalAll = $conn->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
} catch (PDOException $e) {
    $totalAll = 0;
}
$totalFiltered = count($teachers);

/* ============================================================
   NOW INCLUDE HEADER + VIEW + FOOTER
   ============================================================ */
include 'header.php';
include 'teacherviewf.php';
include 'footer.php';
?>