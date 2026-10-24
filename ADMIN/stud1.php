<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="theme-color" content="#00ffff">
  <title>Injibara University · SIMS</title>
  
  <!-- Updated cache version to force mobile to load new CSS -->
  <link rel="stylesheet" href="css1.css?v=103">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="page-wrapper">
    
    <!-- ===== FIXED TOP SECTION (Header + Navigation) ===== -->
    <div class="fixed-top">
      <!-- Header -->
      <header class="uni-header">
        <div class="header-logo">
          <img src="logoinu.jpg" alt="Injibara University Logo">
        </div>
        <div class="header-titles">
          <h1>INJIBARA UNIVERSITY</h1>
          <h2>STUDENT INFORMATION MANAGEMENT SYSTEM</h2>
        </div>
        <!-- FIXED: Changed from login.php to loginf.php -->
        <a href="loginf.php" class="login-btn-header">LOGIN</a>
      </header>

      <!-- Navigation -->
      <nav class="nav-strip" aria-label="Main navigation">
        <!-- FIXED: Changed from a.php to stud1.php -->
        <a href="stud1.php">HOME</a>
        <a href="Bb.php">CONTACT US</a>
        <a href="Cc.php">NEWS</a>
      </nav>
    </div>

    <!-- ===== WELCOME BANNER ===== -->
    <div class="welcome-banner">
      <h1>WELCOME TO SIMS</h1>
    </div>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="main-content">
      <div class="intro-row">
        <div class="intro-image">
          <img src="image.png" alt="SIMS illustration" width="280" height="280" loading="lazy">
        </div>
        <div class="intro-text">
          <h1>WELCOME TO STUDENT INFORMATION MANAGEMENT SYSTEM (SIMS)</h1>
          <p>
            The Student Information Management System (SIMS) is a software application
            designed to manage and streamline the student data and administrative
            processes in educational institutions. It ensures the accurate storage,
            retrieval, and management of student information, including personal
            details, academic records, course enrollment, and results.
          </p>
          <p>
            Traditionally, student information is maintained manually, which is prone
            to errors, data loss, and inefficiency. The proposed system aims to
            automate these tasks to save time, improve accuracy, and support
            decision-making. It is an Integrated Software that automates the following
            Student related Business Processes of a University like:
          </p>
          <ul class="program-list">
            <li>COLLEGES</li>
            <li>COURSES</li>
            <li>DEPARTMENT</li>
            <li>TEACHERS</li>
            <li>STUDENTS</li>
          </ul>
        </div>
      </div>

      <!-- ===== COLLEGES / COURSES / DEPARTMENTS ===== -->
      <div class="info-section">
        <h2>🏛️ Colleges, Courses &amp; Departments</h2>
        <p>Injibara University is organized into several colleges, each offering multiple
          departments and a wide range of courses. Below is a summary of the academic
          structure available in SIMS.</p>

        <div class="cards-wrapper">
          <div class="card">
            <h4>🏫 Colleges</h4>
            <ul>
              <li>College of Engineering</li>
              <li>College of Natural Sciences</li>
              <li>College of Social Sciences</li>
              <li>College of Business and Economics</li>
              <li>College of Agriculture</li>
              <li>College of Medical and Health Sciences</li>
              <li>College of Enducational and Behavioral science</li>
              <li>School of Law</li>
            </ul>
          </div>
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
              <li>Biology</li>
              <li>Physics</li>
              <li>Sport Science</li>
              <li>Mathmatics</li>
             <li> Sociology</li>
             <li>Social Antropology</li>
             <li>Civics and Ethical Enducation</li>
             <li>History</li>
            </ul>
          </div>
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

      <!-- ===== STUDENT INFORMATION ===== -->
      <div class="info-section">
        <h2>📢 Dear Injibara University Students,</h2>
        <p>To follow-up your grade and related information <a href="loginf.php"><b>click here</b></a>.</p>
        <p><b>This System is launched for students to access the following information:</b></p>
        <ul style="margin-bottom: 22px; padding-left: 25px;">
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

        <div class="table-responsive">
          <table class="login-table">
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
                <td rowspan="5" style="vertical-align:middle;">All characters in the temporary password are <b>in small letter</b>.</td>
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
        </div>

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

      <!-- ===== IMAGE GALLERY ===== -->
      <div class="gallery-row">
        <img src="inu.jpg" alt="Campus" loading="lazy">
        <img src="inu2.jpg" alt="Students" loading="lazy">
        <img src="inu3.jpg" alt="Library" loading="lazy">
        <img src="inu4.jpg" alt="Library" loading="lazy">
        <img src="inu5.jpg" alt="Library" loading="lazy">
        <img src="inu6.jpg" alt="Library" loading="lazy">
        <img src="inu7.jpg" alt="Library" loading="lazy">
        <img src="inu8.jpg" alt="Library" loading="lazy">
        <img src="inu9.jpg" alt="Library" loading="lazy">

      </div>
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="site-footer">
      <div class="footer-container">
        <div class="footer-col">
          <h3>Injibara University</h3>
          <p>Student Information Management System (SIMS) — A secure, centralized platform for managing academic records, courses, and student data efficiently.</p>
          <div class="footer-social">
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="https://t.me/tadege2123" target="_blank" rel="noopener" aria-label="Telegram"><i class="fab fa-telegram-plane"></i></a>
            <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            <a href="mailto:tadege4340@gmail.com" aria-label="Email"><i class="fas fa-envelope"></i></a>
          </div>
        </div>
        <div class="footer-col">
          <h3>Quick Links</h3>
          <ul class="footer-links">
            <!-- FIXED: Changed from a.php to stud1.php -->
            <li><a href="stud1.php">Home</a></li>
            <li><a href="Bb.php">Contact Us</a></li>
            <li><a href="Cc.php">News &amp; Events</a></li>
            <li><a href="loginf.php">Student Login</a></li>
            <li><a href="#">Academic Calendar</a></li>
            <li><a href="#">FAQs</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h3>Contact Us</h3>
          <ul class="footer-contact">
            <li><i class="fas fa-map-marker-alt"></i> Injibara, Awi Zone, Amhara, Ethiopia</li>
            <li><i class="fas fa-phone-alt"></i> <a href="tel:0928293370">0928293370</a> / <a href="tel:0918169491">0918169491</a></li>
            <li><i class="fas fa-envelope"></i> <a href="mailto:tadege4340@gmail.com">tadege4340@gmail.com</a></li>
            <li><i class="fas fa-envelope-open-text"></i> <a href="mailto:tadegeyilkal@gmail.com">tadegeyilkal@gmail.com</a></li>
            <li><i class="fab fa-telegram-plane"></i> <a href="https://t.me/tadege2123" target="_blank" rel="noopener">@tadege2123</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h3>Need Help?</h3>
          <p>If you cannot log in or have issues with your student records, please contact the Registrar Office.</p>
          <a href="Bb.php" class="footer-btn">Contact Support</a>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; <span id="footer-year"></span> Injibara University • SIMS. All rights reserved.</p>
        <p>Designed with <i class="fas fa-heart" style="color:#e74c3c;"></i> by Tadege Yilkal</p>
      </div>
    </footer>
  </div>

  <script>
    document.getElementById('footer-year').textContent = new Date().getFullYear();
  </script>
</body>
</html>