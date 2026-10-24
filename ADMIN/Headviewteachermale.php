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

// 2. Fetch Male Teachers Query (PDO)
$sql = "SELECT full_name, gender, email, phone 
        FROM teachers 
        WHERE gender = 'Male'";
        
$params = [];

// Filter by department if the user is a Head
if ($_SESSION['role'] === 'head') {
    if (empty($hod_department)) {
        die("Error: Department not set.");
    }
    $sql .= " AND department = :dept";
    $params['dept'] = $hod_department;
}

$sql .= " ORDER BY full_name ASC";

$teachers = [];

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}
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
                <h2>👨‍🏫 Total Male Teachers</h2>
                <p>Department: <strong><?= htmlspecialchars($hod_department ?: 'All Departments (Admin)') ?></strong></p>
            </div>
            <div class="total-badge">Total Males: <?= count($teachers) ?></div>
        </div>

        <?php if (empty($teachers)): ?>
            <div class="empty-state"><h3>No male teachers found.</h3></div>
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