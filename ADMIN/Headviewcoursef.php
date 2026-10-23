<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Head.css">
    <style> body { background-color: #f4f6f9; padding: 20px; } </style>
</head>
<body>
    <div class="page-container">
        <div class="page-header">
            <div class="header-text">
                <h2>📚 Department Courses</h2>
                <p>Department: <strong><?= htmlspecialchars($hod_department ?: 'All Departments (Admin)') ?></strong></p>
            </div>
            <div class="total-badge">Total Courses: <?= count($courses) ?></div>
        </div>

        <?php if (empty($courses)): ?>
            <div class="empty-state"><h3>No courses found.</h3></div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credit Hour</th>
                            <th>Year</th>
                            <th>Semester</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $c): ?>
                            <tr>
                                <td class="student-name"><?= htmlspecialchars($c['course_code'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($c['course_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($c['credit_hour'] ?? 'N/A') ?></td>
                                <td>Year <?= htmlspecialchars($c['course_year'] ?? 'N/A') ?></td>
                                <td>Semester <?= htmlspecialchars($c['course_semester'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>