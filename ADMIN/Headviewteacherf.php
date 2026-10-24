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
                <h2>Department Teachers</h2>
                <p>
                    Showing teachers for: <strong><?= htmlspecialchars($hod_department ?: 'All Departments (Admin)') ?></strong> 
                    <?= !empty($hod_college) ? '(' . htmlspecialchars($hod_college) . ')' : '' ?>
                </p>
            </div>
            <div class="total-badge" style="background: #1e4e79; color: #fff; padding: 10px 15px; border-radius: 5px; font-weight: bold;">
                Total Teachers: <?= count($teachers) ?>
            </div>
        </div>

        <!-- Teachers Table -->
        <?php if (empty($teachers)): ?>
            <div class="empty-state" style="background: #fff; padding: 40px; text-align: center; border-radius: 8px;">
                <h3>No teachers found</h3>
                <p>There are no teachers registered in this department.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th style="padding: 12px 15px; color: #475569;">Full Name</th>
                            <th style="padding: 12px 15px; color: #475569;">Gender</th>
                            <th style="padding: 12px 15px; color: #475569;">Email</th>
                            <th style="padding: 12px 15px; color: #475569;">Phone</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teachers as $t): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 15px; font-weight: bold;"><?= htmlspecialchars($t['full_name'] ?? 'N/A') ?></td>
                                <td style="padding: 12px 15px;"><?= htmlspecialchars($t['gender'] ?? 'N/A') ?></td>
                                <td style="padding: 12px 15px;"><?= htmlspecialchars($t['email'] ?? 'N/A') ?></td>
                                <td style="padding: 12px 15px;"><?= htmlspecialchars($t['phone'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>