<?php
/* ============================================================
   INJIBARA UNIVERSITY — STUDENT INFORMATION MANAGEMENT SYSTEM
   Login Handler (Converted to PDO for Aiven Cloud)
   ============================================================ */

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'db.php';

/* 1. ONLY ACCEPT POST REQUESTS */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: loginf.php");
    exit();
}

/* 2. GET + VALIDATE INPUT */
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header("Location: loginf.php?error=emptyfields");
    exit();
}

/* 3. LOOK UP THE USER BY USERNAME (PDO) */
$sql = "SELECT id, username, full_name, email, password, role,
               student_id, must_change_password
        FROM users
        WHERE username = :username
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: loginf.php?error=nousername");
    exit();
}

/* 4. VERIFY PASSWORD (Handles BOTH Hashed and Plain Text) */
$is_valid = false;

if (strpos($user['password'], '$2y$') === 0) {
    if (password_verify($password, $user['password'])) {
        $is_valid = true;
    }
} else {
    if ($password === $user['password']) {
        $is_valid = true;
        // Update to hash if it was plain text
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        $upd = $conn->prepare("UPDATE users SET password = :new_hash WHERE id = :id");
        $upd->execute(['new_hash' => $new_hash, 'id' => $user['id']]);
    }
}

if (!$is_valid) {
    header("Location: loginf.php?error=wrongpass");
    exit();
}

/* 5. PREVENT SESSION FIXATION */
session_regenerate_id(true);

/* 6. STORE BASE SESSION DATA */
$_SESSION['user_id']    = (int)$user['id'];
$_SESSION['username']   = $user['username'];
$_SESSION['full_name']  = $user['full_name'];
$_SESSION['email']      = $user['email'] ?? null;
$_SESSION['role']       = $user['role'];
$_SESSION['user_role']  = $user['role']; 
$_SESSION['student_id'] = $user['student_id'] ?? null;

/* 7. FETCH FIRST NAME + FATHER NAME FROM users2 (for students) */
$_SESSION['firstname']   = '';
$_SESSION['father_name'] = '';

if (!empty($user['student_id'])) {
    $name_stmt = $conn->prepare(
        "SELECT firstname, father_name
         FROM users2
         WHERE student_id = :student_id
         LIMIT 1");
    $name_stmt->execute(['student_id' => $user['student_id']]);
    $name_row = $name_stmt->fetch(PDO::FETCH_ASSOC);

    if ($name_row) {
        $_SESSION['firstname']   = $name_row['firstname']   ?? '';
        $_SESSION['father_name'] = $name_row['father_name'] ?? '';
    }
}

if ($_SESSION['firstname'] === '') {
    $_SESSION['firstname'] = $user['full_name'];
}

/* 8. FORCE PASSWORD CHANGE ON FIRST LOGIN */
if ((int)$user['must_change_password'] === 1) {
    header("Location: change_password.php");
    exit();
}

/* 9. ROUTE BY ROLE */

// ---------- ADMIN ----------
if ($user['role'] === 'admin') {
    $_SESSION['student_id'] = null;
    header("Location: homeh.php");
    exit();
}

// ---------- STUDENT / USER ----------
if ($user['role'] === 'user') {
    if (empty($user['student_id'])) {
        session_unset();
        session_destroy();
        header("Location: loginf.php?error=studentnotlinked");
        exit();
    }
    header("Location: user.php");
    exit();
}

// ---------- TEACHER ----------
if ($user['role'] === 'teacher') {
    $_SESSION['student_id'] = null; 
    
    // Safety Check: Fetch BOTH college and department (PDO)
    $t_check = $conn->prepare("SELECT id, department, college FROM teachers WHERE user_id = :user_id LIMIT 1");
    $t_check->execute(['user_id' => $user['id']]);
    $t_row = $t_check->fetch(PDO::FETCH_ASSOC);
    
    if (!$t_row) {
        session_unset();
        session_destroy();
        header("Location: loginf.php?error=teachernoprofile"); 
        exit();
    }
    
    $_SESSION['department'] = trim($t_row['department']);
    $_SESSION['college']    = trim($t_row['college']); 
    
    header("Location: Teacherdashboard.php");
    exit();
}

// ---------- HEAD OF DEPARTMENT ----------
if ($user['role'] === 'head') {
    $_SESSION['student_id'] = null; 
    
    // Safety Check & Fetch Department & College (PDO)
    $h_check = $conn->prepare("SELECT id, department, college FROM teachers WHERE user_id = :user_id LIMIT 1");
    $h_check->execute(['user_id' => $user['id']]);
    $h_row = $h_check->fetch(PDO::FETCH_ASSOC);
    
    if (!$h_row) {
        session_unset();
        session_destroy();
        header("Location: loginf.php?error=teachernoprofile"); 
        exit();
    }
    
    $_SESSION['department'] = trim($h_row['department']);
    $_SESSION['college']    = trim($h_row['college']);
    
    header("Location: Headheader.php");
    exit();
}

// ---------- UNKNOWN ROLE ----------
session_unset();
session_destroy();
header("Location: loginf.php?error=invalidrole");
exit();
?>