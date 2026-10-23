<?php include 'header.php'; ?>
<?php require 'db.php'; ?>

<h2 style="text-align:center; color:#2c3e50;">Search Student</h2>

<form method="POST" action="">
    <label>Enter Student ID, Name, Region, or School:</label>
    <input type="text" name="searchKey"
           value="<?= htmlspecialchars($_POST['searchKey'] ?? '') ?>"
           placeholder="ID / First / Father / Region / School" required>
    <input type="submit" name="search" value="SEARCH">
</form>

<?php
if (isset($_POST['search'])) {
    $key = trim($_POST['searchKey']);

    if ($key === '') {
        echo "<p style='color:#991b1b; text-align:center; font-weight:bold;'>Please enter a search term.</p>";
    } else {
        $like = '%' . $key . '%';

        $sql = "SELECT * FROM users2
                WHERE student_id        LIKE ?
                   OR firstname         LIKE ?
                   OR father_name       LIKE ?
                   OR grandfather_name  LIKE ?
                   OR region            LIKE ?
                   OR secondary_school  LIKE ?
                   OR department        LIKE ?
                ORDER BY student_id ASC";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("<p style='color:red;'>SQL Error: " . $conn->error . "</p>");
        }

        $stmt->bind_param("sssssss",
            $like, $like, $like, $like, $like, $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0):
?>
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
                    <th>Department</th>
                    <th>Year</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
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
                        <td><?= htmlspecialchars($row['department'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['year'] ?? '—') ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
<?php
        else:
            echo "<p style='color:#991b1b; text-align:center; font-weight:bold;'>
                    No student found matching: " . htmlspecialchars($key) . "
                  </p>";
        endif;

        $stmt->close();
    }
}
?>

<?php include 'footer.php'; ?>