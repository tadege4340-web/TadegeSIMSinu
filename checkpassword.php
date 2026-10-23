<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'db.php';

$username     = 'tadege';
$new_password = '1234567';
$hash         = password_hash($new_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "UPDATE users
     SET password = ?,
         role = 'admin',
         must_change_password = 0
     WHERE username = ?");
$stmt->bind_param("ss", $hash, $username);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "✅ Password for <b>$username</b> reset to <b>$new_password</b><br><br>";
    echo "New hash:<br><code>" . htmlspecialchars($hash) . "</code>";
} else {
    echo "❌ User '$username' not found, or no change.";
}

$stmt->close();
$conn->close();
?>