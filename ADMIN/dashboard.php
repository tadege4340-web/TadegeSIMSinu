<?php
// Student Dashboard — quick overview
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

$student_id = $_SESSION['student_id'] ?? '';
$student = [];
$course_count = 0;

try {
    // Get the student's info (PDO)
    $stmt = $conn->prepare("SELECT firstname, father_name, college, department, `year` FROM users2 WHERE student_id = :sid LIMIT 1");
    $stmt->execute(['sid' => $student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    // Count assigned courses (PDO)
    $stmt = $conn->prepare("SELECT COUNT(*) AS c FROM student_courses WHERE student_id = :sid");
    $stmt->execute(['sid' => $student_id]);
    $course_count = (int)$stmt->fetchColumn();

} catch (PDOException $e) {
    // Handle error silently
}
?>

<h2 style="color:#2c3e50;">📊 Dashboard</h2>

<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-top:20px;">

    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <div style="font-size:11px; font-weight:700; color:#64748b; letter-spacing:1px; text-transform:uppercase; margin-bottom:8px;">
            Student ID
        </div>
        <div style="font-size:20px; font-weight:800; color:#1e3a5f;">
            <?= htmlspecialchars($student_id) ?>
        </div>
    </div>

    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <div style="font-size:11px; font-weight:700; color:#64748b; letter-spacing:1px; text-transform:uppercase; margin-bottom:8px;">
            College
        </div>
        <div style="font-size:16px; font-weight:700; color:#1e3a5f;">
            <?= htmlspecialchars($student['college'] ?? '—') ?>
        </div>
    </div>

    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <div style="font-size:11px; font-weight:700; color:#64748b; letter-spacing:1px; text-transform:uppercase; margin-bottom:8px;">
            Department
        </div>
        <div style="font-size:16px; font-weight:700; color:#1e3a5f;">
            <?= htmlspecialchars($student['department'] ?? '—') ?>
        </div>
    </div>

    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <div style="font-size:11px; font-weight:700; color:#64748b; letter-spacing:1px; text-transform:uppercase; margin-bottom:8px;">
            Year
        </div>
        <div style="font-size:20px; font-weight:800; color:#1e3a5f;">
            <?= htmlspecialchars($student['year'] ?? '—') ?>
        </div>
    </div>

    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <div style="font-size:11px; font-weight:700; color:#64748b; letter-spacing:1px; text-transform:uppercase; margin-bottom:8px;">
            Assigned Courses
        </div>
        <div style="font-size:20px; font-weight:800; color:#1e3a5f;">
            <?= number_format($course_count) ?>
        </div>
    </div>

</div>

<div style="margin-top:30px; padding:20px; background:#f0f9ff; border-left:4px solid #0284c7; border-radius:10px;">
    <h3 style="margin:0 0 8px; color:#0c4a6e; font-size:15px;">👋 Welcome to your Dashboard</h3>
    <p style="margin:0; color:#475569; font-size:14px; line-height:1.6;">
        Use the menu on the left to view your courses, grades, status, and other academic information.
    </p>
</div>