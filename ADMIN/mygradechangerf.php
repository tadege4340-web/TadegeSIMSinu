<!-- mygradechangerf.php -->
<link rel="stylesheet" href="mygradechangerf.css">

<div class="registrar-page-container" style="padding: 20px; max-width: 800px; margin: auto;">
    
    <!-- HEADER -->
    <div class="registrar-header" style="margin-bottom: 20px;">
        <h2 style="color: #17365d; margin-bottom: 5px;">Change Grade</h2>
        <p style="color: #64748b;">Admin / Registrar override tool.</p>
    </div>

    <!-- ALERTS (SUCCESS / ERROR MESSAGES) -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'error' ?>" style="padding: 15px; margin-bottom: 20px; border-radius: 5px; font-weight: bold; 
                    background: <?= $message_type === 'success' ? '#dcfce7' : '#fee2e2' ?>; 
                    color: <?= $message_type === 'success' ? '#166534' : '#991b1b' ?>;">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- FORM -->
    <form method="POST" class="grade-change-form" style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">

        <div class="form-row" style="margin-bottom: 15px;">
            <label for="student_id" style="display: block; font-weight: bold; margin-bottom: 5px;">Student ID <span class="req" style="color: red;">*</span></label>
            <input type="text" id="student_id" name="student_id"
                   value="<?= htmlspecialchars($_POST['student_id'] ?? '') ?>"
                   placeholder="e.g. 0936" required
                   style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        </div>

        <div class="form-row" style="margin-bottom: 15px;">
            <label for="course_code" style="display: block; font-weight: bold; margin-bottom: 5px;">Course Code <span class="req" style="color: red;">*</span></label>
            <input type="text" id="course_code" name="course_code"
                   value="<?= htmlspecialchars($_POST['course_code'] ?? '') ?>"
                   placeholder="e.g. cs45" required
                   style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        </div>

        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div class="form-row" style="flex: 1;">
                <label for="ac_year" style="display: block; font-weight: bold; margin-bottom: 5px;">Academic Year <span class="req" style="color: red;">*</span></label>
                <select id="ac_year" name="ac_year" required
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    <option value="">-- Select --</option>
                    <option value="2024/2025" <?= (($_POST['ac_year'] ?? '') === '2024/2025') ? 'selected' : '' ?>>2024/2025</option>
                    <option value="2025/2026" <?= (($_POST['ac_year'] ?? '') === '2025/2026') ? 'selected' : '' ?>>2025/2026</option>
                    <option value="2026/2027" <?= (($_POST['ac_year'] ?? '') === '2026/2027') ? 'selected' : '' ?>>2026/2027</option>
                </select>
            </div>

            <div class="form-row" style="flex: 1;">
                <label for="semester" style="display: block; font-weight: bold; margin-bottom: 5px;">Semester <span class="req" style="color: red;">*</span></label>
                <select id="semester" name="semester" required
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    <option value="">-- Select --</option>
                    <option value="1" <?= (($_POST['semester'] ?? '') == 1) ? 'selected' : '' ?>>Semester 1</option>
                    <option value="2" <?= (($_POST['semester'] ?? '') == 2) ? 'selected' : '' ?>>Semester 2</option>
                </select>
            </div>
        </div>

        <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e2e8f0;">

        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div class="form-row" style="flex: 1;">
                <label for="new_grade" style="display: block; font-weight: bold; margin-bottom: 5px;">New Grade <span class="req" style="color: red;">*</span></label>
                <select id="new_grade" name="new_grade" required onchange="autoGradePoint()"
                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
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

            <div class="form-row" style="flex: 1;">
                <label for="new_grade_point" style="display: block; font-weight: bold; margin-bottom: 5px;">New Grade Point <span class="req" style="color: red;">*</span></label>
                <input type="number" id="new_grade_point" name="new_grade_point"
                       step="0.01" min="0" max="4"
                       value="<?= htmlspecialchars($_POST['new_grade_point'] ?? '') ?>"
                       placeholder="e.g. 3.75" required
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>
        </div>

        <div class="form-row" style="margin-bottom: 20px;">
            <label for="reason" style="display: block; font-weight: bold; margin-bottom: 5px;">Reason for Change <span class="req" style="color: red;">*</span></label>
            <textarea id="reason" name="reason" rows="3" required
                      placeholder="e.g. Instructor reported a data entry error..."
                      style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;"><?= htmlspecialchars($_POST['reason'] ?? '') ?></textarea>
        </div>

        <div class="btn-wrap" style="text-align: right;">
            <button type="submit" class="btn-registrar"
                    style="background: #1e4e79; color: white; padding: 12px 25px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 15px;">
                🔓 CHANGE GRADE
            </button>
        </div>

    </form>
</div>

<!-- AUTO GRADE POINT SCRIPT -->
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