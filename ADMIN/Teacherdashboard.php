<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'db.php'; // Use require for the database connection

// Security Check: Only allow teachers
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: loginf.php");
    exit();
}

$display_name = "Teacher"; 

// Check for the session ID
$session_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;

if ($session_id) {
    try {
        // Get the full name from the teachers table using PDO
        $sql = "SELECT full_name FROM teachers WHERE id = :id1 OR user_id = :id2 LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        // We bind the same ID to both placeholders
        $stmt->execute(['id1' => $session_id, 'id2' => $session_id]);
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            $display_name = htmlspecialchars($row['full_name']);
        }
    } catch (PDOException $e) {
        // If there is an error, it will just keep the default "Teacher" name
    }
}

// Load the HTML
include 'Teacherdashboardf.php';
?>