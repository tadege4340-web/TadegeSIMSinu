<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Head.css">
    <style> body { background-color: #f4f6f9; padding: 20px; } </style>
</head>
<body>
    <div class="page-container">
        <div class="page-header">
            <div class="header-text">
                <h2>📚 Add New Course</h2>
                <p>Department: <strong><?= htmlspecialchars($hod_department) ?></strong></p>
            </div>
        </div>

        <div class="filter-bar" style="max-width: 600px;">
            <?= $message ?>
            <form method="POST" action="Headcourseadd.php" style="display: flex; flex-direction: column; gap: 15px;">
                <div class="filter-group">
                    <label>Course Code</label>
                    <input type="text" name="course_code" required>
                </div>
                <div class="filter-group">
                    <label>Course Name</label>
                    <input type="text" name="course_name" required>
                </div>
                <div class="filter-group">
                    <label>Credit Hour</label>
                    <input type="number" name="credit_hour" required>
                </div>
                <div class="filter-group">
                    <label>Year</label>
                    <input type="number" name="course_year" required placeholder="e.g., 1" min="1" max="7">
                </div>
                <div class="filter-group">
                    <label>Semester</label>
                    <select name="course_semester" required>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                    </select>
                </div>
                <button type="submit" class="btn-apply" style="width: 100%; margin-top: 10px;">Add Course</button>
            </form>
        </div>
    </div>
</body>
</html>