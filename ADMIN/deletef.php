<?php
session_start();
require 'db.php';

// Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginf.php");
    exit();
}

// Handle deletion
$message = "";
$msg_type = "info";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deleteKey'])) {
    $deleteKey = trim($_POST['deleteKey']);

    if (!empty($deleteKey)) {
        // Delete from users2
        $stmt = $conn->prepare("DELETE FROM users2 WHERE student_id = ?");
        $stmt->bind_param("s", $deleteKey);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $message = "Student deleted successfully!";
            $msg_type = "success";
        } else {
            $message = "No student found with this ID.";
            $msg_type = "error";
        }
        $stmt->close();

        // Also delete from users table (login record)
        $stmt2 = $conn->prepare("DELETE FROM users WHERE student_id = ?");
        $stmt2->bind_param("s", $deleteKey);
        $stmt2->execute();
        $stmt2->close();
    } else {
        $message = "Please enter Student ID.";
        $msg_type = "error";
    }
}

// Fetch all students
$result = $conn->query(
    "SELECT id, student_id, firstname, father_name, grandfather_name,
            college, department, `year`
     FROM users2
     ORDER BY student_id ASC"
);

// College name lookup
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

include 'header.php';
?>

<h2 class="page-title">🗑️ Delete Student</h2>

<?php if ($message !== ''): ?>
    <p class="alert-<?= $msg_type === 'success' ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<!-- DELETE FORM -->
<form method="POST"
      onsubmit="return confirm('Are you sure you want to delete this student?');"
      style="text-align:center; margin:20px 0;">
    <label style="font-weight:600; color:#2c3e50;">
        Enter Student ID to Delete:
    </label><br><br>
    <input type="text" name="deleteKey" placeholder="e.g. INU0800343UR" required
           style="padding:10px 14px; width:280px; border:1px solid #cbd5e1; border-radius:8px;">
    <input type="submit" value="🗑️ DELETE"
           style="padding:10px 24px; background:#dc2626; color:#fff;
                  border:none; border-radius:8px; cursor:pointer; font-weight:700;
                  letter-spacing:1px;">
</form>

<!-- TABLE OF ALL STUDENTS -->
<div style="overflow-x:auto; padding:10px;">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>First Name</th>
                <th>Father Name</th>
                <th>Grandfather Name</th>
                <th>College</th>
                <th>Department</th>
                <th>Year</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php $count = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $count++ ?></td>
                        <td><?= htmlspecialchars($row['student_id']) ?></td>
                        <td><?= htmlspecialchars($row['firstname'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['father_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['grandfather_name'] ?? '—') ?></td>
                        <td>
                            <?php
                            $code = $row['college'] ?? '';
                            echo htmlspecialchars($college_names[$code] ?? ($code ?: '—'));
                            ?>
                        </td>
                        <td><?= htmlspecialchars($row['department'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['year'] ?? '—') ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="no-data">No students found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>