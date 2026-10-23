<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['loginf.php', 'login.php', 'login2.php', 'registerf.php', 'register.php', 'signupreg.php'];

if (!isset($_SESSION['user_id']) && !in_array($current_page, $public_pages)) {
    header("Location: loginf.php");
    exit();
}

$welcome_name = trim(($_SESSION['firstname'] ?? '') . ' ' . ($_SESSION['father_name'] ?? ''));
if ($welcome_name === '') {
    $welcome_name = $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Guest';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Injibara University - Head of Department</title>
    <link rel="stylesheet" href="Head.css">
</head>
<body>

    <!-- 1. FIXED HEADER -->
    <header class="teacher-header">
        <div class="header-left">
            <div class="header-logo">
                <img src="/phplab/ADMIN/logoinu.jpg" alt="Injibara University Logo">
            </div>
        </div>

        <div class="header-center">
            <h1>INJIBARA UNIVERSITY</h1>
            <p> STUDENT INFORMATION MANAGEMENT SYSTEM</p>
        </div>

        <div class="header-right-empty"></div>
    </header>

    <!-- 2. WELCOME BAR -->
    <div class="welcome-bar">
        <span class="welcome-text">Welcome, <?= htmlspecialchars($welcome_name) ?>!</span>
        <span class="welcome-role">(Head of Department)</span>
    </div>

    <!-- 3. MAIN LAYOUT -->
    <div class="teacher-layout">
        
        <!-- SIDEBAR -->
        <aside class="teacher-sidebar">
            <div class="sidebar-heading"></div>

            <!-- Added target="teacherContent" to all links -->
            <a href="Headdashboard.php" target="teacherContent">
                <span></span> DASHBOARD
            </a>

            <!-- 1. STUDENT -->
            <input type="checkbox" id="menu-student" class="dropdown-checkbox">
            <label for="menu-student" class="dropdown-label">
                <span></span> STUDENT <span class="arrow">⌄</span>
            </label>
            <div class="dropdown-container">
                <a href="Headviewstudent.php" target="teacherContent">View Students</a>
            </div>

            <!-- 2. TEACHER -->
            <input type="checkbox" id="menu-teacher" class="dropdown-checkbox">
            <label for="menu-teacher" class="dropdown-label">
                <span></span> TEACHER <span class="arrow">⌄</span>
            </label>
            <div class="dropdown-container">
                <a href="Headviewteacher.php" target="teacherContent">View Teachers</a>
                <a href="Headviewteachermale.php" target="teacherContent">Total Male Teachers</a>
                <a href="Headviewteacherfemale.php" target="teacherContent">Total Female Teachers</a>
                <a href="Headviewteachertotal.php" target="teacherContent">Total Teachers</a>
            </div>

            <!-- 3. COURSE -->
            <input type="checkbox" id="menu-course" class="dropdown-checkbox">
            <label for="menu-course" class="dropdown-label">
                <span></span> COURSE <span class="arrow">⌄</span>
            </label>
            <div class="dropdown-container">
                <a href="Headcourseadd.php" target="teacherContent">Add Course</a>
                <a href="Headcourseassignstudent.php" target="teacherContent">Assign Course For Student</a>
                <a href="Headcourseassignteacher.php" target="teacherContent">Assign Course For Teacher</a>
                <a href="Headviewcourse.php" target="teacherContent">View Courses</a>
            </div>

            <!-- 4. GRADE -->
            <input type="checkbox" id="menu-grade" class="dropdown-checkbox">
            <label for="menu-grade" class="dropdown-label">
                <span></span> GRADE <span class="arrow">⌄</span>
            </label>
            <div class="dropdown-container">
                <a href="Headviewgrade.php" target="teacherContent">View Grades</a>
                <a href="Headsubmitgrade.php" target="teacherContent">Mygrade Submit</a>
            </div>
            
            <a href="logout.php" class="logout-link">
                <span></span> LOGOUT
            </a>
        </aside>

        <!-- MAIN CONTENT (IFRAME) -->
        <main class="teacher-main">
            <!-- The iframe loads the pages inside -->
            <iframe id="teacherContent" name="teacherContent" src="Headdashboard.php" title="Dashboard Content" loading="eager"></iframe>
        </main>