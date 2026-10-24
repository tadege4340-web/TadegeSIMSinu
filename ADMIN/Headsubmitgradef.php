<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Head.css">
    <style>
        body { background-color: #f4f6f9; padding: 20px; }
        .filter-panel { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; }
        .filter-item { display: flex; flex-direction: column; gap: 5px; }
        .filter-item label { font-size: 13px; font-weight: bold; color: #475569; }
        .filter-item select { padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; }
        .btn-clear { background: #e2e8f0; color: #333; text-align: center; padding: 10px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 14px; display: flex; align-items: center; justify-content: center; }
        .pending-table-wrap { overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 8px; background: #fff; margin-top: 15px; }
        .pending-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .pending-table th { background: #f8fafc; padding: 12px 15px; text-align: left; border-bottom: 2px solid #cbd5e1; color: #475569; font-weight: bold; }
        .pending-table td { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .pending-table tr:hover { background: #f8fafc; }
        .center { text-align: center; }
        .missing-cell { color: #b91c1c; font-weight: bold; background: #fee2e2; border-radius: 4px; padding: 2px 8px; }
        .graded-cell { color: #166534; font-weight: bold; }
        .btn-submit-grades { background: #1e4e79; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 13px; transition: 0.2s; }
        .btn-submit-grades:hover { background: #163a5a; }
        .btn-submit-partial { background: #d97706; }
        .btn-submit-partial:hover { background: #b45309; }
        .submit-info-box { background: #e0f2fe; border: 1px solid #bae6fd; padding: 20px; border-radius: 8px; margin-top: 20px; color: #0369a1; }
        .submit-info-box ul { margin: 10px 0 0 20px; font-size: 14px; line-height: 1.6; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    </style>
</head>
<body>

    <div class="page-container">
        <!-- HEADER -->
        <div class="page-header" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <div class="header-text">
                <h2>📤 Submit Grades for Approval</h2>
                <p>Department: <strong><?= htmlspecialchars($hod_department) ?></strong></p>
            </div>
            <div class="total-badge" style="background: #1e4e79; color: white; padding: 10px 15px; border-radius: 5px; font-weight: bold;">
                Pending Groups: <?= count($pendingGroups) ?>
            </div>
        </div>

        <!-- ALERTS -->
        <?php if ($message !== ''): ?>
            <?php $alert_class = $message_type == 'error' ? 'alert-error' : 'alert-success'; ?>
            <div class="alert <?= $alert_class ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- STEP 1: FILTERS -->
        <div class="filter-panel">
            <h3 class="panel-title" style="margin-bottom: 15px; color: #17365d;">Step 1 — Filter Pending Grades</h3>
            <form method="GET" action="Headsubmitgrade.php" class="filter-form">
                <div class="filter-grid">
                    
                    <div class="filter-item">
                        <label>📅 Year of Study</label>
                        <select name="year" onchange="this.form.submit()">
                            <option value="">-- All Years --</option>
                            <?php for ($y = 1; $y <= 7; $y++): ?>
                                <option value="<?= $y ?>" <?= $f_year == $y ? 'selected' : '' ?>>Year <?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="filter-item">
                        <label>📖 Semester</label>
                        <select name="semester" onchange="this.form.submit()">
                            <option value="">-- All Semesters --</option>
                            <option value="1" <?= $f_semester == 1 ? 'selected' : '' ?>>Semester 1</option>
                            <option value="2" <?= $f_semester == 2 ? 'selected' : '' ?>>Semester 2</option>
                        </select>
                    </div>

                    <div class="filter-item">
                        <label>&nbsp;</label>
                        <a href="Headsubmitgrade.php" class="btn-clear">✖ Clear Filters</a>
                    </div>

                </div>
            </form>
        </div>

        <!-- STEP 2: PENDING GRADES TABLE -->
        <div class="filter-panel">
            <h3 class="panel-title" style="margin-bottom: 15px; color: #17365d;">
                📋 Pending Grades Ready to Submit
            </h3>

            <?php if (empty($pendingGroups)): ?>
                <div class="empty-state" style="text-align: center; padding: 30px;">
                    <div class="es-icon">✅</div>
                    <h3>No pending grades</h3>
                    <p>There are no pending grades for this department matching your filters.</p>
                </div>
            <?php else: ?>

                <div class="pending-table-wrap">
                    <table class="pending-table">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Course Name</th>
                                <th>Year</th>
                                <th>Academic Year</th>
                                <th>Semester</th>
                                <th>Total</th>
                                <th>Graded</th>
                                <th>Missing</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingGroups as $g): ?>
                                <?php $readyToSubmit = ($g['missing'] == 0); ?>
                                <tr>
                                    <td class="course-code-cell" style="font-weight: bold; color: #1e4e79;"><?= htmlspecialchars($g['course_code']) ?></td>
                                    <td><?= htmlspecialchars($g['course_name'] ?? 'N/A') ?></td>
                                    <td class="center">Year <?= htmlspecialchars($g['course_year'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($g['ac_year']) ?></td>
                                    <td class="center">Sem <?= htmlspecialchars($g['semester']) ?></td>
                                    <td class="center"><?= $g['total'] ?></td>
                                    <td class="center graded-cell"><?= $g['graded'] ?></td>
                                    <td class="center">
                                        <span class="<?= $g['missing'] > 0 ? 'missing-cell' : '' ?>">
                                            <?= $g['missing'] ?>
                                        </span>
                                    </td>
                                    <td class="center">
                                        <?php if ($readyToSubmit): ?>
                                            <form method="POST" action="Headsubmitgrade.php" style="display:inline;" onsubmit="return confirm('Submit all <?= $g['total'] ?> grades for <?= htmlspecialchars($g['course_code']) ?>?');">
                                                <input type="hidden" name="action" value="submit">
                                                <input type="hidden" name="course_code" value="<?= htmlspecialchars($g['course_code']) ?>">
                                                <input type="hidden" name="ac_year" value="<?= htmlspecialchars($g['ac_year']) ?>">
                                                <input type="hidden" name="semester" value="<?= htmlspecialchars($g['semester']) ?>">
                                                <button type="submit" class="btn-submit-grades">📤 Submit</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" action="Headsubmitgrade.php" style="display:inline;" onsubmit="return confirm('<?= $g['missing'] ?> grades are still missing!\n\nSubmit only the <?= $g['graded'] ?> graded ones?');">
                                                <input type="hidden" name="action" value="submit_partial">
                                                <input type="hidden" name="course_code" value="<?= htmlspecialchars($g['course_code']) ?>">
                                                <input type="hidden" name="ac_year" value="<?= htmlspecialchars($g['ac_year']) ?>">
                                                <input type="hidden" name="semester" value="<?= htmlspecialchars($g['semester']) ?>">
                                                <button type="submit" class="btn-submit-grades btn-submit-partial">⚠️ Submit <?= $g['graded'] ?>/<?= $g['total'] ?></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- INFO BOX -->
        <div class="submit-info-box">
            <h4>ℹ️ How Submission Works</h4>
            <ul>
                <li>This page automatically only shows grades for the <strong><?= htmlspecialchars($hod_department) ?></strong> department.</li>
                <li>Select <strong>Year</strong> and <strong>Semester</strong> to filter pending grades.</li>
                <li>Click <strong>📤 Submit</strong> if all grades are entered.</li>
                <li>Click <strong>⚠️ Submit X/Y</strong> if some are missing.</li>
            </ul>
        </div>
    </div>

</body>
</html>