<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Head.css">
    <style> body { background-color: #f4f6f9; padding: 20px; } </style>
</head>
<body>
    <div class="page-container">
        
        <!-- WELCOME CARD -->
        <div class="welcome-card" style="background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h2>Welcome, Head of <?= htmlspecialchars($hod_department ?: 'Department') ?>!</h2>
            <p>Here is an overview of your department. You can manage students, teachers, courses, and grades from the sidebar menu.</p>
        </div>

        <!-- STATS GRID -->
        <div class="dash-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            
            <div class="stat-card card-blue" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-left: 5px solid #3b82f6;">
                <div class="stat-info">
                    <h3 style="font-size: 28px; color: #1e3a5f; margin: 0;"><?= number_format($stats['students']) ?></h3>
                    <p style="color: #64748b; margin-top: 5px;">Total Students</p>
                </div>
            </div>
            
            <div class="stat-card card-green" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-left: 5px solid #10b981;">
                <div class="stat-info">
                    <h3 style="font-size: 28px; color: #1e3a5f; margin: 0;"><?= number_format($stats['teachers']) ?></h3>
                    <p style="color: #64748b; margin-top: 5px;">Total Teachers</p>
                </div>
            </div>
            
            <div class="stat-card card-purple" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-left: 5px solid #8b5cf6;">
                <div class="stat-info">
                    <h3 style="font-size: 28px; color: #1e3a5f; margin: 0;"><?= number_format($stats['courses']) ?></h3>
                    <p style="color: #64748b; margin-top: 5px;">Total Courses</p>
                </div>
            </div>
            
            <div class="stat-card card-orange" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-left: 5px solid #f59e0b;">
                <div class="stat-info">
                    <h3 style="font-size: 28px; color: #1e3a5f; margin: 0;"><?= number_format($stats['pending_grades']) ?></h3>
                    <p style="color: #64748b; margin-top: 5px;">Pending Grades</p>
                </div>
            </div>
        </div>

    </div>
</body>
</html>