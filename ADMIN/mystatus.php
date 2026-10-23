<?php
/* ============================================================
   MY ACADEMIC STATUS — SGPA/CGPA per semester
   Shows ONLY approved grades (registrar-approved)
   ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

$student_id = $_SESSION['student_id'] ?? '';

/* ============================================================
   STEP 1 — GET ALL SEMESTERS (with approved grades only)
   ============================================================ */
$stmt = $conn->prepare("
    SELECT DISTINCT ac_year, semester
    FROM student_courses
    WHERE student_id = ?
      AND ac_year IS NOT NULL
      AND semester IS NOT NULL
      AND grade_status = 'Approved'
    ORDER BY ac_year ASC, semester ASC
");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$semesters = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* ============================================================
   STEP 2 — CALCULATE SGPA + CGPA FOR EACH SEMESTER
   ============================================================ */
$rows = [];
$running_points  = 0;
$running_credits = 0;
$sem_count       = 0;

foreach ($semesters as $sem) {

    $stmt = $conn->prepare("
        SELECT 
            sc.grade,
            sc.grade_point,
            sc.reg_date,
            sc.remark,
            sc.reg_type,
            a.credit_hour
        FROM student_courses sc
        INNER JOIN addcourse a ON a.course_code = sc.course_code
        WHERE sc.student_id = ?
          AND sc.ac_year = ?
          AND sc.semester = ?
          AND sc.grade_point IS NOT NULL
          AND sc.grade_status = 'Approved'
    ");
    $stmt->bind_param("ssi", $student_id, $sem['ac_year'], $sem['semester']);
    $stmt->execute();
    $grades = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $sem_points  = 0;
    $sem_credits = 0;
    $reg_date    = '';
    $remark      = 'Normal Load';
    $reg_type    = 'N';

    foreach ($grades as $g) {
        $cr = (int)$g['credit_hour'];
        $gp = (float)$g['grade_point'];
        $sem_points  += $gp * $cr;
        $sem_credits += $cr;

        if (!$reg_date)             $reg_date = $g['reg_date'];
        if (!empty($g['remark']))   $remark   = $g['remark'];
        if (!empty($g['reg_type'])) $reg_type = $g['reg_type'];
    }

    $sgpa = $sem_credits > 0 ? ($sem_points / $sem_credits) : 0.00;

    $running_points  += $sem_points;
    $running_credits += $sem_credits;
    $sem_count++;

    $cgpa = $running_credits > 0
          ? ($running_points / $running_credits)
          : 0.00;

    $status = '---';
    if ($sgpa >= 2.00)      $status = 'Pass';
    elseif ($sgpa > 0)      $status = 'Fail';

    $year_of_study = (int)ceil($sem_count / 2);

    $rows[] = [
        'ac_year'    => $sem['ac_year'],
        'year'       => $year_of_study,
        'semester'   => $sem['semester'],
        'reg_date'   => $reg_date ? date('d/m/Y', strtotime($reg_date)) : '—',
        'remark'     => $remark,
        'reg_type'   => $reg_type,
        'sgpa'       => round($sgpa, 2),
        'cgpa'       => round($cgpa, 2),
        'status'     => $status,
    ];
}

/* ============================================================
   LOAD VIEW
   ============================================================ */
include 'mystatusf.php';