<?php
// FIX: Allow session cookies to work inside iframes (required for Render)
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,      // Must be true for SameSite=None (Render uses HTTPS)
    'httponly' => true,
    'samesite' => 'None'   // Critical: allows the cookie to be sent inside the iframe
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php");
    exit();
}

$hod_department = trim($_SESSION['department'] ?? '');
$hod_college    = trim($_SESSION['college'] ?? '');

// 2. Get filters from the view form
$search_query    = trim($_GET['search'] ?? '');
$filter_year     = (int)($_GET['year'] ?? 0);
$filter_semester = (int)($_GET['semester'] ?? 0);

// 3. Build the query (PDO)
$sql = "SELECT student_id, firstname, father_name, gender, `year`, current_semester 
        FROM users2 
        WHERE 1=1";
        
$params = [];

// Filter by department if the user is a Head
if ($_SESSION['role'] !== 'admin' && $hod_department !== '') {
    $sql .= " AND department = :dept";
    $params['dept'] = $hod_department;
}

// Filter by search (ID or Name)
if ($search_query !== '') {
    $sql .= " AND (student_id LIKE :search OR firstname LIKE :search OR father_name LIKE :search)";
    $params['search'] = '%' . $search_query . '%';
}

// Filter by Year
if ($filter_year > 0) {
    $sql .= " AND `year` = :year";
    $params['year'] = $filter_year;
}

// Filter by Semester
if ($filter_semester > 0) {
    $sql .= " AND current_semester = :sem";
    $params['sem'] = $filter_semester;
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

// 4. Load the View
include 'Headviewstudentf.php';
?>