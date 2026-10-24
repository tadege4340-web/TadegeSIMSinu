<?php
// Safely start session ONLY if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php'; 

// Security Check: Only allow teachers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: loginf.php");
    exit();
}

// Get the display name safely
$display_name = $_SESSION['full_name'] ?? 'Teacher';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Injibara University - Teacher Portal</title>
    <link rel="stylesheet" href="Teacherdashboard.css">
</head>
<body>

    <header class="teacher-header">
        <div class="header-logo">
            <img src="logoinu.jpg" alt="Injibara University Logo">
        </div>
        <div class="header-title">
            <h1>INJIBARA UNIVERSITY</h1>
            <p>STUDENT INFORMATION MANAGEMENT SYSTEM</p>
        </div>
    </header>

    <div class="welcome-bar">
        <span class="welcome-role">Teacher</span>
        <span class="welcome-text">
            Welcome, <?= htmlspecialchars($display_name) ?>!
        </span>
    </div>

    <div class="teacher-layout">
        <aside class="teacher-sidebar">
            <div class="sidebar-heading"></div>

            <a href="teacherhome.php" target="teacherContent" class="menu-link">
                <span></span> Dashboard
            </a>

            <button type="button" class="dropdown-btn">
                <span></span> Students <span class="arrow">⌄</span>
            </button>
            <div class="dropdown-container">
                <a href="Teacherviewstudent.php" target="teacherContent">View Students</a>
            </div>

            <button type="button" class="dropdown-btn">
                <span></span> Courses <span class="arrow">⌄</span>
            </button>
            <div class="dropdown-container">
                <a href="Teacherviewcourse.php" target="teacherContent">View My Courses</a>
            </div>

            <button type="button" class="dropdown-btn">
                <span></span> Grades <span class="arrow">⌄</span>
            </button>
            <div class="dropdown-container">
                <a href="Teacheraddgrade.php" target="teacherContent">Add Grade</a>
                <a href="Teacherviewgrade.php" target="teacherContent">View Grades</a>
            </div>

            <a href="change_password.php" target="teacherContent" class="menu-link">
                <span></span> Change Password
            </a>

            <a href="logout.php" class="menu-link logout-link">
                <span></span> Logout
            </a>
        </aside>

        <main class="teacher-main">
            <div class="content-toolbar">
                <span></span>
                <span class="portal-status"></span>
            </div>

            <!-- FIXED: Changed srcdoc to src="teacherhome.php" -->
            <iframe id="teacherContent" name="teacherContent" src="teacherhome.php" title="Teacher dashboard content" loading="eager"></iframe>
        </main>
    </div>

    <footer class="teacher-footer">
        <p>&copy; <?= date('Y') ?> Injibara University. Student Information Management System.</p>
        <p>Teacher Portal | All Rights Reserved</p>
    </footer>

    <script>
        document.querySelectorAll('.dropdown-btn').forEach(button => {
            button.addEventListener('click', function () {
                this.classList.toggle('active');
                const menu = this.nextElementSibling;
                const isOpen = menu.style.display === 'block';
                menu.style.display = isOpen ? 'none' : 'block';
            });
        });
    </script>
</body>
</html>