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

/* ============ LOAD ALL DEPARTMENTS (PDO) ============ */
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
    <h2>➕ Add Teacher</h2>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'added'): ?>
        <p class="alert-success">✅ Teacher added successfully!</p>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <p class="alert-error"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>

    <form action="teacher.php" method="POST">

        <label for="full_name">Full Name *</label>
        <input type="text" id="full_name" name="full_name"
               placeholder="e.g. Abebe Tesfaye Alemu" required>

        <label for="teacher_id">Teacher ID *</label>
        <input type="text" id="teacher_id" name="id"
               placeholder="e.g. TCH-2024-001" required>

        <label for="age">Age *</label>
        <input type="number" id="age" name="age" min="18" max="80" required>

        <label>Gender *</label>
        <label style="display:inline-block; font-weight:normal; margin-right:15px;">
            <input type="radio" name="gender" value="Male" required> Male
        </label>
        <label style="display:inline-block; font-weight:normal;">
            <input type="radio" name="gender" value="Female" required> Female
        </label>

        <label for="email">Email *</label>
        <input type="email" id="email" name="email"
               placeholder="e.g. abebe@inu.edu.et" required>

        <label for="phone">Phone</label>
        <input type="tel" id="phone" name="phone"
               placeholder="e.g. 0912345678" maxlength="15">

        <label for="address">Address</label>
        <input type="text" id="address" name="address"
               placeholder="e.g. Injibara, Awi Zone">

        <!-- ============ COLLEGE DROPDOWN ============ -->
        <label for="college">College *</label>
        <select id="college" name="college" required onchange="updateDepartments()">
            <option value="">-- Select College --</option>
            <?php foreach ($colleges as $c): ?>
                <option value="<?= htmlspecialchars($c['code']) ?>">
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- ============ DEPARTMENT DROPDOWN (dynamic) ============ -->
        <label for="department">Department *</label>
        <select id="department" name="department" required>
            <option value="">-- First select a college --</option>
        </select>

        <label for="qualification">Qualification</label>
        <select id="qualification" name="qualification">
            <option value="">-- Select Qualification --</option>
            <option value="BSc">BSc</option>
            <option value="BA">BA</option>
            <option value="MSc">MSc</option>
            <option value="MA">MA</option>
            <option value="PhD">PhD</option>
            <option value="Diploma">Diploma</option>
        </select>

        <label for="position">Position</label>
        <select id="position" name="position">
            <option value="">-- Select Position --</option>
            <option value="Lecturer">Lecturer</option>
            <option value="Assistant Professor">Assistant Professor</option>
            <option value="Associate Professor">Associate Professor</option>
            <option value="Professor">Professor</option>
            <option value="Lab Assistant">Lab Assistant</option>
            <option value="Department Head">Department Head</option>
        </select>

        <label for="hire_date">Hire Date</label>
        <input type="date" id="hire_date" name="hire_date">

        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="Active">Active</option>
            <option value="On Leave">On Leave</option>
            <option value="Retired">Retired</option>
        </select>

        <input type="submit" value="💾 ADD TEACHER">

    </form>
</div>

<!-- ============ DYNAMIC DEPARTMENT SCRIPT ============ -->
<script>
/* Departments grouped by college — generated from PHP */
const departmentsByCollege = <?= json_encode($departments_by_college) ?>;

function updateDepartments() {
    const collegeSelect    = document.getElementById('college');
    const departmentSelect = document.getElementById('department');

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