<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Teacherdashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="iframe-body">

<div class="grade-page-container">
    
    <div class="students-header" style="margin-bottom: 20px;">
        <h2>📊 View Student Grades</h2>
        <p style="color: #64748b;">Select a course to view the submitted grades.</p>
    </div>

    <!-- FILTER FORM -->
    <div class="grade-filter-panel">
        <form method="GET" action="Teacherviewgrade.php" class="grade-filter-form">
            <div class="grade-filter-group" style="flex: 2;">
                <label>Select Assigned Course</label>
                <select name="course_id" required>
                    <option value="">-- Choose Course --</option>
                    <?php foreach ($my_courses as $c): ?>
                        <option value="<?= $c['course_id'] ?>" <?= $f_course_id == $c['course_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['course_code'] . ' - ' . $c['course_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-load-students">Load Grades</button>
        </form>
    </div>

    <!-- GRADES TABLE -->
    <?php if ($f_course_id > 0): ?>
        
        <div class="grade-table-wrap">
            <table class="grade-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Academic Year</th>
                        <th>Sem</th>
                        <th>Grade</th>
                        <th>Grade Point</th>
                        <th>Status</th>
                        <th>Grade Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($grades_list)): ?>
                        <tr><td colspan="9" style="text-align:center; padding: 30px;">No students have registered for this course yet.</td></tr>
                    <?php else: ?>
                        <?php $count = 1; foreach ($grades_list as $g): ?>
                            <tr>
                                <td><?= $count++ ?></td>
                                <td style="font-weight: bold;"><?= htmlspecialchars($g['student_id']) ?></td>
                                <td><?= htmlspecialchars($g['firstname'] . ' ' . $g['father_name']) ?></td>
                                <td><?= htmlspecialchars($g['ac_year'] ?? '—') ?></td>
                                <td style="text-align:center;"><?= htmlspecialchars($g['semester'] ?? '—') ?></td>
                                <td style="font-weight: bold; color: #1e4e79;"><?= htmlspecialchars($g['grade'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($g['grade_point'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($g['status'] ?? '—') ?></td>
                                <td>
                                    <span class="status-badge bg-<?= strtolower($g['grade_status'] ?? 'pending') ?>">
                                        <?= htmlspecialchars($g['grade_status'] ?? 'Pending') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="empty-state" style="background: #fff; padding: 40px; border-radius: 8px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <div class="es-icon" style="font-size: 40px; margin-bottom: 10px;">👆</div>
            <h3>Select a Course</h3>
            <p>Choose one of your assigned courses above to view student grades.</p>
        </div>
    <?php endif; ?>

</div>

</body>
</html>