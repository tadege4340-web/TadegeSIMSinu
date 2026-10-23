<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Head.css">
   
</head>
<body>
    <div class="page-container">
        
        <!-- WELCOME CARD -->
        <div class="welcome-card">
            <h2>Welcome , Head of <?= htmlspecialchars($hod_department ?: 'Department') ?>! </h2>
            <p>Here is an overview of your department. You can manage students, teachers, courses, and grades from the sidebar menu.</p>
        </div>

        <!-- STATS GRID -->
        <div class="dash-grid">
            <div class="stat-card card-blue">
                <div class="stat-icon"></div>
                <div class="stat-info">
                    <h3><?= number_format($stats['students']) ?></h3>
                    <p>Total Students</p>
                </div>
            </div>
            
            <div class="stat-card card-green">
                <div class="stat-icon"></div>
                <div class="stat-info">
                    <h3><?= number_format($stats['teachers']) ?></h3>
                    <p>Total Teachers</p>
                </div>
            </div>
            
            <div class="stat-card card-purple">
                <div class="stat-icon"></div>
                <div class="stat-info">
                    <h3><?= number_format($stats['courses']) ?></h3>
                    <p>Total Courses</p>
                </div>
            </div>
            
            <div class="stat-card card-orange">
                <div class="stat-icon"></div>
                <div class="stat-info">
                    <h3><?= number_format($stats['pending_grades']) ?></h3>
                    <p>Pending Grades</p>
                </div>
            </div>
        </div>

        <!-- QUICK ACTIONS -->
        <h3 style="color: #17365d; margin-bottom: 15px;"> Quick Actions</h3>
        <div class="quick-actions">
            <a href="Headcourseadd.php" target="teacherContent" class="action-btn">
                <span></span> Add New Course
            </a>
            <a href="Headcourseassignstudent.php" target="teacherContent" class="action-btn">
                <span></span> Assign Course to Student
            </a>
            <a href="Headcourseassignteacher.php" target="teacherContent" class="action-btn">
                <span></span> Assign Course to Teacher
            </a>
            <a href="Headsubmitgrade.php" target="teacherContent" class="action-btn">
                <span></span> Submit Grades
            </a>
        </div>

    </div>
</body>
</html>