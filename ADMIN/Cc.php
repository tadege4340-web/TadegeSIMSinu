<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>News & Events • Injibara University</title>
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- External CSS File -->
    <link rel="stylesheet" href="C.css">
</head>
<body>

    <!-- ===== HEADER (Logo Left, Text Centered, FIXED) ===== -->
    <header class="university-header">
        <div class="header-logo">
            <img src="logoinu.jpg" alt="Injibara University Logo">
        </div>
        <div class="header-title">
            <h1>INJIBARA UNIVERSITY</h1>
            <h2>STUDENT INFORMATION MANAGEMENT SYSTEM</h2>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="news-container">
        
        <!-- Page Title -->
        <div class="page-title">
            <h1><i class="fas fa-newspaper"></i> News &amp; Events</h1>
            <p>Stay updated with the latest happenings, announcements, and academic events at Injibara University.</p>
        </div>

        <!-- Featured News (Highlight) -->
        <article class="featured-news">
            <div class="featured-image">
                <img src="image2.jpg" alt="SIMS Launch">
                <span class="badge">FEATURED</span>
            </div>
            <div class="featured-content">
                <div class="news-meta">
                    <span><i class="far fa-calendar-alt"></i> October 15, 2026</span>
                    <span><i class="far fa-user"></i> Registrar Office</span>
                </div>
                <h2>Student Information Management System (SIMS) Officially Launched</h2>
                <p>
                    Injibara University has officially launched the new Student Information Management System (SIMS). 
                    This modern platform allows students to access their academic records, check grades, view course 
                    schedules, and manage their personal information securely from anywhere.
                </p>
                <p>
                    All students are required to log in using their Student ID and temporary password to update 
                    their profiles and change their passwords immediately.
                </p>
                <a href="loginf.php" class="read-more-btn">Log In Now <i class="fas fa-arrow-right"></i></a>
            </div>
        </article>

        <!-- News Grid -->
        <div class="news-grid">
            
            <!-- News Item 1 -->
            <article class="news-card">
                <img src="image3.jpeg" alt="Library">
                <div class="news-card-content">
                    <div class="news-meta">
                        <span><i class="far fa-calendar-alt"></i> Oct 10, 2026</span>
                        <span class="category">Academics</span>
                    </div>
                    <h3>Mid-Semester Examinations Schedule Released</h3>
                    <p>The examination schedule for the 2026/1st semester has been published. Students are advised to check their department notice boards and the SIMS portal for their specific timetables.</p>
                    <a href="#" class="read-more-link">Read More →</a>
                </div>
            </article>

            <!-- News Item 2 -->
            <article class="news-card">
                <img src="image.jpeg" alt="Students">
                <div class="news-card-content">
                    <div class="news-meta">
                        <span><i class="far fa-calendar-alt"></i> Oct 05, 2026</span>
                        <span class="category">Events</span>
                    </div>
                    <h3>Annual Student Orientation Program Concludes</h3>
                    <p>The week-long orientation program for freshmen students successfully concluded last Friday. Students were introduced to campus facilities, academic policies, and student life.</p>
                    <a href="#" class="read-more-link">Read More →</a>
                </div>
            </article>

            <!-- News Item 3 -->
            <article class="news-card">
                <img src="image.png" alt="SIMS Image">
                <div class="news-card-content">
                    <div class="news-meta">
                        <span><i class="far fa-calendar-alt"></i> Sep 28, 2026</span>
                        <span class="category">System Update</span>
                    </div>
                    <h3>SIMS Security Update: Password Policy Changed</h3>
                    <p>To enhance security, all student passwords must now be at least 7 characters long. Please update your password upon your next login to avoid account suspension.</p>
                    <a href="#" class="read-more-link">Read More →</a>
                </div>
            </article>

            <!-- News Item 4 -->
            <article class="news-card">
                <img src="image2.jpg" alt="Campus">
                <div class="news-card-content">
                    <div class="news-meta">
                        <span><i class="far fa-calendar-alt"></i> Sep 20, 2026</span>
                        <span class="category">Announcement</span>
                    </div>
                    <h3>Dormitory Placement Results Announced</h3>
                    <p>The dormitory placement results for the new academic year are now available. Students can check their assigned dormitory blocks and room numbers via the SIMS portal.</p>
                    <a href="#" class="read-more-link">Read More →</a>
                </div>
            </article>

            <!-- News Item 5 -->
            <article class="news-card">
                <img src="image3.jpeg" alt="Library Event">
                <div class="news-card-content">
                    <div class="news-meta">
                        <span><i class="far fa-calendar-alt"></i> Sep 15, 2026</span>
                        <span class="category">Library</span>
                    </div>
                    <h3>New Digital Library Resources Now Available</h3>
                    <p>The university library has subscribed to several new international journals and e-books. Students can now access thousands of academic resources directly from the campus network.</p>
                    <a href="#" class="read-more-link">Read More →</a>
                </div>
            </article>

            <!-- News Item 6 -->
            <article class="news-card">
                <img src="image.jpeg" alt="Students Event">
                <div class="news-card-content">
                    <div class="news-meta">
                        <span><i class="far fa-calendar-alt"></i> Sep 05, 2026</span>
                        <span class="category">Sports</span>
                    </div>
                    <h3>Inter-College Sports Tournament Kicks Off</h3>
                    <p>The annual inter-college sports tournament has officially begun. Students from all departments are competing in football, basketball, and athletics. Come support your team!</p>
                    <a href="#" class="read-more-link">Read More →</a>
                </div>
            </article>

        </div>

        <!-- Pagination (Visual only) -->
        <div class="pagination">
            <a href="#" class="page-link active">1</a>
            <a href="#" class="page-link">2</a>
            <a href="#" class="page-link">3</a>
            <a href="#" class="page-link next">Next <i class="fas fa-chevron-right"></i></a>
        </div>

    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="site-footer">
        <div class="footer-container">
            <!-- About -->
            <div class="footer-col">
                <h3>Injibara University</h3>
                <p>Student Information Management System (SIMS) — A secure, centralized platform for managing academic records, courses, and student data efficiently.</p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://t.me/tadege2123" target="_blank" aria-label="Telegram"><i class="fab fa-telegram-plane"></i></a>
                    <a href="mailto:tadege4340@gmail.com" aria-label="Email"><i class="fas fa-envelope"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="a.php">Home</a></li>
                    <li><a href="B.php">Contact Us</a></li>
                    <li><a href="C.php">News &amp; Events</a></li>
                    <li><a href="loginf.php">Student Login</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="footer-col">
                <h3>Contact Us</h3>
                <ul class="footer-contact">
                    <li><i class="fas fa-map-marker-alt"></i> Injibara, Awi Zone, Ethiopia</li>
                    <li><i class="fas fa-phone-alt"></i> <a href="tel:0928293370">0928293370</a></li>
                    <li><i class="fas fa-envelope"></i> <a href="mailto:tadege4340@gmail.com">tadege4340@gmail.com</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <span id="year"></span> Injibara University • SIMS. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>

</body>
</html>