<?php
session_start(); 

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';

// ============ GET FULL NAME (FIRST + FATHER) ============
$student_id  = $_SESSION['student_id'];
$firstname   = '';
$father_name = '';

$stmt = $conn->prepare("SELECT firstname, father_name FROM users2 WHERE student_id = ? LIMIT 1");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$name_result = $stmt->get_result();

if ($row = $name_result->fetch_assoc()) {
    $firstname   = $row['firstname']   ?? '';
    $father_name = $row['father_name'] ?? '';
}
$stmt->close();

// Build display name
$full_name = trim($firstname . ' ' . $father_name);

// Fallback if users2 has no row
if ($full_name === '') {
    $full_name = $_SESSION['full_name'] ?? 'Student';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Injibara University Menu</title>
    <link rel="stylesheet" href="user1.css">
</head>

<body>

    <!-- HEADER (FIXED) -->
    <div class="first">
        <div class="second">
            <img src="logoinu.jpg" width="100" height="100" alt="Logo">
        </div>
        <div class="third">
            <h1>INJIBARA UNIVERSITY<br>STUDENT INFORMATION MANAGEMENT SYSTEM</h1>
        </div>
    </div>

    <!-- TOP BAR WITH LOGOUT (FIXED) -->
    <div class="fivth">
        <a href="logout.php" class="logout-btn">LOGOUT</a>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="main-container">
        
        <!-- LEFT SIDE: MENU (FIXED) -->
        <div class="add">
            <a href="user.php?page=dashboard" class="btn">Dashboard</a>
            <a href="user.php?page=mycourse" class="btn">My Course</a>
            <a href="user.php?page=mygrade" class="btn">My Grade</a>
            <a href="user.php?page=mystatus" class="btn">My status</a>
            <a href="user.php?page=mydormitory" class="btn">My Dormitory</a>
            <a href="user.php?page=placement" class="btn">Placement Tutorial</a>
            <a href="user.php?page=graduated" class="btn">Graduated Students</a>
            <a href="user.php?page=field" class="btn">Field Selection</a>
            <a href="user.php?page=selection" class="btn">Selection Result</a>
        </div>

        <!-- RIGHT SIDE: CONTENT AREA (SCROLLABLE) -->
        <div class="content-area">

            <!-- WELCOME MESSAGE -->
            <div class="welcome-msg">
                 Welcome, <?php echo htmlspecialchars($full_name); ?>!
            </div>

            <hr>

            <?php
            /* ============ DEFAULT PAGE IS NOW 'dashboard' ============ */
            $page = $_GET['page'] ?? 'dashboard';

            if ($page == 'dashboard')       include('dashboard.php');
            elseif ($page == 'mycourse')    include('mycourse.php');
            elseif ($page == 'mygrade')     include('mygrade.php');
            elseif ($page == 'mystatus')    include('mystatus.php');
            elseif ($page == 'mydormitory') include('mydormitory.php');
            elseif ($page == 'placement')   include('placement.php');
            elseif ($page == 'graduated')   include('graduated.php');
            elseif ($page == 'field')       include('field.php');
            elseif ($page == 'selection')   include('selection.php');
            else                            echo "<h2>Page not found.</h2>";
            ?>
        </div> 
    </div> 

</body>
</html>