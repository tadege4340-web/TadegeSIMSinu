<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - SIMS</title>
<link rel="stylesheet" href="style1.css">
</head>
<body>

<div class="content-area">
    <div class="login-box">
        <h2>Create Account</h2>

        

        <form action="signupreg.php" method="POST">
               <label for="username">full name:</label><br>
            <input type="text" id="username" name="full_name" required><br><br>
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br><br>
   <label for="username">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
               <label for="username">confrim password:</label><br>
            <input type="password" id="confrim password" name="confrim_password" required><br><br>

            <input type="submit" value="Register">
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

</body>
</html>