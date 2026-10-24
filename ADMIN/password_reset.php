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

        try {
            /* ---------- Check if user exists (PDO) ---------- */
            $stmt = $conn->prepare("SELECT id, username, full_name, email 
                                    FROM users 
                                    WHERE username = :username 
                                    LIMIT 1");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {

                /* ---------- Generate reset token ---------- */
                $token   = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                /* ---------- Save token to DB (PDO) ---------- */
                $upd = $conn->prepare("UPDATE users 
                                       SET reset_token = :token, reset_expires = :expires 
                                       WHERE id = :id");
                $upd->execute([
                    'token' => $token,
                    'expires' => $expires,
                    'id' => $user['id']
                ]);

                /* ---------- Build reset link ---------- */
                // Get the base URL dynamically (works on localhost AND Render)
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                $host = $_SERVER['HTTP_HOST'];
                $path = dirname($_SERVER['PHP_SELF']);
                $base = $protocol . "://" . $host . ($path == '/' ? '' : $path);
                
                $reset_link = $base . "/change_password.php?token=" . $token;

                $message = "✅ A reset link has been generated for <strong>"
                         . htmlspecialchars($user['username']) . "</strong>.";

                $message_type = "success";

            } else {
                $message      = "❌ No account found with username: "
                              . htmlspecialchars($username);
                $message_type = "error";
            }
        } catch (PDOException $e) {
            $message      = "❌ Database Error: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

/* ============ LOAD THE VIEW ============ */
include 'password_resetf.php';
?>