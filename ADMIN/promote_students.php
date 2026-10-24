<?php
require 'db.php';

try {
    // Example: Move all Year 3 Sem 1 students to Year 3 Sem 2
    $sql = "UPDATE users2 SET current_semester = 2 WHERE year = 3 AND current_semester = 1";
    
    // Prepare and execute the statement securely
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    // Check how many students were actually promoted
    $affected_rows = $stmt->rowCount();
    
    echo "✅ Success! $affected_rows student(s) promoted to Semester 2!";
    
} catch (PDOException $e) {
    // If something goes wrong (like a table missing), show a friendly error
    echo "❌ Database Error: " . $e->getMessage();
}
?>