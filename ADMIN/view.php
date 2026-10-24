<?php
session_start();
require 'db.php';

// 1. Admin Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginf.php");
    exit();
}

// 2. Get Filters
$f_college    = trim($_GET['college'] ?? '');
$f_department = trim($_GET['department'] ?? '');
$f_year       = (int)($_GET['year'] ?? 0);
$f_semester   = (int)($_GET['semester'] ?? 0);
$f_search     = trim($_GET['search'] ?? '');

// 3. Load Colleges (for filter dropdown)
$colleges = [];
try {
    $stmt = $conn->query("SELECT code, name FROM college ORDER BY name");
    $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

// 4. Load Departments (for selected college)
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

// 5. Build Student Query (with filters)
$sql = "SELECT * FROM users2 WHERE 1=1";
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
    $sql .= " AND (student_id LIKE :search1 OR firstname LIKE :search2 OR father_name LIKE :search3)";
    $params['search1'] = "%$f_search%";
    $params['search2'] = "%$f_search%";
    $params['search3'] = "%$f_search%";
}

$sql .= " ORDER BY id DESC";

$students = [];
try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

// 6. Counts for the summary bar
$totalFiltered = count($students);
$totalAll = $conn->query("SELECT COUNT(*) FROM users2")->fetchColumn();

// 7. Load the advanced view
include 'header.php';
include 'viewf.php';
include 'footer.php';
?>