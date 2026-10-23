<?php
/* ============================================================
   PASSWORD RESET — LOGIC
   Handles POST, generates token, then loads the view
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

/* ============ DEFAULT STATE ============ */
$message      = "";
$message_type = "";
$reset_link   = "";

/* ============ HANDLE POST ============ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');

    if ($username === '') {
        $message      = "Please enter your username.";
        $message_type = "error";
    } else {

        /* ---------- Check if user exists ---------- */
        $stmt = $conn->prepare("SELECT id, username, full_name, email 
                                FROM users 
                                WHERE username = ? 
                                LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {

            /* ---------- Generate reset token ---------- */
            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            /* ---------- Save token to DB ---------- */
            $upd = $conn->prepare("UPDATE users 
                                   SET reset_token = ?, reset_expires = ? 
                                   WHERE id = ?");
            $upd->bind_param("ssi", $token, $expires, $user['id']);
            $upd->execute();
            $upd->close();

            /* ---------- Build reset link ---------- */
            $base = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
            $reset_link = $base . "/change_password.php?token=" . $token;

            $message = "✅ A reset link has been generated for <strong>"
                     . htmlspecialchars($user['username']) . "</strong>.";

            $message_type = "success";

        } else {
            $message      = "❌ No account found with username: "
                          . htmlspecialchars($username);
            $message_type = "error";
        }
        $stmt->close();
    }
}

/* ============ LOAD THE VIEW (matching your filename) ============ */
include 'password_resetf.php';