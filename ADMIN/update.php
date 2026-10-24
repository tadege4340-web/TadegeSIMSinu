<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'db.php';

// Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: loginf.php");
    exit();
}

$error = '';
$success = '';

// Get the student ID from the URL (e.g., update.php?id=5)
$student_id_to_edit = $_GET['id'] ?? null;

if (!$student_id_to_edit) {
    header("Location: view.php");
    exit();
}

/* ============================================================
   1. FETCH STUDENT DATA (PDO)
   ============================================================ */
$stmt = $conn->prepare("SELECT * FROM users2 WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $student_id_to_edit]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student not found.");
}

/* ============================================================
   2. HANDLE FORM SUBMISSION (UPDATE)
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $father_name = trim($_POST['father_name']);
    $grandfather_name = trim($_POST['grandfather_name']);
    $college = trim($_POST['college']);
    $department = trim($_POST['department']);
    $year = (int)$_POST['year'];
    $semester = (int)$_POST['current_semester'];
    $phone = trim($_POST['phone']);

    try {
        $update_sql = "UPDATE users2 SET 
                        firstname = :fname, 
                        father_name = :father, 
                        grandfather_name = :gf, 
                        college = :college, 
                        department = :dept, 
                        `year` = :year, 
                        current_semester = :sem,
                        phone = :phone
                       WHERE id = :id";
        
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->execute([
            'fname' => $firstname,
            'father' => $father_name,
            'gf' => $grandfather_name,
            'college' => $college,
            'dept' => $department,
            'year' => $year,
            'sem' => $semester,
            'phone' => $phone,
            'id' => $student_id_to_edit
        ]);

        $success = "Student updated successfully!";
        
        // Refresh the data so the form shows the new values
        $stmt->execute(['id' => $student_id_to_edit]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $error = "Error updating student: " . $e->getMessage();
    }
}

include 'header.php';
?>

<div class="form-card" style="margin: 20px; max-width: 600px;">
    <h2>✏️ Update Student</h2>

    <?php if ($success): ?>
        <p class="alert-success">✅ <?= $success ?></p>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <p class="alert-error">❌ <?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Student ID (Cannot be changed)</label>
        <input type="text" value="<?= htmlspecialchars($student['student_id']) ?>" disabled>

        <label>First Name</label>
        <input type="text" name="firstname" value="<?= htmlspecialchars($student['firstname']) ?>" required>

        <label>Father Name</label>
        <input type="text" name="father_name" value="<?= htmlspecialchars($student['father_name']) ?>" required>

        <label>Grandfather Name</label>
        <input type="text" name="grandfather_name" value="<?= htmlspecialchars($student['grandfather_name']) ?>" required>

        <label>College</label>
        <input type="text" name="college" value="<?= htmlspecialchars($student['college']) ?>" required>

        <label>Department</label>
        <input type="text" name="department" value="<?= htmlspecialchars($student['department']) ?>" required>

        <label>Year</label>
        <input type="number" name="year" value="<?= htmlspecialchars($student['year']) ?>" min="1" max="7" required>

        <label>Current Semester</label>
        <select name="current_semester" required>
            <option value="1" <?= $student['current_semester'] == 1 ? 'selected' : '' ?>>Semester 1</option>
            <option value="2" <?= $student['current_semester'] == 2 ? 'selected' : '' ?>>Semester 2</option>
        </select>

        <label>Phone</label>
        <input type="tel" name="phone" value="<?= htmlspecialchars($student['phone'] ?? '') ?>">

        <br><br>
        <input type="submit" value="💾 Update Student" class="btn">
        <a href="view.php" style="margin-left: 15px;">Cancel</a>
    </form>
</div>

<?php include 'footer.php'; ?>