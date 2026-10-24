<!-- mygradeapprovef.php -->

<div class="container mt-4">

    <!-- HEADER -->
    <div class="approve-header">
        <h2>📋 Grade Approvals — Registrar</h2>
        <p>Review, approve, or return submitted grades from teachers</p>
    </div>

    <!-- MESSAGE -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'error' ?>" style="padding:15px; margin-bottom:20px; border-radius:8px; background:<?= $message_type === 'success' ? '#dcfce7' : '#fee2e2' ?>; color:<?= $message_type === 'success' ? '#166534' : '#991b1b' ?>;">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- FILTERS -->
    <form method="get" class="approve-filters" style="margin-bottom: 20px; display: flex; gap: 10px;">
        <select name="college" onchange="this.form.submit()" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <option value="">All Colleges</option>
            <?php foreach ($colleges as $c): ?>
                <option value="<?= htmlspecialchars($c['code']) ?>"
                    <?= $f_college === $c['code'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="department" onchange="this.form.submit()" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <option value="">All Departments</option>
            <?php foreach ($departments as $d): ?>
                <option value="<?= htmlspecialchars($d) ?>"
                    <?= $f_department === $d ? 'selected' : '' ?>>
                    <?= htmlspecialchars($d) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="number" name="year" placeholder="Year" value="<?= $f_year ?: '' ?>" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc; width: 80px;">

        <button type="submit" class="btn-filter" style="padding: 8px 15px; background: #1e4e79; color: white; border: none; border-radius: 5px; cursor: pointer;">Filter</button>
    </form>

    <!-- TABLE OR EMPTY STATE -->
    <?php if (empty($submittedGroups)): ?>

        <div class="empty-state" style="text-align: center; padding: 40px; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <div class="es-icon" style="font-size: 40px; margin-bottom: 10px;">📭</div>
            <h3>No submitted grades awaiting approval</h3>
            <p style="color: #64748b;">When teachers submit grades and Heads approve them, they will appear here.</p>
        </div>

    <?php else: ?>

        <div class="approve-table-wrap" style="overflow-x: auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <table class="approve-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th style="padding: 12px 15px; color: #475569;">Course</th>
                        <th style="padding: 12px 15px; color: #475569;">Year</th>
                        <th style="padding: 12px 15px; color: #475569;">Sem</th>
                        <th style="padding: 12px 15px; color: #475569;">Dept</th>
                        <th style="padding: 12px 15px; color: #475569;">Total</th>
                        <th style="padding: 12px 15px; color: #475569;">Graded</th>
                        <th style="padding: 12px 15px; color: #475569;">Submitted By</th>
                        <th style="padding: 12px 15px; color: #475569;">Submitted At</th>
                        <th style="padding: 12px 15px; color: #475569;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($submittedGroups as $g): ?>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px 15px;">
                            <span style="font-weight: bold; color: #1e4e79;">
                                <?= htmlspecialchars($g['course_code']) ?>
                            </span><br>
                            <small style="color: #64748b;"><?= htmlspecialchars($g['course_name']) ?></small>
                        </td>
                        <td style="padding: 12px 15px;"><?= htmlspecialchars($g['ac_year']) ?></td>
                        <td style="padding: 12px 15px; text-align: center;"><?= htmlspecialchars($g['semester']) ?></td>
                        <td style="padding: 12px 15px;"><?= htmlspecialchars($g['department'] ?? '-') ?></td>
                        <td style="padding: 12px 15px; text-align: center;"><?= (int)$g['total'] ?></td>
                        <td style="padding: 12px 15px; text-align: center;"><?= (int)$g['graded'] ?></td>
                        <td style="padding: 12px 15px;">
                            <span style="background: #e2e8f0; padding: 4px 8px; border-radius: 4px; font-size: 12px;">
                                <?= htmlspecialchars($g['submitted_by'] ?? '-') ?>
                            </span>
                        </td>
                        <td style="padding: 12px 15px; font-size: 13px; color: #64748b;">
                            <?= htmlspecialchars($g['submitted_at'] ?? '-') ?>
                        </td>

                        <!-- ACTION COLUMN — Approve + Reject side-by-side -->
                        <td style="padding: 12px 15px;">
                            <form method="post" style="display: flex; gap: 5px;">
                                <input type="hidden" name="course_code" value="<?= htmlspecialchars($g['course_code']) ?>">
                                <input type="hidden" name="ac_year" value="<?= htmlspecialchars($g['ac_year']) ?>">
                                <input type="hidden" name="semester" value="<?= (int)$g['semester'] ?>">
                                <input type="hidden" name="reason" value="" id="reason_<?= md5($g['course_code'].$g['ac_year'].$g['semester']) ?>">

                                <button type="submit" name="action" value="approve" style="background: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;"
                                        onclick="return confirm('Approve all grades for this course?')">
                                    ✅ Approve
                                </button>

                                <button type="button" style="background: #ef4444; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;"
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

<!-- REJECT REASON MODAL -->
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div style="background: #fff; padding: 20px; border-radius: 8px; width: 400px; max-width: 90%;">
        <h3 style="margin-top: 0;">↩️ Return Grades to Teacher</h3>
        <p id="rejectCourseInfo" style="font-weight: bold; color: #1e4e79;"></p>

        <form method="post" id="rejectForm">
            <input type="hidden" name="action" value="reject">
            <input type="hidden" name="course_code" id="rej_course_code">
            <input type="hidden" name="ac_year"     id="rej_ac_year">
            <input type="hidden" name="semester"    id="rej_semester">

            <label for="rej_reason"><strong>Reason for rejection:</strong></label>
            <textarea name="reason" id="rej_reason" required placeholder="Explain why these grades are being returned..." style="width: 100%; height: 80px; margin-top: 5px; padding: 8px; border-radius: 4px; border: 1px solid #ccc;"></textarea>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px;">
                <button type="button" onclick="closeRejectModal()" style="padding: 8px 15px; background: #e2e8f0; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 8px 15px; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function rejectWithReason(courseCode, acYear, semester) {
    document.getElementById('rej_course_code').value = courseCode;
    document.getElementById('rej_ac_year').value     = acYear;
    document.getElementById('rej_semester').value    = semester;
    document.getElementById('rejectCourseInfo').textContent = courseCode + ' — ' + acYear + ' — Semester ' + semester;
    document.getElementById('rejectModal').style.display = 'flex';
    setTimeout(() => document.getElementById('rej_reason').focus(), 100);
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
    document.getElementById('rej_reason').value = '';
}
</script>