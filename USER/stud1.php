<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Injibara University - SIMS</title>
    <link rel="stylesheet" href="css.css">

    <style>
        /* Extra styles for the new sections */
        .info-section {
            max-width: 1100px;
            margin: 30px auto;
            padding: 25px;
            background: #f8fafc;
            border-left: 6px solid #1abc9c;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            font-size: 15px;
            line-height: 1.7;
            color: #2c3e50;
        }
        .info-section h2 {
            color: #17365d;
            border-bottom: 2px solid #1abc9c;
            padding-bottom: 8px;
            margin-bottom: 18px;
        }
        .info-section h3 {
            color: #1abc9c;
            margin-top: 22px;
            margin-bottom: 10px;
        }
        .info-section ul {
            padding-left: 25px;
        }
        .info-section ul li {
            margin-bottom: 6px;
        }
        .info-section table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 14px;
        }
        .info-section th, .info-section td {
            border: 1px solid #cbd5e1;
            padding: 10px;
            text-align: left;
        }
        .info-section th {
            background: #17365d;
            color: white;
        }
        .info-section tr:nth-child(even) {
            background: #f1f5f9;
        }
        .highlight-note {
            background: #fef3c7;
            border-left: 5px solid #d97706;
            padding: 12px 16px;
            margin: 15px 0;
            border-radius: 5px;
            color: #78350f;
        }

        /* Cards for Colleges / Courses / Departments */
        .cards-wrapper {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-top: 5px solid #1abc9c;
        }
        .card h4 {
            color: #17365d;
            margin-top: 0;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
        }
        .card ul {
            padding-left: 20px;
            margin: 0;
        }
        .card ul li {
            margin-bottom: 4px;
            font-size: 14px;
        }
    </style>
</head>
<body>

<!-- ===== HEADER ===== -->
<div class="first">
    <div class="second">
        <img src="logoinu.jpg" width="100" height="100" alt="Injibara University Logo">
    </div>
    <div class="third">
        <h1>INJIBARA UNIVERSITY<br>STUDENT INFORMATION MANAGEMENT SYSTEM</h1>
    </div>
    <div class="fourth">
        <a href="loginf.php" target="frame4">LOGIN</a>
    </div>
</div>

<!-- ===== NAVIGATION ===== -->
<div class="fivth">
    <a href="a.php">HOME</a>
    <a href="B.php">CONTACT US</a>
    <a href="C.php">NEWS</a>
</div>

<!-- ===== WELCOME ===== -->
<h1 style="background-color:aqua; text-align:center;">WELCOME TO SIMS</h1>

<table border="0" width="100%">
    <tr>
        <td width="180">
            <img src="image.png" alt="SIMS image">
        </td>
        <td colspan="3">
            <h1>WELCOME TO STUDENT INFORMATION MANAGEMENT SYSTEM (SIMS)</h1>
        </td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>
            <h3>
                The Student Information Management System (SIMS) is a software application
                designed to manage and streamline the student data and administrative
                processes in educational institutions. It ensures the accurate storage,
                retrieval, and management of student information, including personal
                details, academic records, course enrollment, and results.
                <br><br>
                Traditionally, student information is maintained manually, which is prone
                to errors, data loss, and inefficiency. The proposed system aims to
                automate these tasks to save time, improve accuracy, and support
                decision-making. It is an Integrated Software that automates the following
                Student related Business Processes of a University like:
            </h3>
        </td>
    </tr>
</table>

<ul>
    <li>COLLEGES</li>
    <li>COURSES</li>
    <li>DEPARTMENT</li>
    <li>TEACHERS</li>
    <li>STUDENTS</li>
</ul>

<!-- ===== NEW: COLLEGES / COURSES / DEPARTMENTS ===== -->
<div class="info-section">
    <h2>🏛️ Colleges, Courses &amp; Departments</h2>

    <p>Injibara University is organized into several colleges, each offering multiple
       departments and a wide range of courses. Below is a summary of the academic
       structure available in SIMS.</p>

    <div class="cards-wrapper">

        <!-- Colleges -->
        <div class="card">
            <h4>🏫 Colleges</h4>
            <ul>
                <li>College of Engineering</li>
                <li>College of Natural Sciences</li>
                <li>College of Social Sciences</li>
                <li>College of Business and Economics</li>
                <li>College of Agriculture</li>
                <li>College of Medical and Health Sciences</li>
            </ul>
        </div>

        <!-- Departments -->
        <div class="card">
            <h4>🏢 Departments</h4>
            <ul>
                <li>Computer Science</li>
                <li>Software Engineering</li>
                <li>Information Technology</li>
                <li>Information Systems</li>
                <li>Electrical &amp; Computer Engineering</li>
                <li>Civil Engineering</li>
                <li>Economics</li>
                <li>Accounting</li>
                <li>Management</li>
                <li>Agriculture</li>
                <li>Nursing</li>
                <li>Public Health</li>
            </ul>
        </div>

        <!-- Courses -->
        <div class="card">
            <h4>📚 Sample Courses</h4>
            <ul>
                <li>CS101 – Introduction to Computing</li>
                <li>CS201 – Data Structures</li>
                <li>CS301 – Web Programming</li>
                <li>SE201 – Software Design</li>
                <li>IT210 – Networking</li>
                <li>ECON101 – Microeconomics</li>
                <li>ACC201 – Financial Accounting</li>
                <li>MGT101 – Principles of Management</li>
                <li>MATH101 – Calculus I</li>
                <li>PHYS101 – General Physics</li>
            </ul>
        </div>

    </div>
</div>

<!-- ===== NEW: STUDENT INFORMATION ===== -->
<div class="info-section">
    <h2>📢 Dear Injibara University Students,</h2>

    <p>To follow-up your grade and related information
       <a href="loginf.php"><b>click here</b></a>.</p>

    <p><b>This System is launched for students to access the following information:</b></p>
    <ul>
        <li>All courses in his/her curriculum</li>
        <li>Academic Status — detailed academic status starting from Year 1, Semester 1, ...</li>
        <li>Detail about taken courses, exempted courses, waived courses, ...</li>
        <li>Dormitory placement</li>
        <li>Other important information</li>
    </ul>

    <h3>🔐 Login Guideline:</h3>

    <p>In order to see your detail result, first you must login. The login link is
       located at the <b>Right Section</b> (above the Online Users Section).</p>

    <p>When the login screen is displayed, you must enter your <b>username</b> and
       <b>password</b>. Refer to the following example table on how to use your
       <b>ID number</b> and your <b>Grandfather name</b> as your username and initial
       password respectively.</p>

    <table>
        <thead>
            <tr>
                <th>Student's Father Full Name</th>
                <th>Student ID</th>
                <th>User Name will be</th>
                <th>Temporary Password</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Getahun Abebe</td>
                <td>INU0800343UR</td>
                <td>INU0800343UR</td>
                <td>abebe1234abcd#</td>
                <td rowspan="5" style="vertical-align:middle;">
                    All characters in the temporary password are <b>in small letter</b>.
                </td>
            </tr>
            <tr>
                <td>Belay Moges</td>
                <td>ENGR/3421/2002</td>
                <td>ENGR/3421/2002</td>
                <td>moges1234abcd#</td>
            </tr>
            <tr>
                <td>Sahre Jemal</td>
                <td>INU0801234PR</td>
                <td>INU0801234PR</td>
                <td>jemal1234abcd#</td>
            </tr>
            <tr>
                <td>Abebe Mamo</td>
                <td>INU0504341UD</td>
                <td>INU0504341UD</td>
                <td>mamo1234abcd#</td>
            </tr>
            <tr>
                <td>Gashaw Taye</td>
                <td>INU0504678UDI</td>
                <td>INU0504678UDI</td>
                <td>taye1234abcd#</td>
            </tr>
        </tbody>
    </table>

    <div class="highlight-note">
        <b>⚠️ Important:</b> As soon as you log in, the system will ask you to
        <b>change your temporary password</b> before accessing any resource that
        needs your identity. You have to fill the new password and other requested
        information properly. The new password should <b>not be less than 7 characters</b>.
    </div>

    <p>If you are unable to log in, please ask your concerned registrar office —
       because your ID number and/or grandfather name may not be encoded correctly.
       <i>Remember what you fill in the biography form.</i></p>

    <p>If you have any difficulties, you can contact the concerned <b>Registrar office</b>.</p>
</div>

<!-- ===== IMAGES ===== -->
<div style="text-align:center; margin: 30px 0;">
    <img src="image2.jpg" width="300" alt="Campus">
    <img src="image.jpeg" width="300" alt="Students">
    <img src="image3.jpeg" width="300" alt="Library">
</div>

</body>
</html>