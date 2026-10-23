<!-- ============ REGISTRAR HEADER ============ -->
<div class="registrar-header">
    <h2>Welcome — Changer Grade</h2>
    
</div>

<!-- ============ ALERTS ============ -->
<?php if ($message !== ''): ?>
    <?php
    $safe_message = strip_tags($message, '<b><br><strong><small><em><u><i><span>');
    ?>
    <div class="alert alert-<?= htmlspecialchars($message_type) ?>">
        <?= $safe_message ?>
    </div>
<?php endif; ?>

<!-- ============ FORM ============ -->
<form method="POST" class="grade-change-form">

    <div class="section-title"></div>

    <div class="form-row">
        <label for="student_id">Student ID <span class="req">*</span></label>
        <input type="text" id="student_id" name="student_id"
               value="<?= htmlspecialchars($_POST['student_id'] ?? '') ?>"
               placeholder="e.g. 0936" required>
    </div>

    <div class="form-row">
        <label for="course_code">Course Code <span class="req">*</span></label>
        <input type="text" id="course_code" name="course_code"
               value="<?= htmlspecialchars($_POST['course_code'] ?? '') ?>"
               placeholder="e.g. cs45" required>
    </div>

    <div class="form-row">
        <label for="ac_year">Academic Year <span class="req">*</span></label>
        <select id="ac_year" name="ac_year" required>
            <option value="">-- Select --</option>
            <option value="2024/2025" <?= (($_POST['ac_year'] ?? '') === '2024/2025') ? 'selected' : '' ?>>2024/2025</option>
            <option value="2025/2026" <?= (($_POST['ac_year'] ?? '') === '2025/2026') ? 'selected' : '' ?>>2025/2026</option>
            <option value="2026/2027" <?= (($_POST['ac_year'] ?? '') === '2026/2027') ? 'selected' : '' ?>>2026/2027</option>
        </select>
    </div>

    <div class="form-row">
        <label for="semester">Semester <span class="req">*</span></label>
        <select id="semester" name="semester" required>
            <option value="">-- Select --</option>
            <option value="1" <?= (($_POST['semester'] ?? '') == 1) ? 'selected' : '' ?>>Semester 1</option>
            <option value="2" <?= (($_POST['semester'] ?? '') == 2) ? 'selected' : '' ?>>Semester 2</option>
        </select>
    </div>

    <div class="section-title"></div>

    <div class="form-row">
        <label for="new_grade">New Grade <span class="req">*</span></label>
        <select id="new_grade" name="new_grade" required onchange="autoGradePoint()">
            <option value="">-- Select Grade --</option>
            <option value="A+">A+</option>
            <option value="A">A</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B">B</option>
            <option value="B-">B-</option>
            <option value="C+">C+</option>
            <option value="C">C</option>
            <option value="C-">C-</option>
            <option value="D">D</option>
            <option value="F">F</option>
        </select>
    </div>

    <div class="form-row">
        <label for="new_grade_point">New Grade Point <span class="req">*</span></label>
        <input type="number" id="new_grade_point" name="new_grade_point"
               step="0.01" min="0" max="4"
               value="<?= htmlspecialchars($_POST['new_grade_point'] ?? '') ?>"
               placeholder="e.g. 3.75" required>
    </div>

    <div class="section-title">📝 Justification</div>

    <div class="form-row">
        <label for="reason">Reason for Change <span class="req">*</span></label>
        <textarea id="reason" name="reason" rows="3" required
                  placeholder="e.g. Instructor reported a data entry error..."><?= htmlspecialchars($_POST['reason'] ?? '') ?></textarea>
    </div>

    <div class="btn-wrap">
        <button type="submit" class="btn-registrar">
            🔓 CHANGE GRADE
        </button>
    </div>

</form>

<!-- ============ AUTO GRADE POINT SCRIPT ============ -->
<script>
function autoGradePoint() {
    const map = {
        'A+': 4.00, 'A': 4.00, 'A-': 3.75,
        'B+': 3.50, 'B': 3.00, 'B-': 2.75,
        'C+': 2.50, 'C': 2.00, 'C-': 1.75,
        'D':  1.00, 'F': 0.00
    };
    const g = document.getElementById('new_grade').value;
    if (map[g] !== undefined) {
        document.getElementById('new_grade_point').value = map[g].toFixed(2);
    }
}
</script>