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
            <div class="total-badge">
                Records Found: <?= count($students_grades) ?>
            </div>
        </div>

        <!-- Search & Filter Form -->
        <div class="filter-bar" style="margin-bottom: 20px;">
            <form method="GET" action="Headviewgrade.php" class="filter-form" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                
                <div class="filter-group" style="flex: 2; min-width: 200px;">
                    <label>Student ID (Optional)</label>
                    <input type="text" name="student_id" value="<?= htmlspecialchars($student_id) ?>" placeholder="Leave blank to see all">
                </div>

                <div class="filter-group" style="flex: 1; min-width: 150px;">
                    <label>Year</label>
                    <select name="year">
                        <option value="">All Years</option>
                        <?php for ($i = 1; $i <= 7; $i++): ?>
                            <option value="<?= $i ?>" <?= $filter_year == $i ? 'selected' : '' ?>>Year <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="filter-group" style="flex: 1; min-width: 150px;">
                    <label>Semester</label>
                    <select name="semester">
                        <option value="">All Semesters</option>
                        <option value="1" <?= $filter_semester == 1 ? 'selected' : '' ?>>Semester 1</option>
                        <option value="2" <?= $filter_semester == 2 ? 'selected' : '' ?>>Semester 2</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-apply">Search</button>
                    <a href="Headviewgrade.php" class="btn-reset">Reset</a>
                </div>
            </form>
        </div>

        <!-- Results Table -->
        <?php if (empty($students_grades)): ?>
            <div class="empty-state">
                <div class="es-icon">🔍</div>
                <h3>No Grades Found</h3>
                <p>Select a Year and Semester, or enter a Student ID to view grades.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credit Hr</th>
                            <th>Year</th>
                            <th>Sem</th>
                            <th>Grade</th>
                            <th>Grade Point</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students_grades as $g): ?>
                            <tr>
                                <td class="mono"><?= htmlspecialchars($g['student_id']) ?></td>
                                <td class="student-name"><?= htmlspecialchars($g['firstname'] . ' ' . $g['father_name']) ?></td>
                                <td><?= htmlspecialchars($g['course_code']) ?></td>
                                <td><?= htmlspecialchars($g['course_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($g['credit_hour'] ?? 'N/A') ?></td>
                                <td>Year <?= htmlspecialchars($g['course_year'] ?? 'N/A') ?></td>
                                <td>Sem <?= htmlspecialchars($g['semester'] ?? 'N/A') ?></td>
                                <td class="grade-<?= substr(htmlspecialchars($g['grade'] ?? 'N'), 0, 1) ?>">
                                    <?= htmlspecialchars($g['grade'] ?? 'N/A') ?>
                                </td>
                                <td><?= htmlspecialchars($g['grade_point'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($g['status'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>