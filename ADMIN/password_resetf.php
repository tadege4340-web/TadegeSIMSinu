<?php
session_start();
require 'db.php';

$message = "";
$message_type = "";
$reset_link = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');

    if ($username === '') {
        $message = "❌ Please enter your username.";
        $message_type = "error";
    } else {
        try {
            // 1. Check if the user exists (PDO)
            $stmt = $conn->prepare("SELECT id, email FROM users WHERE username = :username LIMIT 1");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // 2. Generate a secure token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // 3. Save the token in the database
                $update = $conn->prepare("UPDATE users SET reset_token = :token, reset_expires = :expires WHERE id = :id");
                $update->execute([
                    'token' => $token,
                    'expires' => $expires,
                    'id' => $user['id']
                ]);

                // 4. Create the reset link
                // Note: Change this to your real live URL
                $reset_link = "https://tadegesimsinu.onrender.com/reset_password_confirm.php?token=" . $token;

                $message = "✅ A password reset link has been generated for <b>" . htmlspecialchars($username) . "</b>.";
                $message_type = "success";

                // In a real app, you would send an email here using PHPMailer or mail()
                // mail($user['email'], "Password Reset", "Click here: $reset_link");

            } else {
                $message = "❌ No user found with that username.";
                $message_type = "error";
            }
        } catch (PDOException $e) {
            $message = "❌ Database Error: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

// Load the HTML form
include 'password_resetf.php';
?>