<?php
// Safely start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: loginf.php");
    exit();
}

$teacher_user_id = $_SESSION['user_id'] ?? 0;
$teacher_name = $_SESSION['full_name'] ?? 'Teacher';

// 1. Fetch Stats for this Teacher
$total_courses = 0;
$total_students = 0;
$total_pending_grades = 0;

try {
    // Count assigned courses
    $stmt = $conn->prepare("SELECT COUNT(*) FROM teacher_courses WHERE teacher_id = :tid");
    $stmt->execute(['tid' => $teacher_user_id]);
    $total_courses = (int)$stmt->fetchColumn();

    // Count unique students taking their courses
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT sc.student_id) 
        FROM student_courses sc
        JOIN teacher_courses tc ON sc.course_id = tc.course_id
        WHERE tc.teacher_id = :tid
    ");
    $stmt->execute(['tid' => $teacher_user_id]);
    $total_students = (int)$stmt->fetchColumn();

    // Count pending grades
    $stmt = $conn->prepare("
        SELECT COUNT(sc.id) 
        FROM student_courses sc
        JOIN teacher_courses tc ON sc.course_id = tc.course_id
        WHERE tc.teacher_id = :tid AND sc.grade_status = 'Pending'
    ");
    $stmt->execute(['tid' => $teacher_user_id]);
    $total_pending_grades = (int)$stmt->fetchColumn();

} catch (PDOException $e) {
    // Handle errors silently
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="Teacherdashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; padding: 20px; }
        .welcome-box { background: #fff; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .welcome-box h2 { color: #17365d; margin-bottom: 5px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .stat-card { background: #fff; padding: 25px; border-radius: 10px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-top: 5px solid #1e4e79; }
        .stat-card i { font-size: 40px; color: #1e4e79; margin-bottom: 15px; }
        .stat-card h3 { font-size: 32px; color: #17365d; margin: 0; }
        .stat-card p { color: #64748b; font-size: 14px; margin-top: 5px; }
    </style>
</head>
<body>

    <div class="welcome-box">
        <h2>Welcome back, <?= htmlspecialchars($teacher_name) ?>!</h2>
        <p>Here is a quick overview of your assigned classes and students.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-book-open"></i>
            <h3><?= number_format($total_courses) ?></h3>
            <p>Assigned Courses</p>
        </div>

        <div class="stat-card">
            <i class="fas fa-users"></i>
            <h3><?= number_format($total_students) ?></h3>
            <p>My Students</p>
        </div>

        <div class="stat-card" style="border-top-color: #d97706;">
            <i class="fas fa-clock" style="color: #d97706;"></i>
            <h3><?= number_format($total_pending_grades) ?></h3>
            <p>Grades to Submit</p>
        </div>
    </div>

</body>
</html>