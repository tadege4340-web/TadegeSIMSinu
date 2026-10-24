<?php include 'header.php'; ?>
<?php require 'db.php'; ?>

<?php
// ======================
// ADMIN CHECK
// ======================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<p style='color:red; font-weight:bold; text-align:center;'>Only admin can update students.</p>";
    include 'footer.php';
    exit();
}

// ======================
// LOAD COLLEGES (PDO)
// ======================
$colleges = [];
$cres = $conn->query("SELECT code, name FROM college ORDER BY id");
if ($cres) {
    $colleges = $cres->fetchAll(PDO::FETCH_ASSOC);
}

// ======================
// LOAD DEPARTMENTS (PDO)
// ======================
$departments_by_college = [];
$dres = $conn->query("SELECT college_code, name FROM departments ORDER BY name");
if ($dres) {
    while ($r = $dres->fetch(PDO::FETCH_ASSOC)) {
        $departments_by_college[$r['college_code']][] = $r['name'];
    }
}

// ======================
// FETCH STUDENT FOR PREFILL (PDO)
// ======================
$student_data  = [];
$fetch_message = "";

if (isset($_POST['fetch'])) {
    $key = trim($_POST['update_key'] ?? '');
    if ($key !== '') {
        $stmt = $conn->prepare("SELECT * FROM users2 WHERE student_id = :key LIMIT 1");
        $stmt->execute(['key' => $key]);
        $student_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$student_data) {
            $fetch_message = "Student not found!";
        }
    }
}

// ======================
// HANDLE UPDATE (PDO)
// ======================
$update_message = "";
$update_type    = "success";

if (isset($_POST['update'])) {
    $student_id       = trim($_POST['student_id']       ?? '');
    $firstname        = trim($_POST['firstname']        ?? '');
    $father_name      = trim($_POST['father_name']      ?? '');
    $grandfather_name = trim($_POST['grandfather_name'] ?? '');
    $age              = (int)($_POST['age'] ?? 0) ?: null;
    $gender           = trim($_POST['gender']     ?? '') ?: null;
    $phone            = trim($_POST['phone']      ?? '') ?: null;
    $region           = trim($_POST['region']     ?? '') ?: null;
    $secondary_school = trim($_POST['secondary_school'] ?? '') ?: null;
    $college          = trim($_POST['college']    ?? '') ?: null;
    $department       = trim($_POST['department'] ?? '') ?: null;
    $year             = (int)($_POST['year'] ?? 0) ?: null;

    if ($student_id !== '') {
        try {
            $stmt = $conn->prepare(
                "UPDATE users2
                 SET firstname = :fname, father_name = :father, grandfather_name = :gf,
                     age = :age, gender = :gender, phone = :phone,
                     region = :region, secondary_school = :school,
                     college = :college, department = :dept, `year` = :year
                 WHERE student_id = :sid");

            $stmt->execute([
                'fname' => $firstname, 'father' => $father_name, 'gf' => $grandfather_name,
                'age' => $age, 'gender' => $gender, 'phone' => $phone,
                'region' => $region, 'school' => $secondary_school,
                'college' => $college, 'dept' => $department, 'year' => $year,
                'sid' => $student_id
            ]);

            if ($stmt->rowCount() > 0) {
                $update_message = "✅ Student updated successfully!";
                $update_type = "success";
                
                // Refresh displayed data
                $reload = $conn->prepare("SELECT * FROM users2 WHERE student_id = :sid LIMIT 1");
                $reload->execute(['sid' => $student_id]);
                $student_data = $reload->fetch(PDO::FETCH_ASSOC);
            } else {
                $update_message = "No changes made (or no matching student).";
                $update_type = "info";
            }
        } catch (PDOException $e) {
            $update_message = "❌ Update failed: " . $e->getMessage();
            $update_type    = "error";
        }
    }
}

// ======================
// FETCH ALL STUDENTS (PDO)
// ======================
$result = $conn->query(
    "SELECT student_id, firstname, father_name, grandfather_name,
            age, gender, phone, region, secondary_school,
            college, department, `year`
     FROM users2 ORDER BY student_id ASC"
);
$all_students = $result->fetchAll(PDO::FETCH_ASSOC);

$college_names = [
    'technology' => 'Engineering and Technology',
    'medicine'   => 'Medicine and Health Sciences',
    'economics'  => 'Business and Economics',
    'naturals'   => 'Natural and Computational Sciences',
    'food'       => 'Agriculture, Food, Environment',
    'social'     => 'Social Science and Humanities',
    'behavioral' => 'Education and Behavioral Science',
    'law'        => 'School of Law',
];
?>

<h2 style="text-align:center; color:#2c3e50;">Update Student</h2>

<!-- FETCH FORM -->
<form method="post" style="text-align:center; margin:20px;">
    <label>Enter Student ID to Edit:</label><br><br>
    <input type="text" name="update_key"
           value="<?= htmlspecialchars($_POST['update_key'] ?? '') ?>"
           placeholder="e.g. INU0800343UR" required
           style="padding:8px; width:250px;">
    <input type="submit" name="fetch" value="LOAD STUDENT"
           style="padding:8px 20px; background:#2c3e50; color:#fff;
                  border:none; border-radius:5px; cursor:pointer;">
</form>

<?php if ($fetch_message !== ''): ?>
    <p style="text-align:center; background:#fee2e2; color:#991b1b; padding:10px;
              border-radius:6px; font-weight:bold;">
        <?= htmlspecialchars($fetch_message) ?>
    </p>
<?php endif; ?>

<?php if ($update_message !== ''): ?>
    <p style="text-align:center; padding:10px; border-radius:6px; font-weight:bold;
              background:<?= $update_type === 'success' ? '#d1fae5' : ($update_type === 'error' ? '#fee2e2' : '#e0e7ff') ?>;
              color:<?= $update_type === 'success' ? '#065f46' : ($update_type === 'error' ? '#991b1b' : '#3730a3') ?>;">
        <?= htmlspecialchars($update_message) ?>
    </p>
<?php endif; ?>

<!-- UPDATE FORM -->
<?php if (!empty($student_data)): ?>
<hr style="margin:25px 0;">

<h3 style="text-align:center; color:#2c3e50;">
    Edit: <?= htmlspecialchars(($student_data['firstname'] ?? '') . ' ' . ($student_data['father_name'] ?? '')) ?>
</h3>

<form method="post" style="max-width:600px; margin:auto;">
    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_data['student_id']) ?>">

    <label>Student ID</label>
    <input type="text" value="<?= htmlspecialchars($student_data['student_id']) ?>" disabled
           style="background:#eee; padding:8px; width:100%; margin-bottom:10px;">

    <label>First Name</label>
    <input type="text" name="firstname" value="<?= htmlspecialchars($student_data['firstname'] ?? '') ?>" required
           style="padding:8px; width:100%; margin-bottom:10px;">

    <label>Father Name</label>
    <input type="text" name="father_name" value="<?= htmlspecialchars($student_data['father_name'] ?? '') ?>" required
           style="padding:8px; width:100%; margin-bottom:10px;">

    <label>Grandfather Name</label>
    <input type="text" name="grandfather_name" value="<?= htmlspecialchars($student_data['grandfather_name'] ?? '') ?>" required
           style="padding:8px; width:100%; margin-bottom:10px;">

    <label>Age</label>
    <input type="number" name="age" min="0" value="<?= htmlspecialchars($student_data['age'] ?? '') ?>"
           style="padding:8px; width:100%; margin-bottom:10px;">

    <label>Gender</label><br>
    <input type="radio" name="gender" value="Male" <?= strtolower($student_data['gender'] ?? '') === 'male'   ? 'checked' : '' ?>> MALE
    <input type="radio" name="gender" value="Female" <?= strtolower($student_data['gender'] ?? '') === 'female' ? 'checked' : '' ?>> FEMALE
    <br><br>

    <label>Phone</label>
    <input type="tel" name="phone" value="<?= htmlspecialchars($student_data['phone'] ?? '') ?>"
           style="padding:8px; width:100%; margin-bottom:10px;">

    <label>Region</label>
    <select name="region" style="padding:8px; width:100%; margin-bottom:10px;">
        <?php
        $regions = ['Addis Ababa','Afar','Amhara','Benishangul-Gumuz','Central Ethiopia','Dire Dawa','Gambella','Harari','Oromia','Sidama','Somali','South Ethiopia','South West Ethiopia','Tigray'];
        $current = $student_data['region'] ?? '';
        echo '<option value="">-- Select Region --</option>';
        foreach ($regions as $r) {
            $sel = ($current === $r) ? 'selected' : '';
            echo "<option value=\"$r\" $sel>$r</option>";
        }
        ?>
    </select>

    <label>Secondary School</label>
    <input type="text" name="secondary_school" value="<?= htmlspecialchars($student_data['secondary_school'] ?? '') ?>"
           style="padding:8px; width:100%; margin-bottom:10px;">

    <label>College</label>
    <select name="college" id="collegeSelect" onchange="updateDepartments()" style="padding:8px; width:100%; margin-bottom:10px;">
        <option value="">-- Select College --</option>
        <?php
        $currentCollege = $student_data['college'] ?? '';
        foreach ($colleges as $c) {
            $sel = ($currentCollege === $c['code']) ? 'selected' : '';
            echo '<option value="' . htmlspecialchars($c['code']) . '" ' . $sel . '>' . htmlspecialchars($c['name']) . '</option>';
        }
        ?>
    </select>

    <label>Department</label>
    <select name="department" id="departmentSelect" style="padding:8px; width:100%; margin-bottom:10px;">
        <option value="">-- Select College First --</option>
        <?php
        $currentDept = $student_data['department'] ?? '';
        $currentCollegeCode = $student_data['college'] ?? '';
        if ($currentCollegeCode && isset($departments_by_college[$currentCollegeCode])) {
            foreach ($departments_by_college[$currentCollegeCode] as $dname) {
                $sel = ($currentDept === $dname) ? 'selected' : '';
                echo '<option value="' . htmlspecialchars($dname) . '" ' . $sel . '>' . htmlspecialchars($dname) . '</option>';
            }
        }
        ?>
    </select>

    <label>Year</label>
    <input type="number" name="year" min="1" max="7" value="<?= htmlspecialchars($student_data['year'] ?? '') ?>"
           style="padding:8px; width:100%; margin-bottom:10px;">

    <input type="submit" name="update" value="UPDATE" onclick="return confirm('Save changes?');"
           style="padding:12px 24px; background:#2c3e50; color:#fff; border:none; border-radius:5px; cursor:pointer; font-weight:bold;">
</form>

<script>
const departmentsByCollege = <?= json_encode($departments_by_college) ?>;
function updateDepartments() {
    const collegeSelect    = document.getElementById('collegeSelect');
    const departmentSelect = document.getElementById('departmentSelect');
    const collegeCode      = collegeSelect.value;
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
<?php endif; ?>

<hr style="margin:25px 0;">
<h2 style="text-align:center; color:#2c3e50;">All Students</h2>

<div style="overflow-x:auto; padding:10px;">
<table border="1" cellpadding="8" cellspacing="0" style="width:100%; border-collapse:collapse; text-align:center; font-size:13px;">
    <thead style="background:#2c3e50; color:#fff;">
        <tr>
            <th>#</th>
            <th>Student ID</th>
            <th>First Name</th>
            <th>Father Name</th>
            <th>Grandfather Name</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Phone</th>
            <th>Region</th>
            <th>Secondary School</th>
            <th>College</th>
            <th>Department</th>
            <th>Year</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($all_students)): ?>
            <?php $count = 1; ?>
            <?php foreach ($all_students as $row): ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><?= htmlspecialchars($row['student_id']) ?></td>
                    <td><?= htmlspecialchars($row['firstname']) ?></td>
                    <td><?= htmlspecialchars($row['father_name']) ?></td>
                    <td><?= htmlspecialchars($row['grandfather_name']) ?></td>
                    <td><?= htmlspecialchars($row['age'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['gender'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['phone'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['region'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['secondary_school'] ?? '—') ?></td>
                    <td>
                        <?php
                        $code = $row['college'] ?? '';
                        echo htmlspecialchars($college_names[$code] ?? ($code ?: '—'));
                        ?>
                    </td>
                    <td><?= htmlspecialchars($row['department'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($row['year'] ?? '—') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="13" style="color:#991b1b; font-weight:bold;">No students found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>

<?php include 'footer.php'; ?>