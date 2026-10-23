<?php include 'header.php'; ?>
<?php require 'db.php'; ?>

<?php
/* ============ SAFE COUNT HELPER ============ */
function getCount($conn, $table, $where = '') {
    $sql = "SELECT COUNT(*) AS c FROM `$table`" . ($where ? " WHERE $where" : "");
    $result = @mysqli_query($conn, $sql);
    if (!$result) return 0;
    $row = mysqli_fetch_assoc($result);
    return (int)($row['c'] ?? 0);
}

/* ============ LIVE COUNTS ============ */
$students = getCount($conn, 'users2');
$teachers = getCount($conn, 'teachers');
$courses  = getCount($conn, 'addcourse');
$assigned = getCount($conn, 'student_courses');
$colleges = getCount($conn, 'college');
$departments = getCount($conn, 'departments');

/* ============ RECENT STUDENTS ============ */
$recent_students = [];
$res = @mysqli_query($conn, 
    "SELECT student_id, firstname, father_name, college, department, `year`
     FROM users2
     ORDER BY id DESC
     LIMIT 5");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) $recent_students[] = $row;
}

/* ============ COLLEGE NAME LOOKUP ============ */
$college_names = [
    'technology' => 'Engineering & Tech.',
    'medicine'   => 'Medicine & Health',
    'economics'  => 'Business & Econ.',
    'naturals'   => 'Natural Sciences',
    'food'       => 'Agriculture',
    'social'     => 'Social Sciences',
    'behavioral' => 'Education',
    'law'        => 'Law',
];
?>

<!-- ============================================================
     1. WELCOME BANNER
     ============================================================ -->
<div class="dashboard-welcome">
    <h2> Welcome , <span class="welcome-highlight"><?= htmlspecialchars($welcome_name) ?></span>!</h2>
    <p>Injibara University — Student Information Management System Dashboard</p>
</div>

<!-- ============================================================
     2. QUICK STATISTICS — 4 cards
     ============================================================ -->
<h3 class="dashboard-section-title">📊 Quick Statistics</h3>

<div class="dashboard-grid">

    <a href="view.php" class="dash-card blue">
        <div class="dash-icon">👨‍🎓</div>
        <div class="dash-label">Total Students</div>
        <div class="dash-value"><?= number_format($students) ?></div>
        <div class="dash-desc">Registered in system</div>
    </a>

    <a href="teacherview.php" class="dash-card green">
        <div class="dash-icon">👨‍🏫</div>
        <div class="dash-label">Total Teachers</div>
        <div class="dash-value"><?= number_format($teachers) ?></div>
        <div class="dash-desc">Academic staff</div>
    </a>

    <a href="mycourseviewadn.php" class="dash-card purple">
        <div class="dash-icon">📚</div>
        <div class="dash-label">Total Courses</div>
        <div class="dash-value"><?= number_format($courses) ?></div>
        <div class="dash-desc">Available courses</div>
    </a>

    <a href="#" class="dash-card orange">
        <div class="dash-icon">📝</div>
        <div class="dash-label">Assigned Courses</div>
        <div class="dash-value"><?= number_format($assigned) ?></div>
        <div class="dash-desc">Student-course links</div>
    </a>

</div>

<!-- ============================================================
     3. COLLEGES & DEPARTMENTS
     ============================================================ -->
<h3 class="dashboard-section-title">🏛️ Colleges &amp; Departments</h3>

<div class="admin-college-box">

    <div class="admin-college-row">
        <div class="admin-college-icon">🏛️</div>
        <div class="admin-college-info">
            <div class="admin-college-label">Total Colleges</div>
            <div class="admin-college-name"><?= $colleges ?> Colleges</div>
            <div class="admin-college-meta">Engineering, Medicine, Business, Naturals, Agriculture, Social, Education, Law</div>
        </div>
    </div>

    <div class="admin-college-row">
        <div class="admin-college-icon">📖</div>
        <div class="admin-college-info">
            <div class="admin-college-label">Total Departments</div>
            <div class="admin-college-name"><?= $departments ?> Departments</div>
            <div class="admin-college-meta">Spread across all <?= $colleges ?> colleges</div>
        </div>
    </div>

</div>

<!-- ============================================================
     4. QUICK ACTIONS
     ============================================================ -->
<h3 class="dashboard-section-title">⚡ Quick Actions</h3>

<div class="quick-actions">
    <a href="addf.php" class="quick-btn">
        <span class="q-icon">➕</span> Add New Student
    </a>
    <a href="teacherform.php" class="quick-btn">
        <span class="q-icon">➕</span> Add New Teacher
    </a>
    <a href="mycourseadmin.php" class="quick-btn">
        <span class="q-icon">➕</span> Add New Course
    </a>
    <a href="gradeform.php" class="quick-btn">
        <span class="q-icon">➕</span> Add New Grade
    </a>
</div>

<!-- ============================================================
     5. RECENT STUDENTS
     ============================================================ -->
<h3 class="dashboard-section-title">🎓 Recent Students</h3>

<div class="admin-table-card">
    <?php if (empty($recent_students)): ?>
        <p style="padding:30px; text-align:center; color:#94a3b8; font-style:italic;">
            No students registered yet.
        </p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>College</th>
                    <th>Department</th>
                    <th>Year</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_students as $s): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($s['student_id']) ?></strong></td>
                    <td><?= htmlspecialchars(trim(($s['firstname'] ?? '') . ' ' . ($s['father_name'] ?? ''))) ?></td>
                    <td><?= htmlspecialchars($college_names[$s['college']] ?? ($s['college'] ?? '—')) ?></td>
                    <td><?= htmlspecialchars($s['department'] ?? '—') ?></td>
                    <td>Year <?= htmlspecialchars($s['year'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- ============================================================
     6. SYSTEM INFO
     ============================================================ -->
<h3 class="dashboard-section-title">ℹ️ System Information</h3>

<div class="info-card">
    <h3>Welcome to Your Administrative Console</h3>
    <p>
        This is the <strong>Injibara University Student Information Management System</strong>.
        Use the sidebar on the left to manage <strong>students</strong>, <strong>teachers</strong>,
        <strong>courses</strong>, and <strong>grades</strong> across all colleges.
        Click any stat card above to jump directly to that section.
    </p>
    <p style="margin-top:12px; color:#64748b; font-size:13px;">
        <strong>Quick tip:</strong> Use the <strong>COLLEGE</strong> menu to browse all 8 colleges,
        their departments, teachers, and students.
    </p>
</div>

<?php include 'footer.php'; ?>