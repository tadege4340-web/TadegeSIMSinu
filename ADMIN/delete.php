<?php include 'header.php'; ?>
<?php require 'db.php'; ?>

<?php
$message = "";
$message_type = "error";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deleteKey'])) {
    $deleteKey = trim($_POST['deleteKey']);

    if ($deleteKey !== '') {
        try {
            // 1. Delete from users2 (PDO)
            $stmt = $conn->prepare("DELETE FROM users2 WHERE student_id = :sid");
            $stmt->execute(['sid' => $deleteKey]);

            if ($stmt->rowCount() > 0) { // Use rowCount() for PDO instead of affected_rows
                // 2. Also delete the login account (PDO)
                $stmt2 = $conn->prepare("DELETE FROM users WHERE student_id = :sid");
                $stmt2->execute(['sid' => $deleteKey]);

                $message = "Student deleted successfully!";
                $message_type = "success";
            } else {
                $message = "No student found with this ID.";
            }
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
        }
    } else {
        $message = "Please enter Student ID.";
    }
}

/* ============ LOAD STUDENTS WITH COLLEGE (PDO) ============ */
$stmt = $conn->query(
    "SELECT student_id, firstname, father_name, grandfather_name,
            age, gender, phone, region, secondary_school,
            college, department, `year`
     FROM users2
     ORDER BY student_id ASC"
);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ============ COLLEGE NAME LOOKUP ============ */
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

<h2 style="text-align:center; color:#2c3e50;">Delete Student</h2>

<?php if ($message !== ''): ?>
    <p style="text-align:center; padding:10px; border-radius:6px; font-weight:bold;
              background:<?= $message_type === 'success' ? '#d1fae5' : '#fee2e2' ?>;
              color:<?= $message_type === 'success' ? '#065f46' : '#991b1b' ?>;">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<form method="POST" style="text-align:center; margin-bottom:20px;">
    <label>Enter Student ID to Delete:</label><br><br>
    <input type="text" name="deleteKey" placeholder="e.g. INU0800343UR" required
           style="padding:8px; width:250px;">
    <input type="submit" value="DELETE"
           style="padding:8px 20px; background:#c0392b; color:#fff;
                  border:none; border-radius:5px; cursor:pointer;">
</form>

<h2 style="text-align:center; color:#2c3e50; margin-top:30px;">All Students</h2>

<div style="overflow-x:auto; padding:10px;">
<table border="1" cellpadding="8" cellspacing="0"
       style="width:100%; border-collapse:collapse; text-align:center; font-size:13px;">
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
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($students)): ?>
            <?php $count = 1; ?>
            <?php foreach ($students as $row): ?>
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
                    <td>
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="deleteKey"
                                   value="<?= htmlspecialchars($row['student_id']) ?>">
                            <button type="submit"
                                    onclick="return confirm('Delete this student (<?= htmlspecialchars($row['firstname']) ?> <?= htmlspecialchars($row['father_name']) ?>, ID: <?= htmlspecialchars($row['student_id']) ?>)?');"
                                    style="background:#c0392b; color:#fff; border:none;
                                           padding:6px 12px; border-radius:4px; cursor:pointer;">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="14" style="color:#991b1b; font-weight:bold;">No students found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>

<?php include 'footer.php'; ?>