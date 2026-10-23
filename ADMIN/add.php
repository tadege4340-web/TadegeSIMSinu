<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'db.php';

// Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginf.php");
    exit();
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: addf.php");
    exit();
}

/* ============================================================
   COLLECT FORM DATA
   ============================================================ */

// Required fields
$student_id       = trim($_POST['studentid']        ?? '');
$firstname        = trim($_POST['firstname']        ?? '');
$father_name      = trim($_POST['father_name']      ?? '');
$grandfather_name = trim($_POST['grandfather_name'] ?? '');
$region           = trim($_POST['region']           ?? '');
$secondary_school = trim($_POST['secondary_school'] ?? '');
$college          = trim($_POST['college']          ?? '');
$department       = trim($_POST['department']       ?? '');

// Optional fields
$age        = (int)($_POST['age'] ?? 0) ?: null;
$gender     = trim($_POST['gender']     ?? '') ?: null;
$phone      = trim($_POST['phone']      ?? '') ?: null;
$year       = (int)($_POST['year']      ?? 0) ?: null;
$current_semester = (int)($_POST['current_semester'] ?? 1); // 🔥 NEW SEMESTER

/* ============================================================
   VALIDATE REQUIRED FIELDS
   ============================================================ */
if ($student_id === '' || $firstname === '' || $father_name === ''
    || $grandfather_name === '' || $region === '' || $secondary_school === ''
    || $college === '' || $department === '') {

    header("Location: addf.php?error=" . urlencode("Please fill all required fields, including College and Department."));
    exit();
}

/* ============================================================
   DUPLICATE student_id CHECK
   ============================================================ */
$chk = mysqli_prepare($conn, "SELECT student_id FROM users2 WHERE student_id = ? LIMIT 1");
mysqli_stmt_bind_param($chk, "s", $student_id);
mysqli_stmt_execute($chk);
mysqli_stmt_store_result($chk);

if (mysqli_stmt_num_rows($chk) > 0) {
    mysqli_stmt_close($chk);
    header("Location: addf.php?error=" . urlencode("Student ID already exists."));
    exit();
}
mysqli_stmt_close($chk);

/* ============================================================
   GENERATE TEMPORARY PASSWORD
   ============================================================ */
$clean_gp      = strtolower(preg_replace('/[^a-zA-Z]/', '', $grandfather_name));
$temp_password = $clean_gp . '1234abcd#';
$hash          = password_hash($temp_password, PASSWORD_DEFAULT);
$full_name     = trim($firstname . ' ' . $father_name . ' ' . $grandfather_name);

/* ============================================================
   INSERT (transaction)
   ============================================================ */
mysqli_begin_transaction($conn);
try {

    /* ---------- 1. users2 (main student record) ---------- */
    // Added current_semester to the query
    $s1 = mysqli_prepare($conn,
        "INSERT INTO users2
            (student_id, firstname, father_name, grandfather_name,
             age, gender, phone, region, secondary_school,
             college, department, `year`, current_semester, role)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'user')");

    // Updated bind_param string: added 'i' for integer semester
    mysqli_stmt_bind_param($s1, "ssssissssssii",
        $student_id, $firstname, $father_name, $grandfather_name,
        $age, $gender, $phone, $region, $secondary_school,
        $college, $department, $year, $current_semester);

    if (!mysqli_stmt_execute($s1)) {
        throw new Exception("users2: " . mysqli_stmt_error($s1));
    }
    mysqli_stmt_close($s1);

    /* ---------- 2. users (login record) ---------- */
    $s2 = mysqli_prepare($conn,
        "INSERT INTO users
            (username, full_name, password, role, student_id, must_change_password)
         VALUES (?, ?, ?, 'user', ?, 1)");

    mysqli_stmt_bind_param($s2, "ssss",
        $student_id, $full_name, $hash, $student_id);

    if (!mysqli_stmt_execute($s2)) {
        throw new Exception("users: " . mysqli_stmt_error($s2));
    }
    mysqli_stmt_close($s2);

    mysqli_commit($conn);

    // Success → back to addf.php
    header("Location: addf.php?success=added");
    exit();

} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: addf.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>