<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Please enter username and password.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id']  = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role']     = $row['role'];

                // Force password change if flagged
                if (isset($row['must_change_password']) && (int)$row['must_change_password'] === 1) {
                    header("Location: change_password.php");
                } else {
                    header("Location: homeh.php");
                }
                exit();
            } else {
                $error = "Wrong password.";
            }
        } else {
            $error = "Username not found.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="ADMIN/css22.css">
    <style>
        body { padding: 0; background: #eef2f7; }
        .login-box {
            max-width: 400px;
            margin: 120px auto;
            padding: 40px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-top: 5px solid #1abc9c;
        }
        .login-box h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }
        .login-box label {
            display: block;
            margin-top: 14px;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2c3e50;
        }
        .login-box input {
            width: 100%;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 15px;
            box-sizing: border-box;
        }
        .login-box input[type="submit"] {
            margin-top: 24px;
            background: #2c3e50;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            border: none;
            padding: 12px;
        }
        .login-box input[type="submit"]:hover {
            background: #1abc9c;
        }
        .err {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
            padding: 10px 14px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Admin Login</h2>

    <?php if ($error): ?>
        <div class="err"><?= htmlspecialchars($error) ?></p></div>
    <?php endif; ?>

    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <input type="submit" value="LOGIN">
    </form>
</div>

</body>
</html>