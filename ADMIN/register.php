<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    
    // Note: Make sure this matches the 'name' attribute in your HTML form!
    $confirm   = $_POST['confirm_password'] ?? $_POST['confrim_password'] ?? ''; 

    // Validate
    if ($full_name === '' || $username === '' || $email === '' || $password === '') {
        die("Please fill in all fields.");
    }
    if ($password !== $confirm) {
        die("Passwords do not match.");
    }

    try {
        // 1. Duplicate checks (PDO)
        $chk = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1");
        $chk->execute(['username' => $username, 'email' => $email]);
        
        if ($chk->fetch()) {
            die("Username or Email already exists.");
        }

        // 2. Build a student_id (e.g. INU/<random>)
        $student_id = 'INU/' . strtoupper(substr(md5($username . time()), 0, 6));

        // 3. Split full_name into first + last
        $parts = explode(' ', $full_name, 2);
        $firstname = $parts[0];
        $lastname  = $parts[1] ?? '';

        // 4. Begin Transaction
        $conn->beginTransaction();

        // Insert into users2
        $s1 = $conn->prepare(
            "INSERT INTO users2 (student_id, firstname, lastname, age, gender, email, phone, department, year, role)
             VALUES (:student_id, :firstname, :lastname, 0, '', :email, '', '', 0, 'user')"
        );
        $s1->execute([
            'student_id' => $student_id,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email
        ]);

        // Insert into users (link student_id!)
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';
        
        $s2 = $conn->prepare(
            "INSERT INTO users (username, full_name, email, password, role, student_id)
             VALUES (:username, :full_name, :email, :password, :role, :student_id)"
        );
        $s2->execute([
            'username' => $username,
            'full_name' => $full_name,
            'email' => $email,
            'password' => $hash,
            'role' => $role,
            'student_id' => $student_id
        ]);

        // Commit Transaction
        $conn->commit();

        header("Location: loginf.php?success=registered");
        exit();

    } catch (PDOException $e) {
        // Rollback if anything fails
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        die("Registration failed: " . $e->getMessage());
    }
}
?>