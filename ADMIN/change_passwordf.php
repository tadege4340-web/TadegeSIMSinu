<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password — Injibara University</title>
    <link rel="stylesheet" href="change_password.css">
</head>
<body>

    <!-- ============ HEADER ============ -->
    <header class="uni-header">
        <div class="header-logo">
            <img src="logoinu.jpg" alt="Injibara University Logo">
        </div>
        <div class="header-titles">
            <h1>INJIBARA UNIVERSITY</h1>
            <h2>STUDENT INFORMATION MANAGEMENT SYSTEM</h2>
        </div>
    </header>

    <div class="page-wrapper">
        <h1 class="page-title">🔒 Change Your Password</h1>

        <?php if ($message !== ''): ?>
            <div class="alert-error">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($valid_user): ?>
            <div class="info-box">
                Enter a new password for <strong><?= htmlspecialchars($username) ?></strong>.<br>
                It must be at least <b>7 characters</b> long.
            </div>

            <form method="POST">
                <div class="form-row">
                    <label for="new_password">New Password:</label>
                    <!-- CHANGED: minlength is now 7 -->
                    <input type="password" id="new_password" name="new_password" required autofocus minlength="7">
                </div>

                <div class="form-row">
                    <label for="confirm_password">Confirm Password:</label>
                    <!-- CHANGED: minlength is now 7 -->
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="7">
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn btn-send">Save Password</button>
                    <button type="button" class="btn btn-cancel" onclick="window.location.href='logout.php'">Cancel</button>
                </div>
            </form>
        <?php endif; ?>
    </div>

</body>
</html>