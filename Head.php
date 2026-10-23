<?php
session_start();
// Security Check: Only allow Heads of Department
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'head') {
    header("Location: loginf.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Head of Department Dashboard</title></head>
<body>
    <h1>Welcome Head of Department: <?= htmlspecialchars($_SESSION['full_name']) ?></h1>
    <p>This is the Head of Department page.</p>
    <a href="logout.php">Logout</a>
</body>
</html>