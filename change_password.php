<?php
/* ============================================================
   CHANGE PASSWORD — After clicking reset link
   ============================================================ */

session_start();
require 'db.php';

$token        = $_GET['token'] ?? '';
$message      = "";
$message_type = "";
$valid_token  = false;
$user_id      = null;
$username     = "";

/* ============ STEP 1: VALIDATE TOKEN ============ */
if ($token !== '') {
    $stmt = $conn->prepare("SELECT id, username 
                            FROM users 
                            WHERE reset_token = ? 
                              AND reset_expires > NOW() 
                            LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $valid_token = true;
        $user_id     = $user['id'];
        $username    = $user['username'];
    } else {
        $message      = "❌ Invalid or expired reset link. Please request a new one.";
        $message_type = "error";
    }
    $stmt->close();
}

/* ============ STEP 2: HANDLE NEW PASSWORD ============ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {

    $new_pass     = $_POST['new_password']     ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if (strlen($new_pass) < 6) {
        $message      = "❌ Password must be at least 6 characters.";
        $message_type = "error";
    } elseif ($new_pass !== $confirm_pass) {
        $message      = "❌ Passwords do not match.";
        $message_type = "error";
    } else {
        /* -------- Hash and update -------- */
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);

        $upd = $conn->prepare("UPDATE users 
                               SET password = ?, 
                                   reset_token = NULL, 
                                   reset_expires = NULL,
                                   must_change_password = 0
                               WHERE id = ?");
        $upd->bind_param("si", $hash, $user_id);
        $upd->execute();
        $upd->close();

        /* -------- Redirect to login with success -------- */
        header("Location: loginf.php?msg=passwordreset");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password — Injibara University</title>
    <link rel="stylesheet" href="password_reset.css">
</head>
<body>

    <div class="page-wrapper">

        <h1 class="page-title">Online Student Information System &gt; Change Password</h1>

        <!-- ============ INVALID TOKEN ============ -->
        <?php if (!$valid_token && $message === ''): ?>
            <div class="alert alert-error">
                ❌ No reset token provided. Please start from the
                <a href="password_reset.php">Reset Password</a> page.
            </div>
        <?php endif; ?>

        <!-- ============ MESSAGES ============ -->
        <?php if ($message !== ''): ?>
            <div class="alert alert-<?= $message_type ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- ============ NEW PASSWORD FORM ============ -->
        <?php if ($valid_token): ?>

            <div class="info-box">
                Enter a new password for <strong><?= htmlspecialchars($username) ?></strong>.
                Choose a password you'll remember.
            </div>

            <form method="POST">
                <div class="form-row">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password"
                           required autofocus minlength="6">
                </div>

                <div class="form-row">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           required minlength="6">
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn btn-send">Change Password</button>
                    <button type="button" class="btn btn-cancel"
                            onclick="window.location.href='loginf.php'">Cancel</button>
                </div>
            </form>

        <?php endif; ?>

    </div>

</body>
</html>