<?php
/* ============================================================
   SAVE NEW COURSE — INSERT HANDLER
   Handles POST from mycourseadmin.php form
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
   ONLY ACCEPT POST
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: mycourseadmin.php");
    exit();
}

/* ============================================================
   READ FORM DATA
   ============================================================ */
$course_code       = trim($_POST['course_code']       ?? '');
$course_name       = trim($_POST['course_name']       ?? '');
$credit_hour       = (int)($_POST['credit_hour']      ?? 0);
$course_type       = trim($_POST['course_type']       ?? 'Theory');
$college           = trim($_POST['college']           ?? '');
$department        = trim($_POST['department']        ?? '');
$course_year       = (int)($_POST['course_year']      ?? 0);
$course_semester   = (int)($_POST['course_semester']  ?? 0);
$category          = trim($_POST['category']          ?? '');
$prerequisite_code = trim($_POST['prerequisite_code'] ?? '');
if ($prerequisite_code === '') $prerequisite_code = null;

/* ============================================================
   VALIDATION
   ============================================================ */
$errors = [];

if ($course_code === '')    $errors[] = "Course Code is required";
if ($course_name === '')    $errors[] = "Course Name is required";
if ($credit_hour <= 0)      $errors[] = "Credit Hours is required";
if ($college === '')        $errors[] = "College is required";
if ($department === '')     $errors[] = "Department is required";
if ($course_year === 0)     $errors[] = "Curriculum Year is required";
if ($course_semester === 0) $errors[] = "Semester is required";
if ($category === '')       $errors[] = "Category is required";

if (!empty($errors)) {
    $msg = urlencode(implode(", ", $errors));
    header("Location: mycourseadmin.php?error=$msg");
    exit();
}

/* ============================================================
   DUPLICATE CHECK
   ============================================================ */
$dup = $conn->prepare("SELECT course_id FROM addcourse WHERE course_code = ?");
$dup->bind_param("s", $course_code);
$dup->execute();
$dup->store_result();

if ($dup->num_rows > 0) {
    $dup->close();
    header("Location: mycourseadmin.php?error=" . urlencode("Course code '$course_code' already exists."));
    exit();
}
$dup->close();

/* ============================================================
   INSERT
   ============================================================ */
$insert = $conn->prepare("
    INSERT INTO addcourse 
        (course_code, course_name, credit_hour, 
         category, college, department, 
         course_year, course_semester, course_type, prerequisite_code)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

/* Type string: s s i s s s i i s s = "ssisssiiss" */
$insert->bind_param("ssisssiiss",
    $course_code,
    $course_name,
    $credit_hour,
    $category,
    $college,
    $department,
    $course_year,
    $course_semester,
    $course_type,
    $prerequisite_code
);

if ($insert->execute()) {
    $insert->close();
    header("Location: mycourseadmin.php?success=1");
    exit();
} else {
    $err = $insert->error;
    $insert->close();
    header("Location: mycourseadmin.php?error=" . urlencode($err));
    exit();
}