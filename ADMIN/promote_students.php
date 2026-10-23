<?php
require 'db.php';
// Example: Move all Year 3 Sem 1 students to Year 3 Sem 2
$sql = "UPDATE users2 SET current_semester = 2 WHERE year = 3 AND current_semester = 1";
$conn->query($sql);
echo "Students promoted to Semester 2!";
?>