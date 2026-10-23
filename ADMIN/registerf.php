<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
 <div class="first">
   <div class="second">
     <img src="logoinu.jpg"width="100"height="100" target="frame1" >
   </div>
   <div class="third">
     <h1>INJIBARA UNIVERSITY<br>STUDENT INFORMATION MANAGEMENT SYSTEM</h1>
     </div>
   <div class="fourth"> <a href="login.html" target="frame4">LOGIN</a></div>
   
   
 </div> 
<div class="fivth">
    <a href="a.php">HOME</a>
      <a href="B.php">CONTACT US</a>
        <a href="C.php">NEWS</a>
       

   </div>
   <div class="content-area">
    <div class="login-box">
        <h2>Create Account</h2>

        <form action="register.php" method="POST">
               <label for="username">full name:</label><br>
            <input type="text" id="username" name="full_name" required><br><br>
            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br><br>
   <label for="username">Email:</label><br>
            <input type="email" id="username" name="email" required><br><br>
            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>
               <label for="username">confrim password:</label><br>
            <input type="password" id="username" name="confrim_password" required><br><br>

            <input type="submit" value="Register">
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

</body>
</html>
   