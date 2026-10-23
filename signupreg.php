<?php
require 'db.php';
$full_name= $_POST['full_name'];
$username= $_POST['username'];
$email= $_POST['email'];
$password= $_POST['password'];
$confrim= $_POST['confrim_password'];
if($password !== $confrim){
    die("password is do not match!");
}
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$sql = "INSERT INTO users (full_name, username, email, password)
        VALUES ('$full_name', '$username', '$email', '$hashed_password')";

if(mysqli_query($conn , $sql)){
  header("Location: loginte.php?msg=registered");

    exit;
}
else{
    echo "error: " . mysqli_error($conn);
}
?>