<?php
require 'db.php';

echo "<h3>Testing student table...</h3>";

$result = mysqli_query($conn, "SELECT COUNT(*) AS c FROM student");

if (!$result) {
    echo "<p style='color:red;'>❌ ERROR: " . mysqli_error($conn) . "</p>";
} else {
    $row = mysqli_fetch_assoc($result);
    echo "<p style='color:green;'>✅ Students in database: <b>" . $row['c'] . "</b></p>";
}

echo "<h3>Testing teachers table...</h3>";
$result = mysqli_query($conn, "SELECT COUNT(*) AS c FROM teachers");
if (!$result) {
    echo "<p style='color:red;'>❌ ERROR: " . mysqli_error($conn) . "</p>";
} else {
    $row = mysqli_fetch_assoc($result);
    echo "<p style='color:green;'>✅ Teachers in database: <b>" . $row['c'] . "</b></p>";
}

echo "<h3>Testing addcourse table...</h3>";
$result = mysqli_query($conn, "SELECT COUNT(*) AS c FROM addcourse");
if (!$result) {
    echo "<p style='color:red;'>❌ ERROR: " . mysqli_error($conn) . "</p>";
} else {
    $row = mysqli_fetch_assoc($result);
    echo "<p style='color:green;'>✅ Courses in database: <b>" . $row['c'] . "</b></p>";
}

echo "<h3>Testing student_courses table...</h3>";
$result = mysqli_query($conn, "SELECT COUNT(*) AS c FROM student_courses");
if (!$result) {
    echo "<p style='color:red;'>❌ ERROR: " . mysqli_error($conn) . "</p>";
} else {
    $row = mysqli_fetch_assoc($result);
    echo "<p style='color:green;'>✅ Assigned: <b>" . $row['c'] . "</b></p>";
}
?>