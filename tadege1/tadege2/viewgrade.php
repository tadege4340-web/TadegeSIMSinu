<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Student Grades</title>
<link rel="stylesheet" href="register.css">
<style>
    .course-row { margin-bottom: 10px; }
    .course-row input, .course-row select { margin-right: 10px; }
</style>
</head>
<body>

<div class="grade-container">
    <form class="grade-form" action="grade.php" method="POST">
        <h2>Add Student Grades</h2>

        <!-- Student ID -->
        <label for="student_id">Student ID</label>
        <input type="text" id="student_id" name="student_id" required><br><br>

        <!-- College -->
        <label for="college">College</label>
        <select id="college" name="college" required>
            <option value="">-- Select College --</option>
            <option value="College of Engineering">College of Engineering</option>
            <option value="College of Natural Sciences">College of Natural Sciences</option>
            <option value="College of Social Sciences">College of Social Sciences</option>
            <option value="College of Business and Economics">College of Business and Economics</option>
            <option value="College of Agriculture">College of Agriculture</option>
            <option value="College of Medical and Health Sciences">College of Medical and Health Sciences</option>
        </select><br><br>

        <!-- Department -->
        <label for="department">Department</label>
        <select id="department" name="department" required>
            <option value="">-- Select Department --</option>
            <option value="Computer Science">Computer Science</option>
            <option value="Software Engineering">Software Engineering</option>
            <option value="Information Technology">Information Technology</option>
            <option value="Electrical Engineering">Electrical Engineering</option>
            <option value="Civil Engineering">Civil Engineering</option>
            <option value="Economics">Economics</option>
            <option value="Accounting">Accounting</option>
            <option value="Agriculture">Agriculture</option>
            <option value="Nursing">Nursing</option>
            <option value="Public Health">Public Health</option>
        </select><br><br>

        <!-- Courses Section -->
        <h3>Courses</h3>
        <div id="courses">
            <div class="course-row">
                <input type="text" name="course_id[]" placeholder="Course ID" required>
                <select name="grade[]" required>
                    <option value="">Grade</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    <option value="F">F</option>
                </select>
                <input type="text" name="semester[]" placeholder="Semester" required>
                <input type="text" name="academic_year[]" placeholder="2024/2025" required>
                <button type="button" onclick="removeCourse(this)">Remove</button>
            </div>
        </div>
        <button type="button" onclick="addCourse()">Add Another Course</button><br><br>

        <!-- Submit -->
        <button type="submit">Add Grades & Calculate GPA</button>
    </form>
</div>

<script>
function addCourse() {
    const container = document.getElementById('courses');
    const newRow = document.createElement('div');
    newRow.className = 'course-row';
    newRow.innerHTML = `
        <input type="text" name="course_id[]" placeholder="Course ID" required>
        <select name="grade[]" required>
            <option value="">Grade</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
            <option value="F">F</option>
        </select>
        <input type="text" name="semester[]" placeholder="Semester" required>
        <input type="text" name="academic_year[]" placeholder="2024/2025" required>
        <button type="button" onclick="removeCourse(this)">Remove</button>
    `;
    container.appendChild(newRow);
}

function removeCourse(btn) {
    btn.parentElement.remove();
}
</script>

</body>
</html>
