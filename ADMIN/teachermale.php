<?php
require 'db.php';
include 'header.php';

// ====== COUNT MALE TEACHERS ======
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM teachers WHERE gender = 'Male'");
$maleTeachers = mysqli_fetch_assoc($result)['total'];
?>

<h2 style="text-align:center; color:#2980b9;"> Male Teachers</h2>

<p style="text-align:center; font-size:22px; font-weight:bold; color:#2c3e50;">
    Total Male Teachers:
    <span style="background:#2980b9; color:white; padding:6px 18px; border-radius:20px;">
        <?= $maleTeachers ?>
    </span>
</p>

<?php include 'footer.php'; ?>