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
$student_id       = trim($_POST['studentid']        ?? '');
$firstname        = trim($_POST['firstname']        ?? '');
$father_name      = trim($_POST['father_name']      ?? '');
$grandfather_name = trim($_POST['grandfather_name'] ?? '');
$region           = trim($_POST['region']           ?? '');
$secondary_school = trim($_POST['secondary_school'] ?? '');
$college          = trim($_POST['college']          ?? '');
$department       = trim($_POST['department']       ?? '');

$age        = (int)($_POST['age'] ?? 0) ?: null;
$gender     = trim($_POST['gender']     ?? '') ?: null;
$phone      = trim($_POST['phone']      ?? '') ?: null;
$year       = (int)($_POST['year']      ?? 0) ?: null;
$current_semester = (int)($_POST['current_semester'] ?? 1);

/* ============================================================
   VALIDATE REQUIRED FIELDS
   ============================================================ */
if ($student_id === '' || $firstname === '' || $father_name === ''
    || $grandfather_name === '' || $region === '' || $secondary_school === ''
    || $college === '' || $department === '') {
    header("Location: addf.php?error=" . urlencode("Please fill all required fields."));
    exit();
}

/* ============================================================
   DUPLICATE student_id CHECK (PDO)
   ============================================================ */
$chk = $conn->prepare("SELECT student_id FROM users2 WHERE student_id = :sid LIMIT 1");
$chk->execute(['sid' => $student_id]);
if ($chk->fetch()) {
    header("Location: addf.php?error=" . urlencode("Student ID already exists."));
    exit();
}

/* ============================================================
   GENERATE TEMPORARY PASSWORD
   ============================================================ */
$clean_gp      = strtolower(preg_replace('/[^a-zA-Z]/', '', $grandfather_name));
$temp_password = $clean_gp . '1234abcd#';
$hash          = password_hash($temp_password, PASSWORD_DEFAULT);
$full_name     = trim($firstname . ' ' . $father_name . ' ' . $grandfather_name);

/* ============================================================
   INSERT (transaction PDO)
   ============================================================ */
try {
    $conn->beginTransaction();

    /* ---------- 1. users2 (main student record) ---------- */
    $sql1 = "INSERT INTO users2
                (student_id, firstname, father_name, grandfather_name,
                 age, gender, phone, region, secondary_school,
                 college, department, `year`, current_semester, role)
             VALUES (:sid, :fname, :father, :gf, :age, :gender, :phone, :region, :school, :college, :dept, :year, :sem, 'user')";
    
    $s1 = $conn->prepare($sql1);
    $s1->execute([
        'sid' => $student_id, 'fname' => $firstname, 'father' => $father_name,
        'gf' => $grandfather_name, 'age' => $age, 'gender' => $gender,
        'phone' => $phone, 'region' => $region, 'school' => $secondary_school,
        'college' => $college, 'dept' => $department, 'year' => $year, 'sem' => $current_semester
    ]);

    /* ---------- 2. users (login record) ---------- */
    $sql2 = "INSERT INTO users
                (username, full_name, password, role, student_id, must_change_password)
             VALUES (:sid, :fname, :pass, 'user', :sid, 1)";
    
    $s2 = $conn->prepare($sql2);
    $s2->execute([
        'sid' => $student_id, 'fname' => $full_name, 'pass' => $hash
    ]);

    $conn->commit();

    // Success → back to addf.php
    header("Location: addf.php?success=added");
    exit();

} catch (Exception $e) {
    $conn->rollBack();
    header("Location: addf.php?error=" . urlencode("Database Error: " . $e->getMessage()));
    exit();
}
?>