<?php
session_start();
require 'db.php';

// Security check
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'user'])) {
    header("Location: loginf.php");
    exit();
}

$student_id = $_SESSION['student_id'] ?? null;
$search_query = trim($_GET['q'] ?? '');
$rows = [];

if ($student_id) {
    try {
        // Base query to get the student's courses and grades
        $sql = "SELECT 
                    sc.ac_year, 
                    c.course_year AS year, 
                    sc.semester, 
                    sc.reg_date, 
                    sc.remark, 
                    sc.reg_type, 
                    sc.grade, 
                    sc.grade_point,
                    sc.status
                FROM student_courses sc
                JOIN addcourse c ON sc.course_id = c.course_id
                WHERE sc.student_id = :student_id";

        // Add search filter if user typed something
        if ($search_query !== '') {
            $sql .= " AND (sc.ac_year LIKE :search OR sc.course_code LIKE :search OR c.course_name LIKE :search)";
        }

        $sql .= " ORDER BY sc.ac_year DESC, sc.semester DESC";

        $stmt = $conn->prepare($sql);
        $params = ['student_id' => $student_id];
        
        if ($search_query !== '') {
            $params['search'] = '%' . $search_query . '%';
        }

        $stmt->execute($params);
        $raw_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate SGPA and CGPA dynamically (simple example)
        $total_points = 0;
        $total_credits = 0;

        foreach ($raw_rows as $row) {
            // For simplicity, let's assume we calculate running totals here.
            // In a real system, you'd group by semester for SGPA.
            
            $sgpa = (float)$row['grade_point']; 
            $cgpa = 0; // You would calculate this properly in a real system

            $rows[] = [
                'ac_year' => $row['ac_year'],
                'year' => $row['year'],
                'semester' => $row['semester'],
                'reg_date' => $row['reg_date'],
                'remark' => $row['remark'],
                'reg_type' => $row['reg_type'],
                'sgpa' => $sgpa,
                'cgpa' => $cgpa,
                'status' => $row['status']
            ];
        }

    } catch (PDOException $e) {
        $error_message = "Error loading records: " . $e->getMessage();
    }
}

// Load the HTML view
include 'mystatusf.php';
?>