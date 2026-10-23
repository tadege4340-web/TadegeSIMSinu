<!-- mygradeapprovef.php -->

<div class="container mt-4">

    <!-- HEADER -->
    <div class="approve-header">
        <h2>📋 Grade Approvals — Registrar</h2>
        <p>Review, approve, or return submitted grades from teachers</p>
    </div>

    <!-- MESSAGE -->
    <?php if ($message): ?>
        <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- FILTERS -->
    <form method="get" class="approve-filters">
        <select name="college" onchange="this.form.submit()">
            <option value="">All Colleges</option>
            <?php foreach ($colleges as $c): ?>
                <option value="<?= htmlspecialchars($c['code']) ?>"
                    <?= $f_college === $c['code'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="department" onchange="this.form.submit()">
            <option value="">All Departments</option>
            <?php foreach ($departments as $d): ?>
                <option value="<?= htmlspecialchars($d) ?>"
                    <?= $f_department === $d ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="number" name="year" placeholder="Year"
               value="<?= $f_year ?: '' ?>">

        <button type="submit" class="btn-filter">Filter</button>
    </form>

    <!-- TABLE OR EMPTY STATE -->
    <?php if (empty($submittedGroups)): ?>

        <div class="empty-state">
            <div class="es-icon">📭</div>
            <h3>No submitted grades awaiting approval</h3>
            <p>When teachers submit grades, they will appear here.</p>
        </div>

    <?php else: ?>

        <div class="approve-table-wrap">
            <table class="approve-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Sem</th>
                        <th>Dept</th>
                        <th>Total</th>
                        <th>Graded</th>
                        <th>Submitted By</th>
                        <th>Submitted At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submittedGroups as $g): ?>
                    <tr>
                        <td>
                            <span class="course-code-cell">
                                <?= htmlspecialchars($g['course_code']) ?>
                            </span><br>
                            <small><?= htmlspecialchars($g['course_name']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($g['ac_year']) ?></td>
                        <td class="center"><?= htmlspecialchars($g['semester']) ?></td>
                        <td><?= htmlspecialchars($g['department'] ?? '-') ?></td>
                        <td class="center"><?= (int)$g['total'] ?></td>
                        <td class="center"><?= (int)$g['graded'] ?></td>
                        <td>
                            <span class="submitted-by-badge">
                                <?= htmlspecialchars($g['submitted_by'] ?? '-') ?>
                            </span>
                        </td>
                        <td class="date-cell">
                            <?= htmlspecialchars($g['submitted_at'] ?? '-') ?>
                        </td>

                        <!-- ACTION COLUMN — Approve + Reject side-by-side -->
                        <td class="action-col">
                            <form method="post" class="action-form">
                                <input type="hidden" name="course_code"
                                       value="<?= htmlspecialchars($g['course_code']) ?>">
                                <input type="hidden" name="ac_year"
                                       value="<?= htmlspecialchars($g['ac_year']) ?>">
                                <input type="hidden" name="semester"
                                       value="<?= (int)$g['semester'] ?>">
                                <input type="hidden" name="reason"
                                       value="" id="reason_<?= md5($g['course_code'].$g['ac_year'].$g['semester']) ?>">

                                <button type="submit" name="action" value="approve"
                                        class="btn-approve"
                                        onclick="return confirm('Approve all grades for this course?')">
                                    ✅ Approve
                                </button>

                                <button type="button"
                                        class="btn-reject"
                                        onclick="rejectWithReason(
                                            '<?= htmlspecialchars($g['course_code'], ENT_QUOTES) ?>',
                                            '<?= htmlspecialchars($g['ac_year'], ENT_QUOTES) ?>',
                                            '<?= (int)$g['semester'] ?>'
                                        )">
                                    ↩️ Reject
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>
</div>

<!-- ============================================================
     REJECT REASON MODAL
     ============================================================ -->
<div id="rejectModal" class="reject-modal-overlay">
    <div class="reject-modal-box">
        <h3>↩️ Return Grades to Teacher</h3>
        <p id="rejectCourseInfo" class="reject-course-info"></p>

        <form method="post" id="rejectForm">
            <input type="hidden" name="action" value="reject">
            <input type="hidden" name="course_code" id="rej_course_code">
            <input type="hidden" name="ac_year"     id="rej_ac_year">
            <input type="hidden" name="semester"    id="rej_semester">

            <label for="rej_reason"><strong>Reason for rejection:</strong></label>
            <textarea name="reason" id="rej_reason" required
                      placeholder="Explain why these grades are being returned..."></textarea>

            <div class="reject-modal-actions">
                <button type="button" class="btn-cancel"
                        onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="btn-confirm-reject">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     MODAL SCRIPT
     ============================================================ -->
<script>
function rejectWithReason(courseCode, acYear, semester) {
    document.getElementById('rej_course_code').value = courseCode;
    document.getElementById('rej_ac_year').value     = acYear;
    document.getElementById('rej_semester').value    = semester;
    document.getElementById('rejectCourseInfo').textContent =
        courseCode + ' — ' + acYear + ' — Semester ' + semester;
    document.getElementById('rejectModal').classList.add('active');
    setTimeout(() => document.getElementById('rej_reason').focus(), 100);
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('active');
    document.getElementById('rej_reason').value = '';
}

// Close modal when clicking the dark overlay
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeRejectModal();
});
</script>