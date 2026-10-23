<?php
require 'db.php';
$fname   = $_POST['fname'] ?? '';
$lname   = $_POST['lname'] ?? '';
$address = $_POST['address'] ?? '';
$sex     = $_POST['sex'] ?? '';
$phone   = $_POST['phone'] ?? '';
$email   = $_POST['email'] ?? '';

$sql = "INSERT INTO stud (fname, lname, address, sex, phone, email)
        VALUES ('$fname', '$lname', '$address', '$sex', '$phone', '$email')";
if (mysqli_query($conn, $sql)) {
    header("Location: index.php?msg=created");
} else {
    echo "Error: " . mysqli_error($conn);
}
mysqli_close($conn);
?>
