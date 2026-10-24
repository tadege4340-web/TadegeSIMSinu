<?php
require 'db.php';

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
    $hire_date     = !empty($_POST['hire_date']) ? $_POST['hire_date'] : null;
    $status        = $_POST['status'] ?? 'Active';

    // Validation
    if ($college === '') {
        $error = "Please select a college.";
    } elseif ($department === '') {
        $error = "Please select a department.";
    } else {
        try {
            // ====== DUPLICATE CHECK (PDO) ======
            $duplicates = [];

            $c = $conn->prepare("SELECT id FROM teachers WHERE id = :id");
            $c->execute(['id' => $id]);
            if ($c->fetch()) $duplicates[] = "ID";

            $c = $conn->prepare("SELECT id FROM teachers WHERE email = :email");
            $c->execute(['email' => $email]);
            if ($c->fetch()) $duplicates[] = "Email";

            if ($phone !== '') {
                $c = $conn->prepare("SELECT id FROM teachers WHERE phone = :phone");
                $c->execute(['phone' => $phone]);
                if ($c->fetch()) $duplicates[] = "Phone";
            }

            if (!empty($duplicates)) {
                $list  = implode(", ", $duplicates);
                $error = "The following field(s) already exist: $list. Please use different values.";
            } else {
                
                // ====== INSERT TEACHER INTO TEACHERS TABLE (PDO) ======
                $stmt = $conn->prepare(
                    "INSERT INTO teachers 
                     (id, user_id, full_name, age, gender, email, phone, address,
                      college, department, qualification, position, hire_date, status) 
                     VALUES (:id, :user_id, :full_name, :age, :gender, :email, :phone, :address,
                             :college, :department, :qualification, :position, :hire_date, :status)"
                );

                $stmt->execute([
                    'id' => $id, 'user_id' => $id, 'full_name' => $full_name, 'age' => $age,
                    'gender' => $gender, 'email' => $email, 'phone' => $phone, 'address' => $address,
                    'college' => $college, 'department' => $department, 'qualification' => $qualification,
                    'position' => $position, 'hire_date' => $hire_date, 'status' => $status
                ]);
                
                // ====================================================
                // AUTOMATICALLY CREATE LOGIN ACCOUNT (PDO)
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
                $u_stmt = $conn->prepare(
                    "INSERT INTO users (username, password, full_name, role, must_change_password) 
                     VALUES (:username, :password, :full_name, :role, 1)"
                );
                
                $u_stmt->execute([
                    'username' => $id,
                    'password' => $hashed_password,
                    'full_name' => $full_name,
                    'role' => $role
                ]);
                
                // ====================================================

                header("Location: " . $_SERVER['PHP_SELF'] . "?msg=registered");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// ====== STEP 2: FETCH TEACHERS ======
include 'header.php';

$stmt = $conn->query("SELECT * FROM teachers ORDER BY id ASC");
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
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