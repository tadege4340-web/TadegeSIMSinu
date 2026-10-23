<?php
// 1. Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "lab";

$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 2. Receive form data
$first = $_POST['t'];   // ❌ remove the $ inside the quotes
$last = $_POST['f'];   // ❌ same here

// 3. Insert query
$a= "INSERT INTO lab1 (fname, lname) VALUES ('$first', '$last')";
//      ✔ column names must be written directly, not variables

if (mysqli_query($conn, $a)) {
    echo "Record inserted successfully!";
} else {
    echo "Error: " . mysqli_error($conn);
}

// 4. Close connection
mysqli_close($conn);
?>
