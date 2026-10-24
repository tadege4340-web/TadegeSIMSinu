<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['loginf.php', 'login.php', 'login2.php',
                 'registerf.php', 'register.php', 'signupreg.php'];

if (!isset($_SESSION['user_id']) && !in_array($current_page, $public_pages)) {
    header("Location: loginf.php");
    exit();
}

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
    <link rel="stylesheet" href="adminuuhome.css">
</head>
<body>

    <!-- FIXED HEADER -->
    <div class="top-header">
        <div class="header-logo">
            <img src="logoinu.jpg" alt="Injibara University Logo">
        </div>
        <div class="header-title">
            <h1>INJIBARA UNIVERSITY<br>STUDENT INFORMATION MANAGEMENT SYSTEM</h1>
        </div>
    </div>

    <!-- WELCOME BAR -->
    <div class="welcome-bar">
         Welcome, <?= htmlspecialchars($welcome_name) ?>!
        <span class="welcome-role">
            (<?= htmlspecialchars($_SESSION['role'] ?? 'user') ?>)
        </span>
    </div>

    <!-- SIDEBAR -->
    <div class="admin-sidebar">

        <a href="homeh.php" class="direct-link"> DASHBOARD</a>
        
        <button class="dropdown-btn">COLLEGE</button>
        <div class="dropdown-container">
           <a href="ctechnology.php">College of Engineering and Technology</a>
            <a href="cmedicine.php">College of Medicine and Health Sciences</a>
            <a href="ceconomics.php">College of Business and Economics</a>
            <a href="cnatural.php">College of Natural and Computational Sciences</a>
            <a href="cfood.php">College of Agriculture, Food, Environment and Climate Science</a>
             <a href="csocial.php">College of Social Science and Humanities</a>
            <a href="cbehavioral.php">College of Education and Behavioral Science</a>
            <a href="claw.php">School of Law</a>
        </div>
       
        <button class="dropdown-btn"> STUDENT</button>
        <div class="dropdown-container">
            <a href="addf.php"> Add Student</a>
            <a href="view.php"> View Students</a>
            <a href="update.php"> Update Student</a>
            <a href="delete.php"> Delete Student</a>
            <a href="search.php"> Search Student</a>
        </div>

        <button class="dropdown-btn"> TEACHER</button>
        <div class="dropdown-container">
            <a href="teacherform.php"> Add Teacher</a>
            <a href="teacherview.php"> View Teachers</a>
            <a href="teacherdelete.php">Delete Teacher</a>
            <a href="teachermale.php">Total Male Teachers</a>
            <a href="teacherfemale.php"> Total Female Teachers</a>
            <a href="teachertotal.php"> Total Teachers</a>
        </div>

        <button class="dropdown-btn"> COURSE</button>
        <div class="dropdown-container">
            <a href="mycourseviewadn.php">View Courses</a>
        </div>

        <button class="dropdown-btn"> GRADE</button>
        <div class="dropdown-container">
            <a href="mygradeview.php">View Grades</a>
            <a href="mygradechanger.php">Mygarde Changer</a>
            <a href="mygradeapprove.php">Mygrade Aprove</a>
        </div>

        <a href="logout.php" class="direct-link logout-link"> LOGOUT</a>

    </div>

    <!-- MAIN CONTENT OPENS (do NOT close here) -->
    <div class="main-content">
        <!-- The content from homeh.php, view.php, etc. will be inserted here -->