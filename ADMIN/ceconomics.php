<?php include 'header.php'; ?>
<?php require 'db.php'; ?>

<?php
/* ============================================================
   COLLEGE: Business and Economics
   ============================================================ */
$college_code = 'economics';
$college_name = 'College of Business and Economics';
$college_desc = 'Developing business leaders, entrepreneurs, and economic analysts equipped with modern management, accounting, and analytical skills for a rapidly changing global economy.';

/* ============ SAFE COUNT HELPER ============ */
function getCount($conn, $table, $college, $dept = null) {
    $sql = "SELECT COUNT(*) AS c FROM `$table` WHERE college = ?";
    $params = [$college];
    $types = 's';
    if ($dept !== null) {
        $sql .= " AND department = ?";
        $params[] = $dept;
        $types .= 's';
    }
    $stmt = $conn->prepare($sql);
    if (!$stmt) return 0;
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row['c'] ?? 0;
}

/* ============ TOTALS ============ */
$students = getCount($conn, 'users2',   $college_code);
$teachers = getCount($conn, 'teachers', $college_code);
$courses  = getCount($conn, 'addcourse',$college_code);

/* ============ DEPARTMENTS ============ */
$stmt = $conn->prepare("SELECT * FROM departments WHERE college_code = ? ORDER BY name");
$stmt->bind_param("s", $college_code);
$stmt->execute();
$departments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="theme-economics">

    <!-- HERO -->
    <div class="college-hero">
        <span class="college-badge">College</span>
        <h1><?= htmlspecialchars($college_name) ?></h1>
        <p><?= htmlspecialchars($college_desc) ?></p>
    </div>

    <!-- STAT CARDS -->
    <h3 class="dashboard-section-title">📊 College Statistics</h3>
    <div class="dashboard-grid">

        <a href="college_teachers.php?college=<?= urlencode($college_code) ?>" class="dash-card blue">
            <div class="dash-icon">👥</div>
            <div class="dash-label">Teachers</div>
            <div class="dash-value"><?= number_format($teachers) ?></div>
            <div class="dash-desc">Academic staff</div>
        </a>

        <a href="college_students.php?college=<?= urlencode($college_code) ?>" class="dash-card green">
            <div class="dash-icon">🎓</div>
            <div class="dash-label">Students</div>
            <div class="dash-value"><?= number_format($students) ?></div>
            <div class="dash-desc">Enrolled students</div>
        </a>

        <a href="college_courses.php?college=<?= urlencode($college_code) ?>" class="dash-card purple">
            <div class="dash-icon">📚</div>
            <div class="dash-label">Courses</div>
            <div class="dash-value"><?= number_format($courses) ?></div>
            <div class="dash-desc">Available courses</div>
        </a>

        <a href="#departments" class="dash-card orange">
            <div class="dash-icon">🏛️</div>
            <div class="dash-label">Departments</div>
            <div class="dash-value"><?= number_format(count($departments)) ?></div>
            <div class="dash-desc">Academic units</div>
        </a>

    </div>

    <!-- DEPARTMENTS -->
    <h3 class="dashboard-section-title" id="departments">🏛️ Departments</h3>
    <div class="dept-grid">
        <?php if (empty($departments)): ?>
            <div style="grid-column: 1/-1; text-align:center; padding:40px; background:#fef3c7; border-radius:10px; color:#92400e;">
                <strong>No departments found.</strong><br>
                <span style="font-size:13px;">Run the INSERT script to populate departments.</span>
            </div>
        <?php else: ?>
            <?php foreach ($departments as $dept):
                $t = getCount($conn, 'teachers', $college_code, $dept['name']);
                $s = getCount($conn, 'users2',   $college_code, $dept['name']);
            ?>
            <a href="department_view.php?dept=<?= urlencode($dept['name']) ?>&college=<?= urlencode($college_code) ?>" class="dept-card">
                <div class="dept-name"><?= htmlspecialchars($dept['name']) ?></div>
                <div class="dept-stats">
                    <span class="dept-stat">👥 <strong><?= $t ?></strong> Teachers</span>
                    <span class="dept-stat">🎓 <strong><?= $s ?></strong> Students</span>
                </div>
                <div class="dept-view-link">View Details</div>
            </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- ABOUT -->
    <h3 class="dashboard-section-title">ℹ️ About the College</h3>
    <div class="info-card">
        <p><?= htmlspecialchars($college_desc) ?></p>
    </div>

</div>

<script>
document.querySelectorAll('a[href="#departments"]').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.getElementById('departments');
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

document.querySelectorAll('.dash-value').forEach(function(el) {
    const text = el.textContent.replace(/,/g, '');
    const target = parseInt(text);
    if (isNaN(target)) return;
    let current = 0;
    const step = Math.max(1, Math.ceil(target / 30));
    const timer = setInterval(function() {
        current += step;
        if (current >= target) { current = target; clearInterval(timer); }
        el.textContent = current.toLocaleString();
    }, 30);
});
</script>

<?php include 'footer.php'; ?>