<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// 1. Security Check
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['head', 'admin'])) {
    header("Location: loginf.php");
    exit();
}

$hod_department = trim($_SESSION['department'] ?? '');
$hod_college    = trim($_SESSION['college'] ?? '');

// 2. Fetch All Teachers Query
$sql = "SELECT full_name, gender, email, phone 
        FROM teachers 
        WHERE 1=1";
        
$params = [];
$types  = "";

if ($_SESSION['role'] === 'head') {
    if (empty($hod_department)) {
        die("Error: Department not set.");
    }
    $sql .= " AND department = ?";
    $params[] = $hod_department;
    $types .= "s";
}

$sql .= " ORDER BY full_name ASC";

$stmt = $conn->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$teachers = [];
while ($row = $result->fetch_assoc()) {
    $teachers[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Head.css">
    <style> body { background-color: #f4f6f9; padding: 20px; } </style>
</head>
<body>
    <div class="page-container">
        <div class="page-header">
            <div class="header-text">
                <h2>👥 Total Teachers</h2>
                <p>Department: <strong><?= htmlspecialchars($hod_department ?: 'All Departments (Admin)') ?></strong></p>
            </div>
            <div class="total-badge">Total Teachers: <?= count($teachers) ?></div>
        </div>

        <?php if (empty($teachers)): ?>
            <div class="empty-state"><h3>No teachers found.</h3></div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr><th>Full Name</th><th>Gender</th><th>Email</th><th>Phone</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $t): ?>
                            <tr>
                                <td class="student-name"><?= htmlspecialchars($t['full_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($t['gender'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($t['email'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($t['phone'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>