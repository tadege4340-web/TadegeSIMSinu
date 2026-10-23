<?php
require 'db.php';

// ====== STEP 0: SAFE COLUMN CHECK ======
function columnExists($conn, $table, $column) {
    $table  = mysqli_real_escape_string($conn, $table);
    $column = mysqli_real_escape_string($conn, $column);
    $res = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $res && mysqli_num_rows($res) > 0;
}

// Create table if it doesn't exist
$createSQL = "
CREATE TABLE IF NOT EXISTS teachers (
    id              VARCHAR(20)  NOT NULL PRIMARY KEY,
    user_id         VARCHAR(20)  DEFAULT NULL,
    full_name       VARCHAR(150) NOT NULL,
    age             INT          NOT NULL,
    gender          ENUM('Male','Female') NOT NULL,
    email           VARCHAR(100) NOT NULL UNIQUE,
    phone           VARCHAR(20),
    address         VARCHAR(255),
    college         VARCHAR(20)  DEFAULT NULL,
    department      VARCHAR(100),
    qualification   VARCHAR(50),
    position        VARCHAR(100),
    hire_date       DATE,
    status          ENUM('Active','On Leave','Retired') DEFAULT 'Active',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($conn, $createSQL)) {
    die("Table creation failed: " . mysqli_error($conn));
}

if (!columnExists($conn, 'teachers', 'college')) {
    mysqli_query($conn, "ALTER TABLE teachers ADD COLUMN college VARCHAR(20) DEFAULT NULL AFTER address");
}
if (!columnExists($conn, 'teachers', 'user_id')) {
    mysqli_query($conn, "ALTER TABLE teachers ADD COLUMN user_id VARCHAR(20) DEFAULT NULL AFTER id");
}

// ====== STEP 1: HANDLE FORM SUBMISSION (INSERT) ======
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id            = trim($_POST['id'] ?? '');
    $full_name     = $_POST['full_name'] ?? '';
    $age           = $_POST['age'] ?? '';
    $gender        = $_POST['gender'] ?? '';
    $email         = trim($_POST['email'] ?? '');
    $phone         = trim($_POST['phone'] ?? '');
    $address       = $_POST['address'] ?? '';
    $college       = trim($_POST['college'] ?? '');
    $department    = $_POST['department'] ?? '';
    $qualification = $_POST['qualification'] ?? '';
    $position      = $_POST['position'] ?? '';
    $hire_date     = !empty($_POST['hire_date']) ? $_POST['hire_date'] : NULL;
    $status        = $_POST['status'] ?? 'Active';

    // Validation
    if ($college === '') {
        $error = "Please select a college.";
    } elseif ($department === '') {
        $error = "Please select a department.";
    } else {

        // ====== DUPLICATE CHECK ======
        $duplicates = [];

        $c = mysqli_prepare($conn, "SELECT id FROM teachers WHERE id = ?");
        mysqli_stmt_bind_param($c, "s", $id);
        mysqli_stmt_execute($c);
        mysqli_stmt_store_result($c);
        if (mysqli_stmt_num_rows($c) > 0) $duplicates[] = "ID";
        mysqli_stmt_close($c);

        $c = mysqli_prepare($conn, "SELECT id FROM teachers WHERE email = ?");
        mysqli_stmt_bind_param($c, "s", $email);
        mysqli_stmt_execute($c);
        mysqli_stmt_store_result($c);
        if (mysqli_stmt_num_rows($c) > 0) $duplicates[] = "Email";
        mysqli_stmt_close($c);

        if ($phone !== '') {
            $c = mysqli_prepare($conn, "SELECT id FROM teachers WHERE phone = ?");
            mysqli_stmt_bind_param($c, "s", $phone);
            mysqli_stmt_execute($c);
            mysqli_stmt_store_result($c);
            if (mysqli_stmt_num_rows($c) > 0) $duplicates[] = "Phone";
            mysqli_stmt_close($c);
        }

        if (!empty($duplicates)) {
            $list  = implode(", ", $duplicates);
            $error = "The following field(s) already exist: $list. Please use different values.";
        } else {
            
            // ====== INSERT TEACHER INTO TEACHERS TABLE ======
            $stmt = mysqli_prepare($conn,
                "INSERT INTO teachers 
                 (id, user_id, full_name, age, gender, email, phone, address,
                  college, department, qualification, position, hire_date, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param($stmt, "sssissssssssss",
                $id, $id, $full_name, $age, $gender, $email, $phone, $address,
                $college, $department, $qualification, $position, $hire_date, $status
            );

            if (mysqli_stmt_execute($stmt)) {
                
                // ====================================================
                // AUTOMATICALLY CREATE LOGIN ACCOUNT
                // ====================================================
                
                // Extract Grandfather's Name (The LAST word of the full name)
                $name_parts = explode(' ', trim($full_name));
                $grandfather_name = strtolower(end($name_parts)); 
                
                // Create the default password (e.g., melaku1234abcd#)
                $plain_password = $grandfather_name . '1234abcd#';
                
                // Hash the password for security
                $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
                
                $role = 'teacher';
                
                // Insert into the users table (Username is the ID)
                $u_stmt = mysqli_prepare($conn, 
                    "INSERT INTO users (id, username, password, full_name, role, must_change_password) 
                     VALUES (?, ?, ?, ?, ?, 1)"
                );
                mysqli_stmt_bind_param($u_stmt, "sssss", $id, $id, $hashed_password, $full_name, $role);
                mysqli_stmt_execute($u_stmt);
                mysqli_stmt_close($u_stmt);
                
                // ====================================================

                header("Location: " . $_SERVER['PHP_SELF'] . "?msg=registered");
                exit;
            } else {
                $error = "Database error: " . mysqli_stmt_error($stmt);
            }
        }
    }
}

// ====== STEP 2: FETCH TEACHERS ======
include 'header.php';
$result = mysqli_query($conn, "SELECT * FROM teachers ORDER BY id ASC");
?>

<h2 style="text-align:center; color:#2c3e50;">Registered Teachers</h2>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'registered'): ?>
    <p style="text-align:center; color:green;">✅ Teacher registered successfully! They can now log in using their ID.</p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p style="text-align:center; padding:10px; background:#fee2e2; color:#991b1b;
              font-weight:bold; border-radius:6px; width:80%; margin:10px auto;">
        ❌ <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<?php include 'footer.php'; ?>