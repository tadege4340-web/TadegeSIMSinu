<?php
session_start();
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header("Location: loginf.php");
    exit();
}

// 2. Get Teacher's Database ID and Department
$teacher_id = null;
$teacher_department = null;

if ($_SESSION['role'] === 'teacher') {
    try {
        $stmt = $conn->prepare("SELECT id, department FROM teachers WHERE user_id = :uid LIMIT 1");
        $stmt->execute(['uid' => $_SESSION['user_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $teacher_id = $row['id'];
            $teacher_department = trim($row['department']);
        }
    } catch (PDOException $e) {}
    
    if (empty($teacher_id)) {
        die("<h3 style='color:red; font-family:Arial; padding:20px;'>Error: Teacher ID not found. Please contact Admin.</h3>");
    }
}

// 3. Initialize Filters
$f_year       = (int)($_GET['year']      ?? 0);
$f_semester   = (int)($_GET['semester']  ?? 0);
$f_search     = trim($_GET['search']     ?? '');

// 4. Build Query
$sql = "SELECT DISTINCT u.id, u.student_id, u.firstname, u.father_name, u.grandfather_name,
               u.gender, u.phone, u.`year`, u.current_semester
        FROM users2 u
        JOIN student_courses sc ON u.student_id = sc.student_id
        JOIN teacher_courses tc ON sc.course_id = tc.course_id
        WHERE tc.teacher_id = :tid";

$params = ['tid' => $teacher_id];

// Filter by Year
if ($f_year > 0) {
    $sql .= " AND u.`year` = :year";
    $params['year'] = $f_year;
}

// Filter by Semester
if ($f_semester > 0) {
    $sql .= " AND u.current_semester = :sem";
    $params['sem'] = $f_semester;
}

// Search Filter
if ($f_search !== '') {
    $sql .= " AND (u.student_id LIKE :search1 OR u.firstname LIKE :search2 OR u.father_name LIKE :search3 OR u.grandfather_name LIKE :search4)";
    $like = '%' . $f_search . '%';
    $params['search1'] = $like;
    $params['search2'] = $like;
    $params['search3'] = $like;
    $params['search4'] = $like;
}

$sql .= " ORDER BY u.student_id ASC";

$students = [];
try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

// 5. Counts
$totalFiltered = count($students);

// Count total unique students assigned to this teacher
$totalAll = 0;
try {
    $stmt_count = $conn->prepare("
        SELECT COUNT(DISTINCT u.student_id) AS c 
        FROM users2 u 
        JOIN student_courses sc ON u.student_id = sc.student_id 
        JOIN teacher_courses tc ON sc.course_id = tc.course_id 
        WHERE tc.teacher_id = :tid
    ");
    $stmt_count->execute(['tid' => $teacher_id]);
    $totalAll = (int)$stmt_count->fetchColumn();
} catch (PDOException $e) {}

// 6. Include the View
include 'Teacherviewstudentf.php';
?>