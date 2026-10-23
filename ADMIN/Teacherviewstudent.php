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
    $stmt = $conn->prepare("SELECT id, department FROM teachers WHERE user_id = ? LIMIT 1");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $teacher_id = $row['id'];
        $teacher_department = trim($row['department']);
    }
    $stmt->close();
    
    if (empty($teacher_id)) {
        die("<h3 style='color:red; font-family:Arial; padding:20px;'>Error: Teacher ID not found. Please contact Admin.</h3>");
    }
}

// 3. Initialize Filters
$f_year       = (int)($_GET['year']      ?? 0);
$f_semester   = (int)($_GET['semester']  ?? 0);
$f_search     = trim($_GET['search']     ?? '');

// 4. Build Query
// 🔥 THE FIX: We only select students who are in the `student_courses` table 
// AND the course is assigned to this specific teacher in `teacher_courses`.
$sql = "SELECT DISTINCT u.id, u.student_id, u.firstname, u.father_name, u.grandfather_name,
               u.gender, u.phone, u.`year`, u.current_semester
        FROM users2 u
        JOIN student_courses sc ON u.student_id = sc.student_id
        JOIN teacher_courses tc ON sc.course_id = tc.course_id
        WHERE tc.teacher_id = ?";

$params = [$teacher_id];
$types  = 'i';

// Filter by Year
if ($f_year > 0) {
    $sql .= " AND u.`year` = ?";
    $params[] = $f_year;
    $types .= 'i';
}

// Filter by Semester
if ($f_semester > 0) {
    $sql .= " AND u.current_semester = ?";
    $params[] = $f_semester;
    $types .= 'i';
}

// Search Filter
if ($f_search !== '') {
    $sql .= " AND (u.student_id LIKE ? OR u.firstname LIKE ? OR u.father_name LIKE ? OR u.grandfather_name LIKE ?)";
    $like = '%' . $f_search . '%';
    $params[] = $like; $params[] = $like;
    $params[] = $like; $params[] = $like;
    $types .= 'ssss';
}

$sql .= " ORDER BY u.student_id ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// 5. Counts
$totalFiltered = $result->num_rows;

// Count total unique students assigned to this teacher
$stmt_count = $conn->prepare("
    SELECT COUNT(DISTINCT u.student_id) AS c 
    FROM users2 u 
    JOIN student_courses sc ON u.student_id = sc.student_id 
    JOIN teacher_courses tc ON sc.course_id = tc.course_id 
    WHERE tc.teacher_id = ?
");
$stmt_count->bind_param("i", $teacher_id);
$stmt_count->execute();
$totalAll = $stmt_count->get_result()->fetch_assoc()['c'];
$stmt_count->close();

// 6. Include the View
include 'Teacherviewstudentf.php';
?>