<?php
/* ============================================================
   INJIBARA UNIVERSITY — STUDENT INFORMATION MANAGEMENT SYSTEM
   Login Handler
   ============================================================ */

// --- Show errors during development (remove/comment in production) ---
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- Start session safely (only if not already started) ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'db.php';

/* ============================================================
   1. ONLY ACCEPT POST REQUESTS
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: loginf.php");
    exit();
}

/* ============================================================
   2. GET + VALIDATE INPUT
   ============================================================ */
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header("Location: loginf.php?error=emptyfields");
    exit();
}

/* ============================================================
   3. LOOK UP THE USER BY USERNAME
   ============================================================ */
$sql = "SELECT id, username, full_name, email, password, role,
               student_id, must_change_password
        FROM users
        WHERE username = ?
        LIMIT 1";

$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    error_log("Login prepare failed: " . mysqli_error($conn));
    header("Location: loginf.php?error=servererror");
    exit();
}

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) !== 1) {
    mysqli_stmt_close($stmt);
    header("Location: loginf.php?error=nousername");
    exit();
}

$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

/* ============================================================
   4. VERIFY PASSWORD
   ============================================================ */
if (!password_verify($password, $user['password'])) {
    header("Location: loginf.php?error=wrongpass");
    exit();
}

/* ============================================================
   5. PREVENT SESSION FIXATION
   ============================================================ */
session_regenerate_id(true);

/* ============================================================
   6. STORE BASE SESSION DATA
   ============================================================ */
$_SESSION['user_id']    = (int)$user['id'];
$_SESSION['username']   = $user['username'];
$_SESSION['full_name']  = $user['full_name'];
$_SESSION['email']      = $user['email'] ?? null;
$_SESSION['role']       = $user['role'];
$_SESSION['user_role']  = $user['role'];   // alias used across the project
$_SESSION['student_id'] = $user['student_id'] ?? null;

/* ============================================================
   7. FETCH FIRST NAME + FATHER NAME FROM users2 (for students)
   ============================================================ */
$_SESSION['firstname']   = '';
$_SESSION['father_name'] = '';

if (!empty($user['student_id'])) {
    $name_stmt = mysqli_prepare($conn,
        "SELECT firstname, father_name
         FROM users2
         WHERE student_id = ?
         LIMIT 1");
    mysqli_stmt_bind_param($name_stmt, "s", $user['student_id']);
    mysqli_stmt_execute($name_stmt);
    $name_result = mysqli_stmt_get_result($name_stmt);
    $name_row    = mysqli_fetch_assoc($name_result);
    mysqli_stmt_close($name_stmt);

    if ($name_row) {
        $_SESSION['firstname']   = $name_row['firstname']   ?? '';
        $_SESSION['father_name'] = $name_row['father_name'] ?? '';
    }
}

// Fallback for admin (or if the users2 lookup failed)
if ($_SESSION['firstname'] === '') {
    $_SESSION['firstname'] = $user['full_name'];
}

/* ============================================================
   8. FORCE PASSWORD CHANGE ON FIRST LOGIN
   ============================================================ */
if ((int)$user['must_change_password'] === 1) {
    $_SESSION['force_password_change'] = true;   // ✅ FIX: added this line
    header("Location: change_password.php");
    exit();
}

/* ============================================================
   9. ROUTE BY ROLE
   ============================================================ */

// ---------- ADMIN ----------
if ($user['role'] === 'admin') {
    $_SESSION['student_id'] = null;
    header("Location: homeh.php");
    exit();
}

// ---------- STUDENT / USER ----------
if ($user['role'] === 'user') {
    if (empty($user['student_id'])) {
        // Account exists but has no linked student profile
        session_unset();
        session_destroy();
        header("Location: loginf.php?error=studentnotlinked");
        exit();
    }
    header("Location: user.php");
    exit();
}

// ---------- TEACHER / HEAD / REGISTRAR ----------
if (in_array($user['role'], ['teacher', 'head', 'registrar'])) {
    header("Location: homeh.php");
    exit();
}

// ---------- UNKNOWN ROLE ----------
session_unset();
session_destroy();
header("Location: loginf.php?error=invalidrole");
exit();
?>