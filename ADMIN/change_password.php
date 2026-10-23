<?php
session_start();
require 'db.php';

$message      = "";
$message_type = "";
$valid_user   = false;
$user_id      = null;
$username     = "";

/* ============================================================
   STEP 1: CHECK IF USER IS LOGGED IN OR HAS A TOKEN
   ============================================================ */
if (isset($_GET['token']) && $_GET['token'] !== '') {
    // Case A: Reset link from email
    $token = $_GET['token'];
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE reset_token = ? AND reset_expires > NOW() LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $valid_user = true;
        $user_id    = $user['id'];
        $username   = $user['username'];
    } else {
        $message      = "❌ Invalid or expired reset link. Please request a new one.";
        $message_type = "error";
    }
    $stmt->close();
} 
elseif (isset($_SESSION['user_id'])) {
    // Case B: Logged in user forced to change password
    $user_id    = $_SESSION['user_id'];
    $username   = $_SESSION['username'];
    $valid_user = true;
} 
else {
    // Case C: No session, no token
    $message      = "❌ No valid reset token or session. Please log in.";
    $message_type = "error";
}

/* ============================================================
   STEP 2: HANDLE PASSWORD UPDATE
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_user) {
    $new_pass     = $_POST['new_password']     ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    // CHANGED: Now requires at least 7 characters
    if (strlen($new_pass) < 7) {
        $message      = "❌ Password must be at least 7 characters.";
        $message_type = "error";
    } elseif ($new_pass !== $confirm_pass) {
        $message      = "❌ Passwords do not match.";
        $message_type = "error";
    } else {
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $upd = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL, must_change_password = 0 WHERE id = ?");
        $upd->bind_param("si", $hash, $user_id);
        $upd->execute();
        $upd->close();

        // Log them out so they can log in with the new password
        session_unset();
        session_destroy();
        header("Location: loginf.php?msg=passwordreset");
        exit();
    }
}

// Load the HTML View
include 'change_passwordf.php';
?>