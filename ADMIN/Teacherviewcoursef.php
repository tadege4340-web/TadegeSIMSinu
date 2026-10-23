<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Teacherdashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="iframe-body">
<div class="students-page">
    <div class="students-header" style="margin-bottom: 20px;">
        <h2>📚 My Assigned Courses</h2>
        <p>Below are the courses assigned to you by the Head of Department.</p>
    </div>

    <?php if (empty($my_courses)): ?>
        <div class="empty-state" style="background: #fff; padding: 40px; border-radius: 10px; text-align: center;">
            <div class="es-icon" style="font-size: 40px; margin-bottom: 10px;">📭</div>
            <h3>No Courses Assigned</h3>
            <p>You currently do not have any courses assigned to you.</p>
        </div>
    <?php else: ?>
        <div class="course-grid">
            <?php foreach ($my_courses as $c): ?>
                <div class="course-card">
                    <div class="course-code"><?= htmlspecialchars($c['course_code']) ?></div>
                    <div class="course-name"><?= htmlspecialchars($c['course_name']) ?></div>
                    <div class="course-meta">
                        <div><i class="fas fa-clock"></i> <strong><?= htmlspecialchars($c['credit_hour']) ?> CH</strong></div>
                        <div>
                            <span class="badge">Year <?= htmlspecialchars($c['course_year']) ?></span>
                            <span class="badge">Sem <?= htmlspecialchars($c['course_semester']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>