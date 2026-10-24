<?php
session_start();
require 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginf.php");
    exit();
}

// Fetch all students using PDO
$stmt = $conn->prepare("SELECT * FROM users2 ORDER BY id DESC");
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<div class="admin-table-card" style="margin: 20px;">
    <h2>👨‍🎓 View Students</h2>

    <?php if (empty($students)): ?>
        <p style="padding: 20px; text-align: center;">No students found in the database.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>College</th>
                    <th>Department</th>
                    <th>Year</th>
                    <th>Semester</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($student['student_id']) ?></strong></td>
                    <td><?= htmlspecialchars(trim(($student['firstname'] ?? '') . ' ' . ($student['father_name'] ?? '') . ' ' . ($student['grandfather_name'] ?? ''))) ?></td>
                    <td><?= htmlspecialchars($student['college'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($student['department'] ?? '—') ?></td>
                    <td>Year <?= htmlspecialchars($student['year'] ?? '—') ?></td>
                    <td>Sem <?= htmlspecialchars($student['current_semester'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($student['gender'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($student['phone'] ?? '—') ?></td>
                    <td>
                        <a href="update.php?id=<?= $student['id'] ?>" style="color: blue;">Edit</a> | 
                        <a href="delete.php?id=<?= $student['id'] ?>" style="color: red;" onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>