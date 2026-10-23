<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pages that don't require login
$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['loginf.php', 'login.php', 'login2.php',
                 'registerf.php', 'register.php', 'signupreg.php'];

if (!isset($_SESSION['user_id']) && !in_array($current_page, $public_pages)) {
    header("Location: loginf.php");
    exit();
}

// Build welcome name
$welcome_name = trim(($_SESSION['firstname'] ?? '') . ' ' . ($_SESSION['father_name'] ?? ''));
if ($welcome_name === '') {
    $welcome_name = $_SESSION['username'] ?? 'Guest';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Injibara University - Admin</title>
    <link rel="stylesheet" href="css22.css">
</head>
<body>

    <!-- ===== WELCOME BAR ===== -->
    <div class="welcome-bar" style="
        background: #d1fae5;
        color: #065f46;
        padding: 12px 25px;
        text-align: right;
        font-weight: bold;
        font-size: 15px;
        border-bottom: 3px solid #10b981;">
        👋 Welcome, <?= htmlspecialchars($welcome_name) ?>!
        <span style="
            font-weight: normal;
            color: #065f46;
            font-size: 13px;
            margin-left: 10px;">
            (<?= htmlspecialchars($_SESSION['role'] ?? 'user') ?>)
        </span>
    </div>

    <!-- ===== LOGO + TITLE ===== -->
    <div class="first">
        <div class="second">
            <img src="logoinu.jpg" width="100" height="100" alt="Injibara University Logo">
        </div>
        <div class="third">
            <h1>INJIBARA UNIVERSITY<br>STUDENT INFORMATION MANAGEMENT SYSTEM</h1>
        </div>
    </div>

    <!-- ===== NAV BAR ===== -->
    <div class="fivth"></div>

    <!-- ===== ADMIN SIDEBAR ===== -->
    <div class="admin-sidebar">
        <a href="teacherform.php">ADD TEACHER</a>
        <a href="teacherview.php">VIEW TEACHER</a>
        <a href="teacherdelete.php">DELETE TEACHER</a>
        <a href="teachermale.php">TOTAL NUMBERS OF MALE TEACHER</a>
        <a href="teacherfemale.php">TOTAL NUMBERS OF FEMALE TEACHER</a>
        <a href="teachertotal.php">TOTAL NUMBERS OF TEACHER</a>
        <a href="gradeform.php">ADD GRADE</a>
        <a href="viewgradeadmin.php">VIEW GRADE</a>
        <a href="addf.php">ADD STUDENT</a>
        <a href="view.php">VIEW STUDENT</a>
        <a href="update.php">UPDATE STUDENT</a>
        <a href="delete.php">DELETE STUDENT</a>
        <a href="search.php">SEARCH STUDENT</a>
        <a href="mycourseadmin.php">ADD COURSES</a>
        <a href="mycourseassignadmin.php">ASSIGN COURSE</a>
        <a href="mycourseviewadn.php">VIEW COURSES</a>
        <a href="logout.php" class="logout-link">LOGOUT</a>
    </div>

    <!-- ===== MAIN CONTENT OPENS (footer.php closes this) ===== -->
    <div class="main-content">