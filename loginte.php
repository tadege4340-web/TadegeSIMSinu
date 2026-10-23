<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - SIMS</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="content-area">
    <div class="login-box">
        <h2>LOGIN</h2>
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'registered'): ?>
            <p class="sccuss">Account created successfully. please login.</p>
            <?php endif; ?>

        

        <form action="logincheck.php" method="POST">
            <label for="username">Email:</label><br>
            <input type="email" id="username" name="email" required><br><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>

            <input type="submit" value="Login">
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>

<?php if(isset($_GET['error'])): ?>
    <?php if ($_GET['error'] == 'wrongpass'): ?>
        <p class="error">incorrect password .please try again.</p>
        <?php endif; ?>
        <?php if($_GET['error']  == 'noemail'): ?>
            <p class="error">Email not found .please create an account.</p>
            <?php endif; ?>
            <?php endif; ?>
</div>
</body>
</html>