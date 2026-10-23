<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — Injibara University</title>
    <link rel="stylesheet" href="passsword_reset.css">
</head>
<body>

    <!-- ============ UNIVERSITY HEADER ============ -->
    <div class="header">
        <div class="logo">
            <img src="logoinu.jpg" alt="Injibara University Logo">
        </div>
        <div class="title">
            <h1>INJIBARA UNIVERSITY<br>STUDENT INFORMATION MANAGEMENT SYSTEM</h1>
        </div>
    </div>

    <!-- ============ PAGE CONTENT ============ -->
    <div class="page-wrapper">

        <!-- Page title -->
        

        <!-- Info box -->
        <div class="info-box">
            If you forgot your password an email with a password reset link will be sent to you.
            Click on the link in that email and you will be taken to a page where you can then
            create a new password.
        </div>

        <!-- Success message -->
        <?php if (!empty($message) && $message_type === 'success'): ?>
            <div class="alert alert-success">
                <?= $message ?>

                <?php if (!empty($reset_link)): ?>
                    <div class="reset-link-box">
                        <a href="<?= htmlspecialchars($reset_link) ?>">
                            <?= htmlspecialchars($reset_link) ?>
                        </a>
                    </div>
                    <small class="reset-note">
                        ⓘ In production, this link would be emailed to the user.
                    </small>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Error message -->
        <?php if (!empty($message) && $message_type === 'error'): ?>
            <div class="alert alert-error">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST" class="reset-form" id="resetForm">

            <div class="form-row">
                <label for="username">
                    User Name:
                    <span class="info-icon" title="Enter the username you used to register">ℹ️</span>
                </label>
                <input type="text" id="username" name="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       required autofocus>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn btn-send">Send Reset Link</button>
                <button type="button" class="btn btn-cancel" onclick="goBack()">Cancel</button>
            </div>

        </form>

    </div>

    <script>
    function goBack() {
        window.location.href = 'loginf.php';
    }
    </script>

</body>
</html>