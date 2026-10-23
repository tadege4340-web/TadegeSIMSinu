<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Head.css">
    <style>
        body { background-color: #f4f6f9; padding: 20px; }
    </style>
</head>
<body>

    <div class="page-container">

        <!-- Page Header -->
        <div class="page-header">
            <div class="header-text">
                <h2>Department Teachers</h2>
                <p>
                    Showing teachers for: <strong><?= htmlspecialchars($hod_department ?: 'All Departments (Admin)') ?></strong> 
                    <?= !empty($hod_college) ? '(' . htmlspecialchars($hod_college) . ')' : '' ?>
                </p>
            </div>
            <div class="total-badge">
                Total Teachers: <?= count($teachers) ?>
            </div>
        </div>

        <!-- Teachers Table -->
        <?php if (empty($teachers)): ?>
            <div class="empty-state">
                <h3>No teachers found</h3>
                <p>There are no teachers registered in this department.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $t): ?>
                            <tr>
                                <td class="student-name"><?= htmlspecialchars($t['full_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($t['gender'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($t['email'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($t['phone'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>