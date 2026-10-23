<?php 
require 'db.php'; 

// ======================
// SEARCH
// ======================
$student_id = "";
$result     = null;
$gpa_result = null;

if (isset($_GET['student_id']) && trim($_GET['student_id']) !== '') {
    $student_id = trim($_GET['student_id']);

    $stmt = $conn->prepare(
        "SELECT * FROM users4
         WHERE student_id = ?
         ORDER BY academic_year DESC, semester ASC"
    );
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $stmt2 = $conn->prepare(
        "SELECT AVG(gpa) AS cumulative_gpa FROM users4 WHERE student_id = ?"
    );
    $stmt2->bind_param("s", $student_id);
    $stmt2->execute();
    $gpa_result = $stmt2->get_result();
}
?>

<h2 style="text-align:center; color:#2c3e50;">Search Student Grades</h2>

<!-- FORM: action points to user.php, hidden page keeps the sidebar active -->
<form method="GET" action="user.php" style="text-align:center; margin: 20px 0;">
    <input type="hidden" name="page" value="view">
    
    <label for="student_id"><strong>Student ID:</strong></label>
    <input type="text" name="student_id" id="student_id"
           value="<?= htmlspecialchars($student_id) ?>" 
           placeholder="Enter Student ID"
           required
           style="padding:8px; width:250px; font-size:15px; margin-left:10px;">
    <input type="submit" value="SEARCH" 
           style="padding:8px 20px; font-size:15px; cursor:pointer;">
</form>

<?php if ($result): ?>

    <h2 style="text-align:center; color:#2c3e50;">Student Grades</h2>

    <table border="1" cellpadding="8" cellspacing="0" 
           style="width:100%; border-collapse:collapse; margin-top:20px; background:white;">
        <thead style="background:#e0e0e0;">
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
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
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
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="color:#991b1b; font-weight:bold; text-align:center;">
                        No grades found for this student.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($gpa_result && $gpa_result->num_rows > 0): ?>
        <?php $gpa_row = $gpa_result->fetch_assoc(); ?>
        <h3 style="text-align:center; color:#2c3e50; margin-top:20px;">
            Cumulative GPA: <?= number_format($gpa_row['cumulative_gpa'], 2) ?>
        </h3>
    <?php endif; ?>

<?php endif; ?>