<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Student</title>
    <link rel="stylesheet" href="css.css">
</head>

<body>

<div class="first">
    <div class="second">
        <img src="logoinu.jpg" width="100" height="100">
    </div>
    <div class="third">
        <h1>INJIBARA UNIVERSITY<br>STUDENT INFORMATION MANAGEMENT SYSTEM</h1>
    </div>
</div>

<div class="fivth">
    <a href="a.html">HOME</a>
    <a href="B.html">CONTACT US</a>
    <a href="C.html">NEWS</a>
</div>
<div class="addt">
<h2>Add Student</h2>

<form id="studentForm">

    <label>First Name</label>
    <input type="text" id="firstname" name="firstname" required><br>

    <label>Last Name</label>
    <input type="text" id="lastname" name="lastname"  required><br>

    <label>Age</label>
    <input type="number" id="age"name="age"  required><br>

    <label>ID</label>
    <input type="text" id="studentid"name="studentid"  required><br>

    <label>Gender:</label><br>
    <input type="radio" name="gender" value="Male" required> MALE <br>
    <input type="radio" name="gender" value="Female" required> FEMALE <br>


    <label>Email</label>
    <input type="email" id="email"name="email" ><br>

    <label>Phone No</label>
    <input type="tel" id="phone" name="phone" maxlength="10"><br>

    <label>Department</label>
    <select id="department"name="departement" >
        <option value="CS">Computer Science</option>
        <option value="ECE">Electrical & Computer Eng.</option>
        <option value="SE">Software Engineering</option>
        <option value="IS">Information Systems</option>
        <option value="IT">Information Technology</option>
        <option value="CE">Civil Engineering</option>
        <option value="ECON">Economics</option>
        <option value="ACC">Accounting</option>
        <option value="MGT">Management</option>
    </select><br><br>

    <label>Year</label>
    <input type="number" id="year" min="1" max="7" name="year" required><br><br>

    <input type="button" value="ADD STUDENT" onclick="addStudent()">
</form>
</div>
<script>
function addStudent() {
    let firstname = document.getElementById("firstname").value;
    let lastname  = document.getElementById("lastname").value;
    let age       = document.getElementById("age").value;
    let id        = document.getElementById("studentid").value;
    let email     = document.getElementById("email").value;
    let phone     = document.getElementById("phone").value;
    let year      = document.getElementById("year").value;
    let dept      = document.getElementById("department").value;

    let gender = document.querySelector("input[name='gender']:checked").value;

   
    let student = {
        firstname,
        lastname,
        age,
        id,
        gender,
        email,
        phone,
        department: dept,
        year
    };

 
    let list = JSON.parse(localStorage.getItem("students")) || [];
    list.push(student);
    localStorage.setItem("students", JSON.stringify(list));

    alert("Student Added Successfully!");


  window.location.href = "login2.html?view=true";

}
</script>

<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "students_db";

$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname  = $_POST['lastname'];
    $age       = $_POST['age'];
    $studentid = $_POST['studentid'];
    $gender    = $_POST['gender'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];
    $department= $_POST['department'];
    $year      = $_POST['year'];

    $sql = "INSERT INTO students 
            (firstname, lastname, age, studentid, gender, email, phone, department, year)
            VALUES 
            ('$firstname', '$lastname', '$age', '$studentid', '$gender', '$email', '$phone', '$department', '$year')";

    if (mysqli_query($conn, $sql)) {
        echo "Student added successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>




</body>
</html>
