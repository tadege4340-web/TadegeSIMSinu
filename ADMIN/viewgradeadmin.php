<?php include 'header.php'; ?>
<?php require 'db.php'; ?>

<?php
// ======================
// SEARCH (optional filter)
// ======================
$student_id = trim($_GET['student_id'] ?? '');
$grades = []; // Array to hold the results

try {
    if ($student_id !== '') {
        // Filter by student ID (PDO)
        $stmt = $conn->prepare(
            "SELECT * FROM users4
             WHERE student_id = :sid
             ORDER BY academic_year DESC, semester ASC"
        );
        $stmt->execute(['sid' => $student_id]);
    } else {
        // No filter — show ALL grades (PDO)
        $stmt = $conn->prepare(
            "SELECT * FROM users4
             ORDER BY student_id ASC, academic_year DESC, semester ASC"
        );
        $stmt->execute();
    }
    
    $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}
?>

<h2 style="text-align:center; color:#2c3e50;">All Student Grades</h2>

<form method="GET" action="">
    <label for="student_id">Filter by Student ID (optional):</label>
    <input type="text" name="student_id" id="student_id"
           value="<?= htmlspecialchars($student_id) ?>"
           placeholder="Leave empty to show all">
    <input type="submit" value="SEARCH">
</form>

<?php if ($student_id !== ''): ?>
    <p style="text-align:center; color:#2c3e50; font-weight:bold;">
        Showing results for: <?= htmlspecialchars($student_id) ?>
        — <a href="viewgrade.php">show all</a>
    </p>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Username</th>
            <th>Student ID</th>
            <th>College</th>
            <th>Department</th>
            <th>Course ID</th>
            <th>Grade</th>
            <th>Semester</th>
            <th>Academic Year</th>
            <th>GPA</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($grades)): ?>
            <?php foreach ($grades as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['student_id']) ?></td>
                    <td><?= htmlspecialchars($row['college']) ?></td>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td><?= htmlspecialchars($row['course_id']) ?></td>
                    <td><?= htmlspecialchars($row['grade']) ?></td>
                    <td><?= htmlspecialchars($row['semester']) ?></td>
                    <td><?= htmlspecialchars($row['academic_year']) ?></td>
                    <td><?= htmlspecialchars($row['gpa']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" style="color:#991b1b; font-weight:bold;">
                    No grades found<?= $student_id !== '' ? ' for this student' : '' ?>.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
// ======================
// CUMULATIVE GPA (only when filtering by student) - PDO
// ======================
if ($student_id !== '') {
    try {
        $stmt2 = $conn->prepare("SELECT AVG(gpa) AS cumulative_gpa FROM users4 WHERE student_id = :sid");
        $stmt2->execute(['sid' => $student_id]);
        $gpa_row = $stmt2->fetch(PDO::FETCH_ASSOC);

        if ($gpa_row && $gpa_row['cumulative_gpa'] !== null) {
            echo '<h3 style="text-align:center; color:#2c3e50; margin-top:20px;">'
                 . 'Cumulative GPA: ' . number_format($gpa_row['cumulative_gpa'], 2)
                 . '</h3>';
        }
    } catch (PDOException $e) {
        // Ignore error if average cannot be calculated
    }
}
?>

<?php include 'footer.php'; ?>