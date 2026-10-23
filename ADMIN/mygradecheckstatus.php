<?php
/* ============================================================
   AJAX — Check grade status of an enrollment
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

header('Content-Type: application/json');

$student_id  = trim($_POST['student_id']  ?? '');
$course_code = trim($_POST['course_code'] ?? '');
$ac_year     = trim($_POST['ac_year']     ?? '');
$semester    = (int)($_POST['semester']   ?? 0);

if ($student_id === '' || $course_code === '' || $ac_year === '' || $semester === 0) {
    echo json_encode(['found' => false]);
    exit();
}

$stmt = $conn->prepare("
    SELECT grade, grade_point, grade_status,
           last_edited_by, last_edited_at, approved_by
    FROM student_courses 
    WHERE student_id = ? 
      AND course_code = ? 
      AND ac_year = ? 
      AND semester = ?
    LIMIT 1");
$stmt->bind_param("sssi", $student_id, $course_code, $ac_year, $semester);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['found' => false]);
    exit();
}

echo json_encode([
    'found'          => true,
    'grade'          => $row['grade'],
    'grade_point'    => $row['grade_point'],
    'grade_status'   => $row['grade_status'] ?? 'Pending',
    'last_edited_by' => $row['last_edited_by'],
    'last_edited_at' => $row['last_edited_at'],
    'approved_by'    => $row['approved_by'],
]);