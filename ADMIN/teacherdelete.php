<?php include 'header.php'; ?>
<?php require 'db.php'; ?>

<?php
$message = "";
$messageType = "error";

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['deleteKey'])) {

    $deleteKey = trim($_POST['deleteKey']);

    try {
        // Delete by ID OR Email (PDO)
        $stmt = $conn->prepare("DELETE FROM teachers WHERE id = :id OR email = :email");
        $stmt->execute(['id' => $deleteKey, 'email' => $deleteKey]);

        if ($stmt->rowCount() > 0) {
            $message = "✅ Teacher deleted successfully!";
            $messageType = "success";
        } else {
            $message = "❌ No teacher found with ID or Email: " . htmlspecialchars($deleteKey);
        }
    } catch (PDOException $e) {
        $message = "❌ Database Error: " . $e->getMessage();
    }
}

/* ============ FETCH ALL TEACHERS (PDO) ============ */
$stmt = $conn->query("SELECT * FROM teachers ORDER BY id ASC");
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

<h2 style="text-align:center; color:#2c3e50;">Delete Teacher</h2>

<?php if ($message !== ''): ?>
    <p style="text-align:center; padding:10px; border-radius:6px; font-weight:bold;
              background:<?= $messageType === 'success' ? '#d1fae5' : '#fee2e2' ?>;
              color:<?= $messageType === 'success' ? '#065f46' : '#991b1b' ?>;">
        <?= $message ?>
    </p>
<?php endif; ?>

<!-- Delete by typing ID or Email -->
<form method="POST" style="text-align:center; margin:20px;">
    <label>Enter Teacher ID or Email to Delete:</label><br><br>
    <input type="text" name="deleteKey" placeholder="ID or Email" required
           style="padding:8px; width:250px;">
    <input type="submit" value="DELETE"
           style="padding:8px 20px; background:#c0392b; color:#fff;
                  border:none; border-radius:5px; cursor:pointer;">
</form>

<h2 style="text-align:center; color:#2c3e50; margin-top:30px;">All Teachers</h2>

<div style="overflow-x:auto; width:98%; margin:auto;">
<table border="1" cellpadding="8" cellspacing="0"
       style="margin:20px auto; border-collapse:collapse; width:100%; text-align:center; font-size:14px;">
    <thead style="background:#2c3e50; color:white;">
        <tr>
            <th>NO</th>
            <th>ID</th>
            <th>Full Name</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>College</th>
            <th>Department</th>
            <th>Qualification</th>
            <th>Position</th>
            <th>Hire Date</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($teachers)): ?>
            <?php $count = 1; ?>
            <?php foreach ($teachers as $row): ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['age']) ?></td>
                    <td><?= htmlspecialchars($row['gender']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['address'] ?? '') ?></td>
                    <td>
                        <?php
                        $code = $row['college'] ?? '';
                        echo htmlspecialchars($college_names[$code] ?? ($code ?: '—'));
                        ?>
                    </td>
                    <td><?= htmlspecialchars($row['department'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['qualification'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['position'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['hire_date'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['status'] ?? '') ?></td>
                    <td>
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="deleteKey"
                                   value="<?= htmlspecialchars($row['id']) ?>">
                            <button type="submit"
                                    onclick="return confirm('Delete teacher <?= htmlspecialchars($row['full_name']) ?> (ID: <?= htmlspecialchars($row['id']) ?>)?');"
                                    style="background:#c0392b; color:#fff; border:none;
                                           padding:6px 14px; border-radius:4px;
                                           cursor:pointer; font-weight:bold;">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="15" style="color:red;">No teachers found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>
</div>

<?php include 'footer.php'; ?>