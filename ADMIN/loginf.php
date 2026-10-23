<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

    <!-- ============ TOP MENU ============ -->
    <div class="menu">
        
    </div>

    <!-- ============ CONTENT ============ -->
    <div class="content-area">
        <div class="login-box">

            <!-- Success message after registration -->
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'registered'): ?>
                <p class="success">✅ Account created successfully. Please login.</p>
            <?php endif; ?>

            <form action="login.php" method="POST" id="loginForm">

                <!-- Username -->
                <div class="form-row">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>

                <!-- Password with eye toggle -->
                <div class="form-row">
                    <label for="password">Password:</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required>
                        <button type="button"
                                class="toggle-password"
                                onclick="togglePassword('password', this)"
                                aria-label="Show password">🔐</button>
                    </div>
                </div>

                <!-- Remember Login -->
                <div class="remember-row">
                    <input type="checkbox" id="remember" name="remember" value="1">
                    <label for="remember">Remember Login</label>
                </div>

                <!-- Login + Cancel -->
                <div class="btn-row">
                    <button type="submit" class="btn btn-login">Login</button>
                    <button type="button" class="btn btn-cancel"
                            onclick="resetForm()">Cancel</button>
                </div>

            </form>

            <hr class="divider">

            <!-- Reset Password -->
            <div class="reset-row">
               <button type="button" class="btn-reset"
        onclick="window.location.href='password_reset.php';">
    Reset Password
</button>
            </div>

            <!-- Error messages -->
            <?php if (isset($_GET['error'])): ?>
                <div class="error-msg">
                    <?php
                        $err = $_GET['error'];
                        if ($err === 'wrongpass')          echo "❌ Incorrect password. Please try again.";
                        elseif ($err === 'nousername')     echo "❌ Username not found. Please create an account.";
                        elseif ($err === 'emptyfields')    echo "❌ Please fill in both username and password.";
                        elseif ($err === 'servererror')    echo "❌ Server error. Please try again.";
                        elseif ($err === 'invalidrole')    echo "❌ Invalid account role.";
                        elseif ($err === 'studentnotlinked') echo "❌ Account not linked to a student profile.";
                        else                               echo "❌ Login failed. Please try again.";
                    ?>
                </div>
            <?php endif; ?>

            <!-- Create account link -->
            <div class="create">
                
            </div>

        </div>
    </div>

    <!-- ============ JAVASCRIPT ============ -->
    <script>
    /* Toggle password visibility */
    function togglePassword(fieldId, icon) {
        const field = document.getElementById(fieldId);
        if (!field) return;

        if (field.type === 'password') {
            field.type = 'text';
            icon.textContent = '🔓';
            icon.classList.add('visible');
        } else {
            field.type = 'password';
            icon.textContent = '🔐';
            icon.classList.remove('visible');
        }
    }

    /* Reset form */
    function resetForm() {
        document.getElementById('loginForm').reset();
        document.getElementById('username').focus();
    }
    </script>

</body>
</html>
<?php if (isset($_GET['msg']) && $_GET['msg'] === 'passwordreset'): ?>
    <div class="success">
        ✅ Password changed successfully! Please login with your new password.
    </div>
<?php endif; ?>