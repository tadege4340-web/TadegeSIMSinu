<?php
session_start();
require 'db.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: loginf.php");
    exit();
}

$teacher_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;
$message = '';
$message_type = 'success';

// ============================================================
// HANDLE SAVE GRADES (POST)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_grades') {
    $course_code = $_POST['course_code'] ?? '';
    $grades = $_POST['grades'] ?? [];

    try {
        $conn->beginTransaction();
        $count = 0;

        foreach ($grades as $student_id => $data) {
            $grade = strtoupper(trim($data['grade'] ?? ''));
            $gp = $data['gp'] ?? null;

            if ($grade !== '') {
                // Update the student's grade (PDO)
                $stmt = $conn->prepare("UPDATE student_courses 
                                        SET grade = :grade, grade_point = :gp, grade_status = 'Pending', submitted_at = NOW() 
                                        WHERE student_id = :sid AND course_code = :cc");
                $stmt->execute(['grade' => $grade, 'gp' => $gp, 'sid' => $student_id, 'cc' => $course_code]);
                $count++;
            }
        }
        $conn->commit();
        $message = "✅ $count grade(s) saved successfully! They are now Pending for Head approval.";
    } catch (PDOException $e) {
        $conn->rollBack();
        $message = "❌ Error saving grades: " . $e->getMessage();
        $message_type = 'error';
    }
}

// ============================================================
// FETCH TEACHER'S ASSIGNED COURSES
// ============================================================
$my_courses = [];
try {
    // Note: We match by user_id or id depending on how your teacher is logged in
    $stmt = $conn->prepare("
        SELECT tc.course_id, tc.course_code, c.course_name, c.course_year, c.course_semester 
        FROM teacher_courses tc
        JOIN addcourse c ON tc.course_id = c.course_id
        WHERE tc.teacher_id = :tid
    ");
    $stmt->execute(['tid' => $teacher_id]);
    $my_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error loading courses: " . $e->getMessage();
}

// ============================================================
// FETCH STUDENTS FOR SELECTED COURSE
// ============================================================
$f_course_id = (int)($_GET['course_id'] ?? 0);
$students_with_grades = [];

if ($f_course_id > 0) {
    try {
        $stmt = $conn->prepare("
            SELECT sc.student_id, sc.grade, sc.grade_point, sc.grade_status, u.firstname, u.father_name
            FROM student_courses sc
            JOIN users2 u ON sc.student_id = u.student_id
            WHERE sc.course_id = :cid
            ORDER BY u.firstname ASC
        ");
        $stmt->execute(['cid' => $f_course_id]);
        $students_with_grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message = "Error loading students: " . $e->getMessage();
    }
}

// Load the HTML view
include 'Teacheraddgradef.php';
?>