<?php
session_start();
require 'db.php';
$email = $_POST['email'];
$password =  $_POST['password'];
$sql = "SELECT * FROM users WHERE email = '$email'  LIMIT 1";
$result = mysqli_query($conn, $sql);
if(mysqli_num_rows($result) == 1){
    $user = mysqli_fetch_assoc($result);
    if(password_verify($password, $user['password'])){
        $_SESSION['user_id'] = $user['id'];
          $_SESSION['full_name'] = $user['full_name'];
           $_SESSION['email'] = $user['email'];
          header("Location: dashboard.php");
          exit;


    }
    else{
      header("Location: loginte.php?error=wrongpass");
      exit;
    }

}
else{
    header("Location: loginte.php?error=noemail");
    exit;
}
?>