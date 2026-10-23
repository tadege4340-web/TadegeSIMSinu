<?php
session_start();

// --- SECURITY CHECK: Allow only admin ---
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';
$table_name = "users2";
$message = "";

// -------------------- ADD STUDENT --------------------
if (isset($_POST['add_student'])) {
    $fn = $_POST['firstname'];
    $ln = $_POST['lastname'];
    $age = (int)$_POST['age'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $dept = $_POST['department'];
    $year = (int)$_POST['year'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO $table_name (firstname, lastname, age, gender, email, phone, department, year, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissssis", $fn, $ln, $age, $gender, $email, $phone, $dept, $year, $role);

    if ($stmt->execute()) {
        $message = "New student record created successfully.";
    } else {
        $message = "Error adding student: " . $stmt->error;
    }
    $stmt->close();
}

// -------------------- DELETE STUDENT --------------------
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM $table_name WHERE student_id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "Record deleted successfully.";
    } else {
        $message = "Error deleting record: " . $stmt->error;
    }
    $stmt->close();
}

// -------------------- SEARCH & VIEW --------------------
$search_query = "";
$where_clause = "";
if (isset($_GET['search']) && !empty($_GET['search_term'])) {
    $search_term = $conn->real_escape_string($_GET['search_term']);
    $search_query = $search_term;
    if (is_numeric($search_term)) {
        $where_clause = " WHERE student_id = $search_term OR firstname LIKE '%$search_term%' OR lastname LIKE '%$search_term%' OR department LIKE '%$search_term%'";
    } else {
        $where_clause = " WHERE firstname LIKE '%$search_term%' OR lastname LIKE '%$search_term%' OR department LIKE '%$search_term%'";
    }
}

$sql = "SELECT * FROM $table_name $where_clause ORDER BY student_id DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>INJIBARA UNIVERSITY - Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f0f0f0; }
        .header { background-color: #c0c0c0; text-align: center; padding: 20px 0 10px; }
        .header h1 { color: #008080; margin: 0; }
        .header .logo { float: left; margin-left: 20px; width: 60px; height: 60px; background-color: #ccc; border: 1px solid #000; }
        
        .nav-primary a { margin: 0 15px; text-decoration: none; color: #0000ff; font-weight: bold; }
        
        .nav-secondary { background-color: #d3d3d3; padding: 10px; text-align: center; margin-top: 10px; }
        .nav-secondary button, .nav-secondary a { 
            padding: 8px 15px; 
            border: 1px solid #000; 
            background: #fff; 
            text-decoration: none;
            color: #000;
            cursor: pointer;
            margin: 0 5px;
            display: inline-block;
        }

        .active-section { border-left: 5px solid #008080; padding-left: 10px; }

        .container { width: 90%; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .message { padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 15px; }
        
        .add-form input, .add-form select { padding: 5px; margin-right: 10px; margin-bottom: 10px; }
        .add-form, .search-form { padding: 15px; border: 1px solid #ddd; border-radius: 5px; }

        .logout { position: fixed; bottom: 10px; right: 10px; padding: 5px 15px; border: 1px solid #000; background: #fff; cursor: pointer; }
    </style>
    <script>
        function scrollToSection(id) {
            document.getElementById(id).scrollIntoView({ behavior: 'smooth' });
        }
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete record ID ' + id + '?')) {
                window.location.href = 'admin_dashboard.php?delete_id=' + encodeURIComponent(id);
            }
        }
    </script>
</head>
<body>

<div class="header">
    <div class="logo">LOGO</div>
    <h1>INJIBARA UNIVERSITY</h1>
    <h1 style="color: #008080;">STUDENT INFORMATION MANAGEMENT SYSTEM</h1>
</div>

<div class="nav-primary">
    <a href="admin_dashboard.php">HOME</a> |
    <a href="#">CONTACT US</a> |
    <a href="#">NEWS</a>
</div>

<div class="nav-secondary">
    <a href="#">COLLEGE</a>
    <a href="#">TEACHER</a>
    <button onclick="scrollToSection('add-student-section');">ADD STUDENT</button>
    <button onclick="scrollToSection('view-student-section');">VIEW STUDENT</button>
    <button onclick="alert('To UPDATE a record, click Edit link in the Action column.');">UPDATE STUDENT</button>
    <button onclick="alert('To DELETE a record, click Delete link in the Action column.');">DELETE STUDENT</button>
    <button onclick="scrollToSection('search-student-section');">SEARCH STUDENT</button>
</div>

<div class="container">
    <h2>Admin Dashboard (Full CRUD Access)</h2>

    <?php if (!empty($message)) : ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div id="add-student-section" class="active-section">
        <h2>➕ ADD STUDENT</h2>
        <form method="POST" class="add-form">
            <input type="text" name="firstname" placeholder="First Name" required>
            <input type="text" name="lastname" placeholder="Last Name" required>
            <input type="number" name="age" placeholder="Age" required>
            <select name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
            <input type="email" name="email" placeholder="Email">
            <input type="text" name="phone" placeholder="Phone">
            <input type="text" name="department" placeholder="Dept." required>
            <input type="number" name="year" placeholder="Year" required>
            <select name="role" required>
                <option value="user">user</option>
                <option value="admin">admin</option>
            </select>
            <button type="submit" name="add_student">Add Student</button>
        </form>
    </div>

    <hr>

    <div id="search-student-section">
        <h2>🔍 SEARCH STUDENT</h2>
        <form method="GET" class="search-form">
            <input type="text" name="search_term" placeholder="Search by Name, ID, or Dept." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit" name="search">Search</button>
            <a href="admin_dashboard.php">Show All</a>
        </form>
    </div>

    <div id="view-student-section">
        <h2>📋 VIEW STUDENT Records (<?php echo $result->num_rows; ?> Found)</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>First Name</th><th>Last Name</th><th>Age</th><th>Dept.</th><th>Year</th><th>Role</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['age']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['year']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                        echo "<td>";
                        echo "<a href='edit_student.php?id=" . htmlspecialchars($row['student_id']) . "'>Edit</a> | ";
                        echo "<a href='#' onclick=\"confirmDelete(" . htmlspecialchars($row['student_id']) . ")\">Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No students found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<a href="logout.php" class="logout">LOGOUT</a>

<?php $conn->close(); ?>
</body>
</html>
