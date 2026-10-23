<?php
/* ============================================================
   CHANGE PASSWORD
   Two valid entry paths:
     A) Forced first-login   → $_SESSION['force_password_change'] = true
     B) Reset-link (token)   → ?token=... matching users.reset_token
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

$msg      = '';
$msg_type = 'error';

/* ------------------------------------------------------------
   DETECT ENTRY PATH
   ------------------------------------------------------------ */
$isForcedFirstLogin = !empty($_SESSION['force_password_change']);
$token              = trim($_GET['token'] ?? $_POST['token'] ?? '');

$valid_token = false;
$user_id     = null;
$username    = '';
$mode        = 'none';

/* ---------- PATH A: forced first-login ---------- */
if ($isForcedFirstLogin && !empty($_SESSION['user_id'])) {
    $user_id = (int)$_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $valid_token = true;
        $mode        = 'forced';
        $username    = $row['username'];
    } else {
        session_unset();
        session_destroy();
        header("Location: loginf.php?error=usernotfound");
        exit();
    }
}

/* ---------- PATH B: reset-token link ---------- */
elseif ($token !== '') {
    $stmt = $conn->prepare("
        SELECT id, username 
        FROM users 
        WHERE reset_token = ? 
          AND reset_expires > NOW() 
        LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $valid_token = true;
        $mode        = 'reset';
        $user_id     = $row['id'];
        $username    = $row['username'];
    } else {
        $msg = "❌ Invalid or expired reset link. Please request a new one.";
    }
}

/* ---------- PATH C: nothing valid ---------- */
else {
    $msg = "❌ No valid reset token. Please go to Reset Password.";
}

/* ------------------------------------------------------------
   HANDLE PASSWORD CHANGE (POST)
   ------------------------------------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {

    $new     = $_POST['new_password']     ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (strlen($new) < 7) {
        $msg = "Password must be at least 7 characters.";
    } elseif ($new !== $confirm) {
        $msg = "Passwords do not match.";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            UPDATE users 
            SET password             = ?, 
                must_change_password = 0,
                reset_token          = NULL,
                reset_expires        = NULL
            WHERE id = ?");
        $stmt->bind_param("si", $hash, $user_id);

        if ($stmt->execute()) {
            $stmt->close();

            /* Forced path → send to dashboard as logged-in user */
            if ($mode === 'forced') {
                unset($_SESSION['force_password_change']);

                $role = $_SESSION['role'] ?? 'user';
                if ($role === 'user') {
                    header("Location: user.php?password_changed=1");
                } elseif ($role === 'admin') {
                    header("Location: homeh.php?password_changed=1");
                } else {
                    header("Location: loginf.php?msg=passwordchanged");
                }
                exit();
            }

            /* Reset path → send to login */
            header("Location: loginf.php?msg=passwordreset");
            exit();
        } else {
            $msg = "❌ Database error: " . htmlspecialchars($stmt->error);
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Injibara University SIMS</title>
    <link rel="stylesheet" href="logine.css">
</head>
<body>

    <!-- ============ HEADER ============ -->
    <div class="header">
        <div class="logo">
            <img src="logoinu.jpg" alt="Injibara University Logo">
        </div>
        <div class="title">
            <h1>INJIBARA UNIVERSITY<br>STUDENT INFORMATION MANAGEMENT SYSTEM</h1>
        </div>
    </div>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: #eef2f7; 
            margin: 0; 
            padding: 40px 20px;
        }
        .box { 
            max-width: 420px; 
            margin: 60px auto; 
            padding: 34px 36px; 
            background: #ffffff; 
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border-top: 5px solid #1e3a5f;
        }
        h2 { 
            text-align: center; 
            color: #1e3a5f; 
            margin: 0 0 20px;
            font-size: 22px;
            padding-bottom: 14px;
            border-bottom: 2px solid #e5e7eb;
        }
        label {
            display: block;
            font-weight: 700;
            color: #334155;
            font-size: 13.5px;
            margin-top: 14px;
            margin-bottom: 6px;
        }
        input[type="password"] { 
            width: 100%; 
            padding: 11px 14px; 
            margin: 0; 
            border: 1.5px solid #cbd5e1; 
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
            font-family: inherit;
            background: #f9fafb;
            outline: none;
            transition: all 0.2s;
        }
        input[type="password"]:focus {
            border-color: #0066cc;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(0,102,204,0.12);
        }
        input[type="submit"] { 
            width: 100%;
            margin-top: 22px;
            background: linear-gradient(135deg, #1e3a5f 0%, #0066cc 100%);
            color: #ffffff;
            border: none; 
            cursor: pointer;
            font-weight: 800; 
            padding: 14px;
            border-radius: 8px;
            font-size: 15px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            transition: all 0.25s;
            box-shadow: 0 6px 18px rgba(30,58,95,0.25);
        }
        input[type="submit"]:hover { 
            background: linear-gradient(135deg, #0066cc 0%, #004b99 100%);
            transform: translateY(-2px);
        }
        .err { 
            background: #fee2e2; 
            color: #991b1b;
            padding: 12px 16px; 
            border-radius: 8px;
            border-left: 4px solid #ef4444;
            margin-bottom: 16px; 
            text-align: center;
            font-weight: 600;
            font-size: 13.5px;
        }
        .err a { color: #0284c7; text-decoration: underline; }
        .info {
            text-align: center;
            color: #64748b;
            font-size: 13.5px;
            margin: 0 0 10px;
        }
        .badge {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            font-size: 11px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 20px;
            letter-spacing: 1px;
            margin-bottom: 14px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
<div class="box">

    <?php if ($mode === 'forced'): ?>
        <div style="text-align:center;">
            <span class="badge">🔐 First Login</span>
        </div>
        <h2>🔒 Change Your Password</h2>
        <p class="info">
            For security, you must set a new password before continuing.
        </p>
    <?php else: ?>
        <h2>🔒 Change Your Password</h2>
    <?php endif; ?>

    <?php if ($msg): ?>
        <div class="err"><?= $msg ?></div>
    <?php endif; ?>

    <?php if (!$valid_token && $msg === ''): ?>
        <div class="err">
            ❌ No valid reset token. Please go to
            <a href="password_reset.php">Reset Password</a>.
        </div>
    <?php endif; ?>

    <?php if ($valid_token): ?>
        <p class="info">
            Set a new password for <strong><?= htmlspecialchars($username) ?></strong>
            (minimum 7 characters).
        </p>

        <form method="POST" autocomplete="off">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <label>New Password:</label>
            <input type="password" name="new_password" required minlength="7" autofocus>

            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required minlength="7">

            <input type="submit" value="💾 SAVE NEW PASSWORD">
        </form>
    <?php endif; ?>

</div>
</body>
</html>