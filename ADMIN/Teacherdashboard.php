<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

$display_name = "Teacher"; 

// Check for the session ID
$session_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;

if ($session_id) {
    // Get the full name from the teachers table
    $sql = "SELECT full_name FROM teachers WHERE id = ? OR user_id = ? LIMIT 1";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $session_id, $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $display_name = htmlspecialchars($row['full_name']);
        }
        $stmt->close();
    }
}

// Load the HTML
include 'Teacherdashboardf.php';
?>