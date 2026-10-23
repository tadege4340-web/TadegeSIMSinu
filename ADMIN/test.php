<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'db.php';

echo "<h1>🔍 Diagnostic Test</h1>";

// 1. Check Session
echo "<h3>1. Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

$hod_dept = $_SESSION['department'] ?? 'NOT SET';
echo "<p><strong>HOD Department in Session:</strong> <code>" . htmlspecialchars($hod_dept) . "</code></p>";

// 2. Check Database Connection
echo "<h3>2. Database Connection:</h3>";
if ($conn) {
    echo "<p style='color:green;'>✅ Connected successfully to database.</p>";
} else {
    die("<p style='color:red;'>❌ Database connection failed.</p>");
}

// 3. Check all students
echo "<h3>3. All Students in 'users2' table:</h3>";
$res = $conn->query("SELECT student_id, firstname, department FROM users2 LIMIT 10");
if ($res && $res->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Department</th></tr>";
    while($row = $res->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
        echo "<td>" . htmlspecialchars($row['department']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>❌ No students found in database OR query failed: " . $conn->error . "</p>";
}

// 4. Check the exact query Headview.php uses
echo "<h3>4. Query Test (Filtering by HOD Department):</h3>";
$stmt = $conn->prepare("SELECT student_id, firstname FROM users2 WHERE department = ?");
$stmt->bind_param("s", $hod_dept);
$stmt->execute();
$res2 = $stmt->get_result();

echo "<p>Found <strong>" . $res2->num_rows . "</strong> students matching '<strong>" . htmlspecialchars($hod_dept) . "</strong>'</p>";
if ($res2->num_rows > 0) {
    echo "<p style='color:green;'>✅ The query works! The issue is with the HTML/CSS layout, not the data.</p>";
} else {
    echo "<p style='color:red;'>❌ The query found 0 students. This means the department in the session does NOT match the department in the database exactly.</p>";
}
?>