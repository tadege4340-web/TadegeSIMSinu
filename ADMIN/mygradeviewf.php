<?php
if (!defined('FROM_MYGRADEVIEW')) {
    die('Please open <a href="mygradeview.php">mygradeview.php</a> instead.');
}
?>

<!-- ============ LINK STYLESHEET ============ -->
<link rel="stylesheet" href="mygradeview.css">

<!-- ============ HEADER ============ -->
<div class="grades-header">
    <h2>📊 All Student Grades</h2>
    <p>View and manage grades for every student across all colleges</p>
</div>

<!-- ============ FILTERS ============ -->
<form method="GET" class="grades-filters">

    <div class="filter-item">
        <label>🔍 Search</label>
        <input type="text" name="q" placeholder="Student ID or name..."
               value="<?= htmlspecialchars($search) ?>">
    </div>

    <div class="filter-item">
        <label>🏛️ College</label>
        <select name="college">
            <option value="">All Colleges</option>
            <?php
            // FIXED: Changed mysqli to PDO
            try {
                $r = $conn->query("SELECT code, name FROM college ORDER BY id");
                while ($c = $r->fetch(PDO::FETCH_ASSOC)):
            ?>
                <option value="<?= htmlspecialchars($c['code']) ?>"
                    <?= $filter_college === $c['code'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php 
                endwhile; 
            } catch (PDOException $e) {
                // Handle error silently
            }
            ?>
        </select>
    </div>

    <div class="filter-item">
        <label>📅 Academic Year</label>
        <select name="ac_year">
            <option value="">All Years</option>
            <option value="2024/2025" <?= $filter_year === '2024/2025' ? 'selected' : '' ?>>2024/2025</option>
            <option value="2025/2026" <?= $filter_year === '2025/2026' ? 'selected' : '' ?>>2025/2026</option>
            <option value="2026/2027" <?= $filter_year === '2026/2027' ? 'selected' : '' ?>>2026/2027</option>
        </select>
    </div>

    <div class="filter-buttons">
        <button type="submit" class="btn-apply">🔍 Apply</button>
        <a href="mygradeview.php" class="btn-clear">✖ Clear</a>
    </div>

</form>

<!-- ============ SUMMARY ============ -->
<div class="grades-summary">
    <span>📋 <strong><?= number_format($total_rows) ?></strong> grade records found</span>
    <span>Filter: <?= $filter_college ?: 'All colleges' ?> · <?= $filter_year ?: 'All years' ?></span>
</div>

<!-- ============ TABLE ============ -->
<div class="grades-table-wrap">
    <table class="grades-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>College</th>
                <th>Department</th>
                <th>Course</th>
                <th>Course Name</th>
                <th>Credit</th>
                <th>Year</th>
                <th>Sem</th>
                <th>Grade</th>
                <th>Grade Point</th>
                <th>SGPA</th>
                <th>CGPA</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($grades)): ?>
                <tr>
                    <td colspan="16" class="no-data">
                        No grade records found for this filter.
                    </td>
                </tr>
            <?php else: ?>
                <?php $i = 1; foreach ($grades as $g): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td class="student-id"><?= htmlspecialchars($g['student_id']) ?></td>
                        <td><?= htmlspecialchars(trim(($g['firstname'] ?? '') . ' ' . ($g['father_name'] ?? '')) ?: '—') ?></td>
                        <td><?= htmlspecialchars($g['college'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($g['department'] ?? '—') ?></td>
                        <td class="course-code"><?= htmlspecialchars($g['course_code']) ?></td>
                        <td><?= htmlspecialchars($g['course_name'] ?? '—') ?></td>
                        <td style="text-align:center;"><?= htmlspecialchars($g['credit_hour'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($g['ac_year'] ?? '—') ?></td>
                        <td style="text-align:center;"><?= htmlspecialchars($g['semester'] ?? '—') ?></td>
                        <td class="grade-cell"><?= htmlspecialchars($g['grade'] ?? '—') ?></td>
                        <td class="gp-cell"><?= $g['grade_point'] !== null ? number_format($g['grade_point'], 2) : '—' ?></td>
                        <td class="sgpa-cell"><?= number_format($g['sgpa'] ?? 0, 2) ?></td>
                        <td class="cgpa-cell"><?= number_format($g['cgpa'] ?? 0, 2) ?></td>
                        <td>
                            <?php if ($g['status'] === 'Pass'): ?>
                                <span class="badge-pass">Pass</span>
                            <?php elseif ($g['status'] === 'Fail'): ?>
                                <span class="badge-fail">Fail</span>
                            <?php else: ?>
                                <span class="badge-pending">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="action-cell">
                            <a href="mygrade.php?student=<?= urlencode($g['student_id']) ?>&course=<?= urlencode($g['course_code']) ?>&year=<?= urlencode($g['ac_year']) ?>&sem=<?= urlencode($g['semester']) ?>"
                               class="btn-edit-grade">✏️ Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>