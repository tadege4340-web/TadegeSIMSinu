<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Head.css">
    <style> 
        body { background-color: #f4f6f9; padding: 20px; } 
        .table-wrapper { background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); overflow-x: auto; }
    </style>
</head>
<body>
    <div class="page-container">

        <!-- Page Header -->
        <div class="page-header" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div class="header-text">
                <h2>📚 Department Courses</h2>
                <p>Department: <strong><?= htmlspecialchars($hod_department ?: 'All Departments (Admin)') ?></strong></p>
            </div>
            <div class="total-badge" style="background: #1e4e79; color: #fff; padding: 10px 15px; border-radius: 5px; font-weight: bold;">
                Total Courses: <?= count($courses) ?>
            </div>
        </div>

        <!-- Courses Table -->
        <?php if (empty($courses)): ?>
            <div class="empty-state" style="background: #fff; padding: 40px; text-align: center; border-radius: 8px;">
                <h3>No courses found.</h3>
                <p>There are no courses registered in this department yet.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th style="padding: 12px 15px; color: #475569;">Course Code</th>
                            <th style="padding: 12px 15px; color: #475569;">Course Name</th>
                            <th style="padding: 12px 15px; color: #475569;">Credit Hour</th>
                            <th style="padding: 12px 15px; color: #475569;">Year</th>
                            <th style="padding: 12px 15px; color: #475569;">Semester</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $c): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 15px; font-weight: bold; color: #1e4e79;"><?= htmlspecialchars($c['course_code'] ?? 'N/A') ?></td>
                                <td style="padding: 12px 15px;"><?= htmlspecialchars($c['course_name'] ?? 'N/A') ?></td>
                                <td style="padding: 12px 15px; text-align: center;"><?= htmlspecialchars($c['credit_hour'] ?? 'N/A') ?></td>
                                <td style="padding: 12px 15px;">Year <?= htmlspecialchars($c['course_year'] ?? 'N/A') ?></td>
                                <td style="padding: 12px 15px;">Semester <?= htmlspecialchars($c['course_semester'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>