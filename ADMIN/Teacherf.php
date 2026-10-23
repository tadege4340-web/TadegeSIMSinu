<?php
session_start();
// Security Check: Only allow teachers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: loginf.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Teacher Dashboard</title></head>
<body>
    <h1>Welcome Teacher: <?= htmlspecialchars($_SESSION['full_name']) ?></h1>
    <p>This is the teacher page.</p>
    <a href="logout.php">Logout</a>
</body>
</html>