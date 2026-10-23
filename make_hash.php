<?php
$password = '123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . "<br>";
echo "Hash: " . $hash . "<br><br>";
echo "Verify test: ";
var_dump(password_verify($password, $hash));