<html>
<head>
<title>PHP Lab1</title>
</head>
<body>



<form action="create.php" method="POST">
    <label>First Name:</label>
         <input type="text" name="fname" required><br><br>
    <label>Last Name:</label>
         <input type="text" name="lname" required><br><br>
    <label>Address:</label>
        <input type="text" name="address" required><br><br>
    <label>Sex:</label>
            <select name="sex" required>
        <option value="">Select</option>
        <option>Male</option>
        <option>Female</option>
    </select><br><br>
    <label>Phone:</label>
    <input type="text" name="phone" required><br><br>
    <label>Email:</label>
    <input type="email" name="email" required><br><br>
    <button type="submit">Save</button>
</form>
</body>
</html>