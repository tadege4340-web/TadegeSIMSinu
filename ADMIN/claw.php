<?php include 'header.php'; ?>
<?php require 'db.php'; ?>

<?php
/* ============================================================
   SCHOOL: Law
   ============================================================ */
$college_code = 'law';
$college_name = 'School of Law';
$college_desc = 'Educating ethical legal professionals dedicated to justice, the rule of law, and the protection of human rights in Ethiopia and beyond.';

/* ============ SAFE COUNT HELPER (PDO) ============ */
function getCount($conn, $table, $college, $dept = null) {
    try {
        $sql = "SELECT COUNT(*) AS c FROM `$table` WHERE college = :college";
        $params = ['college' => $college];
        
        if ($dept !== null) {
            $sql .= " AND department = :dept";
            $params['dept'] = $dept;
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['c'] ?? 0);
    } catch (PDOException $e) {
        return 0;
    }
}

/* ============ TOTALS ============ */
$students = getCount($conn, 'users2',   $college_code);
$teachers = getCount($conn, 'teachers', $college_code);
$courses  = getCount($conn, 'addcourse',$college_code);

/* ============ DEPARTMENTS (PDO) ============ */
$departments = [];
try {
    $stmt = $conn->prepare("SELECT * FROM departments WHERE college_code = :cc ORDER BY name");
    $stmt->execute(['cc' => $college_code]);
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Keep array empty if error
}
?>

<div class="theme-law">

    <!-- HERO -->
    <div class="college-hero">
        <span class="college-badge">School</span>
        <h1><?= htmlspecialchars($college_name) ?></h1>
        <p><?= htmlspecialchars($college_desc) ?></p>
    </div>

    <!-- STAT CARDS -->
    <h3 class="dashboard-section-title">📊 School Statistics</h3>
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

    <!-- PROGRAMS -->
    <h3 class="dashboard-section-title" id="departments">🏛️ Programs</h3>
    <div class="dept-grid">
        <?php if (empty($departments)): ?>
            <div style="grid-column: 1/-1; text-align:center; padding:40px; background:#fef3c7; border-radius:10px; color:#92400e;">
                <strong>No programs found.</strong><br>
                <span style="font-size:13px;">Run the INSERT script to populate programs.</span>
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
    <h3 class="dashboard-section-title">ℹ️ About the School</h3>
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