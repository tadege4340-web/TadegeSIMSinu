<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Head.css">
    <style>
        body { background-color: #f4f6f9; padding: 20px; }
        .filter-top { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .assign-container { display: flex; gap: 20px; margin-top: 20px; align-items: stretch; }
        .list-box { flex: 1; background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; flex-direction: column; }
        .list-box h4 { margin-bottom: 10px; color: #17365d; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; display: flex; justify-content: space-between;}
        .list-box select { width: 100%; height: 300px; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; font-size: 14px; flex-grow: 1;}
        .list-box select option { padding: 8px; border-bottom: 1px solid #f1f5f9; }
        .btn-filter { background: #1e4e79; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; height: 42px;}
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-warning { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="page-header">
            <div class="header-text">
                <h2>👨‍🏫 Bulk Assign Courses to Teachers</h2>
                <p>Department: <strong><?= htmlspecialchars($hod_department) ?></strong></p>
            </div>
        </div>

        <?php if ($message !== ''): ?>
            <?php $alert_class = $message_type == 'error' ? 'alert-error' : ($message_type == 'warning' ? 'alert-warning' : 'alert-success'); ?>
            <div class="alert <?= $alert_class ?>"><?= $message ?></div>
        <?php endif; ?>

        <div class="filter-top">
            <form method="GET" action="Headcourseassignteacher.php" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                <div class="filter-group" style="flex: 1; min-width: 200px;">
                    <label style="font-weight: bold; margin-bottom: 5px; display: block;">Year</label>
                    <select name="year" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        <option value="">-- Choose Year --</option>
                        <?php for ($i = 1; $i <= 7; $i++): ?>
                            <option value="<?= $i ?>" <?= $filter_year == $i ? 'selected' : '' ?>>Year <?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="filter-group" style="flex: 1; min-width: 200px;">
                    <label style="font-weight: bold; margin-bottom: 5px; display: block;">Semester</label>
                    <select name="semester" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        <option value="">-- Choose Semester --</option>
                        <option value="1" <?= $filter_semester == '1' ? 'selected' : '' ?>>Semester 1</option>
                        <option value="2" <?= $filter_semester == '2' ? 'selected' : '' ?>>Semester 2</option>
                    </select>
                </div>
                <button type="submit" class="btn-filter">Load Data</button>
            </form>
        </div>

        <?php if ($filter_year !== '' && $filter_semester !== ''): ?>
            <form method="POST" action="Headcourseassignteacher.php">
                <div class="assign-container">
                    <div class="list-box">
                        <h4>👨‍🏫 Teachers <span>(<?= count($teachers) ?>)</span></h4>
                        <select multiple id="teacherList" name="teacher_ids[]">
                            <?php foreach ($teachers as $t): ?>
                                <!-- IMPORTANT: VALUE MUST BE user_id -->
                                <option value="<?= $t['user_id'] ?>" selected>
                                    <?= htmlspecialchars($t['full_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="list-box">
                        <h4>📚 Courses <span>(<?= count($courses) ?>)</span></h4>
                        <select multiple id="courseList" name="course_ids[]">
                            <?php foreach ($courses as $c): ?>
                                <!-- IMPORTANT: VALUE MUST BE course_id -->
                                <option value="<?= $c['course_id'] ?>">
                                    <?= htmlspecialchars($c['course_code'] . ' - ' . $c['course_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 25px;">
                    <button type="submit" name="assign" class="btn-apply" style="padding: 15px 50px; font-size: 18px;">🚀 Assign Courses</button>
                </div>
            </form>
        <?php else: ?>
            <div class="empty-state">
                <div class="es-icon">👆</div>
                <h3>Select Year and Semester</h3>
                <p>Choose both filters above to load teachers and courses.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>