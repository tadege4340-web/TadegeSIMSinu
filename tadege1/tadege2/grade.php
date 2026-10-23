<?php
$conn = mysqli_connect("localhost", "root", "", "lab");

// Student Info
$student_id = $_POST['student_id'];
$college = $_POST['college'];
$department = $_POST['department'];

// Insert/Update student info
mysqli_query($conn, "INSERT INTO students (student_id, college, department) 
                     VALUES ('$student_id','$college','$department') 
                     ON DUPLICATE KEY UPDATE college='$college', department='$department'");

// Courses info
$course_ids = $_POST['course_id'];
$grades = $_POST['grade'];
$semesters = $_POST['semester'];
$academic_years = $_POST['academic_year'];

// Insert grades
for($i=0; $i<count($course_ids); $i++){
    $course_id = $course_ids[$i];
    $grade = $grades[$i];
    $semester = $semesters[$i];
    $year = $academic_years[$i];

    mysqli_query($conn, "INSERT INTO grades (student_id, course_id, grade, semester, academic_year) 
                         VALUES ('$student_id','$course_id','$grade','$semester','$year')");
}

// Calculate GPA
$result = mysqli_query($conn, "SELECT grade FROM grades WHERE student_id='$student_id'");

$points = 0;
$count = 0;
while($row = mysqli_fetch_assoc($result)){
    switch($row['grade']){
        case 'A': $points += 4; break;
        case 'B': $points += 3; break;
        case 'C': $points += 2; break;
        case 'D': $points += 1; break;
        case 'F': $points += 0; break;
    }
    $count++;
}

$gpa = $count ? round($points / $count, 2) : 0;

// Update student GPA
mysqli_query($conn, "UPDATE students SET gpa='$gpa' WHERE student_id='$student_id'");

echo "Grades added successfully! Current GPA: $gpa";
?>
