<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Injibara University - Student Portal</title>
    <link rel="stylesheet" href="user1.css">
</head>
<body>

    <!-- WELCOME BAR -->
    <div class="welcome-bar">
        Welcome, <?= htmlspecialchars($welcome_name) ?>!
    </div>

    <!-- LOGO + UNIVERSITY TITLE -->
    <div class="first">
        <div class="second">
            <img src="logoinu.jpg" alt="Injibara University Logo">
        </div>
        <div class="third">
            <h1>
                INJIBARA UNIVERSITY<br>
                STUDENT INFORMATION MANAGEMENT SYSTEM
            </h1>
        </div>
    </div>

    <!-- NAVIGATION BAR -->
    <div class="fivth">
        <a href="a.php">HOME</a>
        <a href="B.php">CONTACT US</a>
        <a href="C.php">NEWS</a>
        <a href="logout.php" class="logout-link">LOGOUT</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">