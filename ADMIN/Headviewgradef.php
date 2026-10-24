<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Head.css">
    <style>
        body { background-color: #f4f6f9; padding: 20px; }
        .grade-A { color: #166534; font-weight: bold; }
        .grade-B { color: #1e40af; font-weight: bold; }
        .grade-C { color: #b45309; font-weight: bold; }
        .grade-D { color: #b91c1c; font-weight: bold; }
        .grade-F { color: #991b1b; font-weight: bold; }
    </style>
</head>
<body>
    <div class="page-container">
        
        <!-- Header -->
        <div class="page-header" style="margin-bottom: 20px;">
            <div class="header-text">
                <h2>📝 Student Grades</h2>
                <p>Department: <strong><?= htmlspecialchars($hod_department ?: 'All Departments (Admin)') ?></strong></p>
            </div>
            <div class="total-badge" style="background: #1e4e79; color: #fff; padding: 10px 15px; border-radius: 5px; font-weight: bold;">
                Records Found: <?= count($students_grades) ?>
            </div>
        </div>

        <!-- Search & Filter Form -->
        <div class="filter-bar" style="margin-bottom: 20px;">
            <form method="GET" action="Headviewgrade.php" class="filter-form" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                
                <div class="filter-group" style="flex: 2; min-width: 200px;">
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Student ID (Optional)</label>
                    <input type="text" name="student_id" value="<?= htmlspecialchars($student_id) ?>" placeholder="Leave blank to see all" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <div class="filter-group" style="flex: 1; min-width: 150px;">
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Year</label>
                    <select name="year" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="">All Years</option>
                        <?php for ($i = 1; $i <= 7; $i++): ?>
                            <option value="<?= $i ?>" <?= $filter_year == $i ? 'selected' : '' ?>>Year <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="filter-group" style="flex: 1; min-width: 150px;">
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Semester</label>
                    <select name="semester" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="">All Semesters</option>
                        <option value="1" <?= $filter_semester == 1 ? 'selected' : '' ?>>Semester 1</option>
                        <option value="2" <?= $filter_semester == 2 ? 'selected' : '' ?>>Semester 2</option>
                    </select>
                </div>

                <div class="filter-actions" style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-apply" style="background: #1e4e79; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Search</button>
                    <a href="Headviewgrade.php" style="background: #e2e8f0; color: #333; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-weight: bold;">Reset</a>
                </div>
            </form>
        </div>

        <!-- Results Table -->
        <?php if (empty($students_grades)): ?>
            <div class="empty-state" style="background: #fff; padding: 40px; text-align: center; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <div class="es-icon" style="font-size: 40px; margin-bottom: 10px;">🔍</div>
                <h3>No Grades Found</h3>
                <p>Select a Year and Semester, or enter a Student ID to view grades.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper" style="overflow-x: auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th style="padding: 12px 15px; color: #475569;">Student ID</th>
                            <th style="padding: 12px 15px; color: #475569;">Student Name</th>
                            <th style="padding: 12px 15px; color: #475569;">Course Code</th>
                            <th style="padding: 12px 15px; color: #475569;">Course Name</th>
                            <th style="padding: 12px 15px; color: #475569;">Credit Hr</th>
                            <th style="padding: 12px 15px; color: #475569;">Year</th>
                            <th style="padding: 12px 15px; color: #475569;">Sem</th>
                            <th style="padding: 12px 15px; color: #475569;">Grade</th>
                            <th style="padding: 12px 15px; color: #475569;">Grade Point</th>
                            <th style="padding: 12px 15px; color: #475569;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students_grades as $g): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 15px; font-weight: bold; color: #1e4e79;"><?= htmlspecialchars($g['student_id']) ?></td>
                                <td style="padding: 12px 15px;"><?= htmlspecialchars($g['firstname'] . ' ' . $g['father_name']) ?></td>
                                <td style="padding: 12px 15px;"><?= htmlspecialchars($g['course_code']) ?></td>
                                <td style="padding: 12px 15px;"><?= htmlspecialchars($g['course_name'] ?? 'N/A') ?></td>
                                <td style="padding: 12px 15px; text-align: center;"><?= htmlspecialchars($g['credit_hour'] ?? 'N/A') ?></td>
                                <td style="padding: 12px 15px;">Year <?= htmlspecialchars($g['course_year'] ?? 'N/A') ?></td>
                                <td style="padding: 12px 15px; text-align: center;">Sem <?= htmlspecialchars($g['semester'] ?? 'N/A') ?></td>
                                <td style="padding: 12px 15px;" class="grade-<?= substr(htmlspecialchars($g['grade'] ?? 'N'), 0, 1) ?>">
                                    <?= htmlspecialchars($g['grade'] ?? 'N/A') ?>
                                </td>
                                <td style="padding: 12px 15px;"><?= htmlspecialchars($g['grade_point'] ?? 'N/A') ?></td>
                                <td style="padding: 12px 15px;"><?= htmlspecialchars($g['status'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>