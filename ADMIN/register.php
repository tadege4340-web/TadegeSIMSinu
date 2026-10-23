<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


// ... rest of the code
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confrim_password'] ?? '';

    // Validate
    if ($full_name === '' || $username === '' || $email === '' || $password === '') {
        die("Please fill in all fields.");
    }
    if ($password !== $confirm) {
        die("Passwords do not match.");
    }

    // Duplicate checks
    $chk = mysqli_prepare($conn, "SELECT id FROM users WHERE username=? OR email=? LIMIT 1");
    mysqli_stmt_bind_param($chk, "ss", $username, $email);
    mysqli_stmt_execute($chk);
    mysqli_stmt_store_result($chk);
    if (mysqli_stmt_num_rows($chk) > 0) {
        die("Username or Email already exists.");
    }
    mysqli_stmt_close($chk);

    // Build a student_id (e.g. INU/<random>)
    $student_id = 'INU/' . strtoupper(substr(md5($username . time()), 0, 6));

    // Split full_name into first + last
    $parts = explode(' ', $full_name, 2);
    $firstname = $parts[0];
    $lastname  = $parts[1] ?? '';

    mysqli_begin_transaction($conn);
    try {
        // 1. Insert into users2
        $s1 = mysqli_prepare($conn,
          "INSERT INTO users2 (student_id, firstname, lastname, age, gender, email, phone, department, year, role)
           VALUES (?, ?, ?, 0, '', ?, '', '', 0, 'user')");
        mysqli_stmt_bind_param($s1, "ssss", $student_id, $firstname, $lastname, $email);
        if (!mysqli_stmt_execute($s1)) throw new Exception("users2: " . mysqli_stmt_error($s1));
        mysqli_stmt_close($s1);

        // 2. Insert into users (link student_id!)
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';
        $s2 = mysqli_prepare($conn,
          "INSERT INTO users (username, full_name, email, password, role, student_id)
           VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($s2, "ssssss", $username, $full_name, $email, $hash, $role, $student_id);
        if (!mysqli_stmt_execute($s2)) throw new Exception("users: " . mysqli_stmt_error($s2));
        mysqli_stmt_close($s2);

        mysqli_commit($conn);

        header("Location: loginf.php?success=registered");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        die("Registration failed: " . $e->getMessage());
    }
}
?>