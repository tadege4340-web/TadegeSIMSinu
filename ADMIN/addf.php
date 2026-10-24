<?php
session_start();
require 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginf.php");
    exit();
}

/* ============ LOAD COLLEGES FROM DB (PDO) ============ */
$colleges = [];
$stmt = $conn->query("SELECT code, name FROM college ORDER BY id");
if ($stmt) {
    $colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* ============ LOAD ALL DEPARTMENTS (grouped by college) (PDO) ============ */
$departments_by_college = [];
$stmt = $conn->query("SELECT college_code, name FROM departments ORDER BY name");
if ($stmt) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $departments_by_college[$row['college_code']][] = $row['name'];
    }
}

include 'header.php';
?>

<div class="form-card">
    <h2>➕ Add Student</h2>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'added'): ?>
        <p class="alert-success">✅ Student added successfully!</p>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <p class="alert-error"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>

    <form action="add.php" method="POST">

        <label>Student ID *</label>
        <input type="text" name="studentid" placeholder="e.g. INU0800343UR" required>

        <label>First Name *</label>
        <input type="text" name="firstname" placeholder="Given name" required>

        <label>Father Name *</label>
        <input type="text" name="father_name" placeholder="Father's given name" required>

        <label>Grandfather Name *</label>
        <input type="text" name="grandfather_name" placeholder="Grandfather's given name" required>

        <label>Age</label>
        <input type="number" name="age" min="0">

        <label>Gender</label>
        <label style="display:inline-block; font-weight:normal; margin-right:15px;">
            <input type="radio" name="gender" value="Male"> Male
        </label>
        <label style="display:inline-block; font-weight:normal;">
            <input type="radio" name="gender" value="Female"> Female
        </label>

        <label>Phone No (optional)</label>
        <input type="tel" name="phone" maxlength="10">

        <label>Region *</label>
        <select name="region" required>
            <option value="">-- Select Region --</option>
            <option value="Addis Ababa">Addis Ababa</option>
            <option value="Afar">Afar</option>
            <option value="Amhara">Amhara</option>
            <option value="Benishangul-Gumuz">Benishangul-Gumuz</option>
            <option value="Central Ethiopia">Central Ethiopia</option>
            <option value="Dire Dawa">Dire Dawa</option>
            <option value="Gambella">Gambella</option>
            <option value="Harari">Harari</option>
            <option value="Oromia">Oromia</option>
            <option value="Sidama">Sidama</option>
            <option value="Somali">Somali</option>
            <option value="South Ethiopia">South Ethiopia</option>
            <option value="South West Ethiopia">South West Ethiopia</option>
            <option value="Tigray">Tigray</option>
        </select>

        <label>Secondary School *</label>
        <input type="text" name="secondary_school" placeholder="e.g. Injibara Secondary School" required>

        <!-- ============ COLLEGE DROPDOWN ============ -->
        <label>College *</label>
        <select name="college" id="collegeSelect" required onchange="updateDepartments()">
            <option value="">-- Select College --</option>
            <?php foreach ($colleges as $c): ?>
                <option value="<?= htmlspecialchars($c['code']) ?>">
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- ============ DEPARTMENT DROPDOWN (dynamic) ============ -->
        <label>Department *</label>
        <select name="department" id="departmentSelect" required>
            <option value="">-- First select a college --</option>
        </select>

        <label>Year *</label>
        <input type="number" name="year" min="1" max="7" required>

        <!-- 🔥 NEW SEMESTER DROPDOWN -->
        <label>Current Semester *</label>
        <select name="current_semester" required>
            <option value="1">Semester 1</option>
            <option value="2">Semester 2</option>
        </select>

        <input type="submit" value="💾 ADD STUDENT">
    </form>
</div>

<!-- ============ DYNAMIC DEPARTMENT SCRIPT ============ -->
<script>
const departmentsByCollege = <?= json_encode($departments_by_college) ?>;

function updateDepartments() {
    const collegeSelect    = document.getElementById('collegeSelect');
    const departmentSelect = document.getElementById('departmentSelect');

    const collegeCode = collegeSelect.value;
    departmentSelect.innerHTML = '<option value="">-- Select Department --</option>';

    if (!collegeCode || !departmentsByCollege[collegeCode]) {
        departmentSelect.innerHTML = '<option value="">-- First select a college --</option>';
        return;
    }

    departmentsByCollege[collegeCode].forEach(function(deptName) {
        const option = document.createElement('option');
        option.value = deptName;
        option.textContent = deptName;
        departmentSelect.appendChild(option);
    });
}
</script>

<?php include 'footer.php'; ?>