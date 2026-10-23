<?php include 'header.php'; ?>
<?php require 'db.php'; ?>

<h2 style="text-align:center; color:#2c3e50;">View Courses</h2>

<?php
$sql = "SELECT course_code, course_name, credit_hour, category
        FROM addcourse
        ORDER BY course_code ASC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error fetching courses: " . mysqli_error($conn));
}
?>

<table>
    <thead>
        <tr>
            <th>Course Code</th>
            <th>Course Name</th>
            <th>Credit Hour</th>
            <th>Category</th>
        </tr>
    </thead>

    <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['course_code']) ?></td>
                    <td><?= htmlspecialchars($row['course_name']) ?></td>
                    <td><?= htmlspecialchars($row['credit_hour']) ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No courses found in the database.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div style="text-align:center; margin-top:25px;">
    <a href="mycourseadmin.php"
       style="display:inline-block; text-decoration:none; background:#2c3e50;
              color:#fff; padding:12px 22px; border-radius:5px;">
        ADD NEW COURSE
    </a>
</div>

<?php include 'footer.php'; ?>