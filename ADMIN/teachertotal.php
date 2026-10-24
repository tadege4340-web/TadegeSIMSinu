<?php
require 'db.php';

// ====== TOTAL TEACHERS COUNT (PDO) ======
try {
    $stmt = $conn->query("SELECT COUNT(*) AS total FROM teachers");
    $totalTeachers = $stmt->fetchColumn();
} catch (PDOException $e) {
    $totalTeachers = 0;
}

include 'header.php';
?>

<h2 style="text-align:center; color:#2c3e50;">All Teachers</h2>

<!-- ====== TOTAL NUMBER DISPLAY ====== -->
<p style="text-align:center; font-size:20px; font-weight:bold; color:#2c3e50;">
    📊 Total Teachers: 
    <span style="background:#2c3e50; color:white; padding:5px 15px; border-radius:20px;">
        <?= htmlspecialchars($totalTeachers) ?>
    </span>
</p>

<?php include 'footer.php'; ?>