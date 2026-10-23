<?php
// --------------------
// DATABASE CONNECTION
// --------------------
$host = "localhost";
$user = "root";
$pass = "";
$db   = "lab";

$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}



    $firstname = $_POST['firstname'];
    $lastname  = $_POST['lastname'];
    $age       = $_POST['age'];
    $studentid = $_POST['studentid'];
    $gender    = $_POST['gender'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];
    $department= $_POST['department'];
    $year      = $_POST['year'];

    $sql = "INSERT INTO student 
            (fname, lname, age,id, gender, email, phone, department, year)
            VALUES 
            ('$firstname', '$lastname', '$age', '$studentid', '$gender', '$email', '$phone', '$department', '$year')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Student added successfully!');</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }

?>
