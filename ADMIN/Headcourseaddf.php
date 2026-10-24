<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Head.css">
    <style> 
        body { background-color: #f4f6f9; padding: 20px; } 
        .form-container { max-width: 600px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #17365d; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .btn-submit { background: #1e4e79; color: white; padding: 12px 20px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; width: 100%; font-size: 16px; margin-top: 10px; }
        .btn-submit:hover { background: #163a5a; }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="page-header" style="margin-bottom: 20px; text-align: center;">
            <h2>📚 Add New Course</h2>
            <p>Department: <strong><?= htmlspecialchars($hod_department) ?></strong></p>
        </div>

        <div class="form-container">
            <!-- Display Success/Error Message -->
            <?= $message ?>

            <form method="POST" action="Headcourseadd.php">
                <div class="form-group">
                    <label>Course Code *</label>
                    <input type="text" name="course_code" required placeholder="e.g. CS101">
                </div>

                <div class="form-group">
                    <label>Course Name *</label>
                    <input type="text" name="course_name" required placeholder="e.g. Introduction to Computing">
                </div>

                <div class="form-group">
                    <label>Credit Hour *</label>
                    <input type="number" name="credit_hour" required min="1" max="10" placeholder="e.g. 3">
                </div>

                <div class="form-group">
                    <label>Year *</label>
                    <input type="number" name="course_year" required placeholder="e.g. 1" min="1" max="7">
                </div>

                <div class="form-group">
                    <label>Semester *</label>
                    <select name="course_semester" required>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">➕ Add Course</button>
            </form>
        </div>
    </div>
</body>
</html>