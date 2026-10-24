<?php
require 'db.php';
include 'header.php';

// ====== COUNT FEMALE TEACHERS (PDO) ======
try {
    $stmt = $conn->query("SELECT COUNT(*) AS total FROM teachers WHERE gender = 'Female'");
    $femaleTeachers = $stmt->fetchColumn();
} catch (PDOException $e) {
    $femaleTeachers = 0;
}
?>

<h2 style="text-align:center; color:#c0392b;"> Female Teachers</h2>

<p style="text-align:center; font-size:22px; font-weight:bold; color:#2c3e50;">
    Total Female Teachers:
    <span style="background:#c0392b; color:white; padding:6px 18px; border-radius:20px;">
        <?= htmlspecialchars($femaleTeachers) ?>
    </span>
</p>

<?php include 'footer.php'; ?>