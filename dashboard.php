<?php
session_start();
// Prevent caching (VERY IMPORTANT)
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache"); 
header("Expires: 0");
// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: dashboard.php?error=loginrequired");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>ፋርማሲ ሲስተም </h2>
    <a href="#">Dashboard</a>
    <a href="#">Drugs</a>
    <a href="#">Sales</a>
    <a href="#">Stock</a>
    <a href="#">Expired</a>
    <a href="#">Suppliers</a>
    <a href="#">Settings</a>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<!-- Main content -->
<div class="main">

    <!-- Header -->
    <div class="header">
        <h1>Pharmacy Dashboard</h1>
        <div class="user-info">
            <h2>Welcome, <?= $_SESSION['full_name'] ?>!</h2>
            <h3><?= $_SESSION['email'] ?>!</h3>
        </div>
    </div>

    <!-- Cards Section -->
    <div class="cards">

        <div class="card">
            <h3>Total Medicines</h3>
            <p>245</p>
        </div>

        <div class="card">
            <h3>Today Sales</h3>
            <p>3,450 Birr</p>
        </div>

        <div class="card">
            <h3>Low Stock</h3>
            <p>12 Items</p>
        </div>

        <div class="card">
            <h3>Expired Soon</h3>
            <p>8 Items</p>
        </div>

    </div>

    <!-- Table Section -->
    <div class="section-title">Recently Added Medicines</div>

    <table class="table">
        <thead>
            <tr>
                <th>Drug Name</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Expiry Date</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Paracetamol</td>
                <td>Pain Killer</td>
                <td>120</td>
                <td>2026-05-10</td>
            </tr>

            <tr>
                <td>Amoxicillin</td>
                <td>Antibiotic</td>
                <td>45</td>
                <td>2025-11-30</td>
            </tr>

            <tr>
                <td>ORS</td>
                <td>Hydration</td>
                <td>90</td>
                <td>2027-01-22</td>
            </tr>

            <tr>
                <td>Vitamin C</td>
                <td>Supplement</td>
                <td>200</td>
                <td>2026-08-12</td>
            </tr>
        </tbody>
    </table>

</div>

</body>
</html>
