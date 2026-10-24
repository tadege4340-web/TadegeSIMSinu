<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View My Students</title>
    
    <link rel="stylesheet" href="Teacherdashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="iframe-body">

<div class="students-page">

    <!-- HEADER -->
    <div class="students-header">
        <h2>My Assigned Students</h2>
        
        <!-- Inform teacher they are only seeing their course students -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'teacher'): ?>
            <div class="teacher-dept-notice" style="background-color: #e0f2fe; color: #0369a1; padding: 10px; border-radius: 6px; margin-top: 10px; border-left: 4px solid #0284c7;">
                <i class="fas fa-info-circle"></i> You are only viewing students who are registered for the courses assigned to you in the <strong><?= htmlspecialchars($teacher_department) ?></strong> department.
            </div>
        <?php endif; ?>
    </div>

    <br>

    <!-- FILTER FORM -->
    <form method="get" class="students-filter-bar">

        <!-- YEAR -->
        <div class="filter-group">
            <label for="year">Year</label>
            <select name="year" id="year" onchange="this.form.submit()">
                <option value="">All Years</option>
                <?php for ($y = 1; $y <= 7; $y++): ?>
                    <option value="<?= $y ?>" <?= ($f_year === $y) ? 'selected' : '' ?>>Year <?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <!-- SEMESTER -->
        <div class="filter-group">
            <label for="semester">Semester</label>
            <select name="semester" id="semester" onchange="this.form.submit()">
                <option value="">All Semesters</option>
                <option value="1" <?= ($f_semester === 1) ? 'selected' : '' ?>>Semester 1</option>
                <option value="2" <?= ($f_semester === 2) ? 'selected' : '' ?>>Semester 2</option>
            </select>
        </div>

        <!-- SEARCH -->
        <div class="filter-group">
            <label for="search">Search</label>
            <input type="text" name="search" id="search" placeholder="ID or name…" value="<?= htmlspecialchars($f_search) ?>">
        </div>

        <!-- ACTION BUTTONS -->
        <div class="filter-actions">
            <button type="submit" class="btn-apply">Apply</button>
            <a href="Teacherviewstudent.php" class="btn-reset">Reset</a>
        </div>
    </form>

    <!-- ACTIVE FILTER SUMMARY -->
    <?php if ($f_year || $f_semester || $f_search): ?>
        <div class="filter-summary">
            <strong>Showing:</strong>
            <?php if ($f_year): ?>
                <span class="chip">Year <?= (int)$f_year ?></span>
            <?php endif; ?>
            <?php if ($f_semester): ?>
                <span class="chip">Semester <?= (int)$f_semester ?></span>
            <?php endif; ?>
            <?php if ($f_search): ?>
                <span class="chip">"<?= htmlspecialchars($f_search) ?>"</span>
            <?php endif; ?>
            <span class="count-badge"><?= $totalFiltered ?> of <?= $totalAll ?></span>
        </div>
    <?php endif; ?>

    <!-- RESULTS TABLE -->
    <div class="students-table-wrap">
        <table class="students-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student ID</th>
                    <th>First Name</th>
                    <th>Father Name</th>
                    <th>Grandfather Name</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Year</th>
                    <th>Semester</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)): ?>
                    <?php $count = 1; ?>
                    <?php foreach ($students as $row): ?>
                        <tr>
                            <td class="center"><?= $count++ ?></td>
                            <td class="mono"><?= htmlspecialchars($row['student_id']) ?></td>
                            <td><?= htmlspecialchars($row['firstname']) ?></td>
                            <td><?= htmlspecialchars($row['father_name']) ?></td>
                            <td><?= htmlspecialchars($row['grandfather_name']) ?></td>
                            <td class="center"><?= htmlspecialchars($row['gender'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($row['phone'] ?? '—') ?></td>
                            <td class="center">Year <?= htmlspecialchars($row['year'] ?? '—') ?></td>
                            <td class="center">Sem <?= htmlspecialchars($row['current_semester'] ?? '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="no-data">
                            <div class="empty-state">
                                <div class="es-icon">🔍</div>
                                <h3>No assigned students found</h3>
                                <p>You currently have no students registered for your assigned courses.</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>