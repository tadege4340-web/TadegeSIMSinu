<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Head.css">
    <style> 
        body { background-color: #f4f6f9; padding: 20px; } 
        .filter-bar { margin-bottom: 20px; display: flex; gap: 10px; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .filter-bar input, .filter-bar select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .filter-bar button { padding: 8px 15px; background: #1e4e79; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="page-container">

        <!-- Page Header -->
        <div class="page-header" style="margin-bottom: 20px;">
            <div class="header-text">
                <h2>Department Students</h2>
                <p>
                    Showing students for: <strong><?= htmlspecialchars($hod_department ?: 'All Departments (Admin)') ?></strong> 
                    <?= !empty($hod_college) ? '(' . htmlspecialchars($hod_college) . ')' : '' ?>
                </p>
            </div>
            <div class="total-badge" style="font-size: 16px; font-weight: bold; color: #1e4e79; margin-top: 10px;">
                Total Students: <?= count($students) ?>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <form method="GET" action="Headviewstudent.php" style="display: flex; gap: 10px; width: 100%; flex-wrap: wrap;">
                
                <div class="filter-group" style="flex: 2; min-width: 200px;">
                    <input type="text" name="search" value="<?= htmlspecialchars($search_query) ?>" placeholder="Search by ID or Name..." style="width: 100%;">
                </div>

                <div class="filter-group" style="flex: 1; min-width: 120px;">
                    <select name="year" style="width: 100%;">
                        <option value="">All Years</option>
                        <?php for ($i = 1; $i <= 7; $i++): ?>
                            <option value="<?= $i ?>" <?= $filter_year == $i ? 'selected' : '' ?>>Year <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="filter-group" style="flex: 1; min-width: 120px;">
                    <select name="semester" style="width: 100%;">
                        <option value="">All Semesters</option>
                        <option value="1" <?= $filter_semester == 1 ? 'selected' : '' ?>>Semester 1</option>
                        <option value="2" <?= $filter_semester == 2 ? 'selected' : '' ?>>Semester 2</option>
                    </select>
                </div>

                <div class="filter-actions" style="display: flex; gap: 5px;">
                    <button type="submit" class="btn-apply">Apply</button>
                    <a href="Headviewstudent.php" style="padding: 8px 15px; background: #e2e8f0; color: #333; text-decoration: none; border-radius: 4px;">Reset</a>
                </div>
            </form>
        </div>

        <!-- Students Table -->
        <?php if (empty($students)): ?>
            <div class="empty-state" style="background: #fff; padding: 40px; text-align: center; border-radius: 8px;">
                <h3>No students found</h3>
                <p>There are no students matching your criteria in this department.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper" style="overflow-x: auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th style="padding: 12px 15px; color: #475569;">Student ID</th>
                            <th style="padding: 12px 15px; color: #475569;">Full Name</th>
                            <th style="padding: 12px 15px; color: #475569;">Gender</th>
                            <th style="padding: 12px 15px; color: #475569;">Year</th>
                            <th style="padding: 12px 15px; color: #475569;">Semester</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $s): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 15px; font-weight: bold;"><?= htmlspecialchars($s['student_id']) ?></td>
                                <td style="padding: 12px 15px;"><?= htmlspecialchars($s['firstname'] . ' ' . $s['father_name']) ?></td>
                                <td style="padding: 12px 15px;"><?= htmlspecialchars($s['gender'] ?? 'N/A') ?></td>
                                <td style="padding: 12px 15px;">Year <?= htmlspecialchars($s['year'] ?? 'N/A') ?></td>
                                <td style="padding: 12px 15px;">Sem <?= htmlspecialchars($s['current_semester'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>