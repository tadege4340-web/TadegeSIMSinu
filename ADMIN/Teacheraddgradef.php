<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Teacherdashboard.css">
</head>
<body class="iframe-body">

<div class="grade-page-container">
    
    <div class="students-header" style="margin-bottom: 20px;">
        <h2>📝 Add Grades</h2>
        <p style="color: #64748b;">Select a course to enter student grades.</p>
    </div>

    <?php if ($message !== ''): ?>
        <div class="alert alert-<?= $message_type ?>" style="padding:15px; border-radius:8px; margin-bottom:20px; font-weight:bold;">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- FILTER FORM -->
    <div class="grade-filter-panel">
        <form method="GET" action="Teacheraddgrade.php" class="grade-filter-form">
            
            <div class="grade-filter-group" style="flex: 2;">
                <label>Select Assigned Course</label>
                <select name="course_id" required>
                    <option value="">-- Choose Course --</option>
                    <?php foreach ($my_courses as $c): ?>
                        <option value="<?= $c['course_id'] ?>" <?= $f_course_id == $c['course_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['course_code'] . ' - ' . $c['course_name'] . ' (Yr ' . $c['course_year'] . ' Sem ' . $c['course_semester'] . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-load-students">Load Students</button>
        </form>
    </div>

    <!-- STUDENTS TABLE -->
    <?php if ($f_course_id > 0): ?>
        
        <form method="POST" action="Teacheraddgrade.php">
            <input type="hidden" name="action" value="save_grades">
            <input type="hidden" name="semester" value="1"> <!-- Dummy value -->
            <?php 
                $selected_code = '';
                foreach($my_courses as $mc) { if($mc['course_id'] == $f_course_id) $selected_code = $mc['course_code']; }
            ?>
            <input type="hidden" name="course_code" value="<?= htmlspecialchars($selected_code) ?>">

            <div class="grade-table-wrap">
                <table class="grade-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Grade</th>
                            <th>Grade Point</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students_with_grades)): ?>
                            <tr><td colspan="6" style="text-align:center; padding: 30px;">No students found for this course.</td></tr>
                        <?php else: ?>
                            <?php $count = 1; foreach ($students_with_grades as $s): ?>
                                <tr>
                                    <td><?= $count++ ?></td>
                                    <td style="font-weight: bold;"><?= htmlspecialchars($s['student_id']) ?></td>
                                    <td><?= htmlspecialchars($s['firstname'] . ' ' . $s['father_name']) ?></td>
                                    <td>
                                        <!-- 🔥 REMOVED DISABLED ATTRIBUTE SO YOU CAN ALWAYS TYPE -->
                                        <input type="text" name="grades[<?= $s['student_id'] ?>][grade]" 
                                               class="grade-input" value="<?= htmlspecialchars($s['grade'] ?? '') ?>"
                                               placeholder="A" maxlength="2" 
                                               oninput="calculateGP(this)">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="grades[<?= $s['student_id'] ?>][gp]" 
                                               class="gp-input" value="<?= htmlspecialchars($s['grade_point'] ?? '') ?>"
                                               placeholder="4.0">
                                    </td>
                                    <td>
                                        <span class="status-badge bg-<?= strtolower($s['grade_status'] ?? 'pending') ?>">
                                            <?= htmlspecialchars($s['grade_status'] ?? 'Pending') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($students_with_grades)): ?>
                <button type="submit" class="btn-save-grades" onclick="return confirm('Save these grades? This will send them back to Pending for Head approval.');">💾 Save Grades</button>
            <?php endif; ?>
        </form>

    <?php else: ?>
        <div class="empty-state" style="background: #fff; padding: 40px; border-radius: 8px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <div class="es-icon" style="font-size: 40px; margin-bottom: 10px;">👆</div>
            <h3>Select a Course</h3>
            <p>Choose a course above to load all registered students.</p>
        </div>
    <?php endif; ?>

</div>

<!-- JAVASCRIPT AUTO-CALCULATION -->
<script>
    function calculateGP(inputElement) {
        let grade = inputElement.value.toUpperCase().trim();
        let row = inputElement.closest('tr');
        let gpInput = row.querySelector('.gp-input');

        let gradePoints = {
            'A+': 4.00, 'A': 4.00, 'A-': 3.75, 'B+': 3.50, 'B': 3.00, 'B-': 2.75,
            'C+': 2.50, 'C': 2.00, 'C-': 1.75, 'D': 1.00, 'F': 0.00
        };

        if (gradePoints.hasOwnProperty(grade)) {
            gpInput.value = gradePoints[grade].toFixed(2);
        } else if (grade === '') {
            gpInput.value = ''; 
        }
    }
</script>

</body>
</html>