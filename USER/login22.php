<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Injibara University Admin</title>
    <link rel="stylesheet" href="user/css22.css">
</head>
<body>

    <!-- UNIVERSITY HEADER -->
    <div class="first">
        <div class="second">
            <img src="logoinu.jpg" width="100" height="100" alt="Injibara University Logo">
        </div>
        <div class="third">
            <h1>INJIBARA UNIVERSITY<br>STUDENT INFORMATION MANAGEMENT SYSTEM</h1>
        </div>
    </div>

    <!-- TOP MENU (empty) -->
    <div class="fivth"></div>

    <!-- LEFT ADMIN SIDEBAR -->
    <div class="admin-sidebar">
        <a href="teacherform.php"         target="contentFrame">TEACHER</a>
        <a href="gradeform.php"           target="contentFrame">ADD GRADE</a>
        <a href="viewgrade.php"           target="contentFrame">VIEW GRADE</a>
        <a href="addf.php"                target="contentFrame">ADD STUDENT</a>
        <a href="view.php"                target="contentFrame">VIEW STUDENT</a>
        <a href="update.php"              target="contentFrame">UPDATE STUDENT</a>
        <a href="delete.php"              target="contentFrame">DELETE STUDENT</a>
        <a href="search.php"              target="contentFrame">SEARCH STUDENT</a>
        <a href="mycourseadmin.php"       target="contentFrame">ADD COURSES</a>
        <a href="mycourseassignadmin.php" target="contentFrame">ASSIGN COURSE</a>
        <a href="mycourseviewadn.php"     target="contentFrame">VIEW COURSES</a>
        <a href="logout.php" class="logout-link">LOGOUT</a>
    </div>

    <!-- MAIN CONTENT (iframe) -->
    <div class="main-content">
        <iframe name="contentFrame" src="welcome.php" frameborder="0"></iframe>
    </div>

</body>
</html>