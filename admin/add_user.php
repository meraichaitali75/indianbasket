<?php
session_start();
require_once __DIR__ . '/../includes/db/functions.php';

// Check if admin is logged in
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

$usersObj = new Functions();
$conn = $usersObj->getDatabaseConnection();
$errors = [];
$success = "";

// Handle User Addition
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    // Validate Inputs
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    }
    
    // Name validation (Only letters, 2-50 characters)
    if (!preg_match("/^[a-zA-Z]{2,50}$/", $firstname)) {
        $errors[] = "First name must be only letters, between 2 to 50 characters.";
    }
    if (!preg_match("/^[a-zA-Z]{2,50}$/", $lastname)) {
        $errors[] = "Last name must be only letters, between 2 to 50 characters.";
    }

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } else {
        // Check if email already exists
        $existing_user = $usersObj->getUserByEmail($email);
        if ($existing_user) {
            $errors[] = "This email is already registered.";
        }
    }

    // Password validation (Min 8 chars, 1 uppercase, 1 number, 1 special character)
    if (!preg_match("/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $password)) {
        $errors[] = "Password must be at least 8 characters long, contain an uppercase letter, a number, and a special character.";
    }

    // If no errors, proceed to insert
    if (empty($errors)) {
        // Hash Password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Ensure Upload Directory Exists
        $uploadDir = __DIR__ . "/../assets/img/uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Upload Profile Picture if provided
        $profilePic = "assets/img/default-profile.png"; // Default Image
        if (!empty($_FILES["profile_pic"]["name"])) {
            $profilePicName = time() . "_" . basename($_FILES["profile_pic"]["name"]);
            $targetFilePath = $uploadDir . $profilePicName;
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            // Allow only jpg, png, jpeg formats
            if (in_array($fileType, ["jpg", "jpeg", "png"])) {
                if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFilePath)) {
                    $profilePic = "../assets/img/uploads/" . $profilePicName;
                } else {
                    $errors[] = "Error uploading profile picture.";
                }
            } else {
                $errors[] = "Only JPG, JPEG, and PNG formats are allowed.";
            }
        }

        // Insert into Database
        if (empty($errors)) {
            $insertQuery = "INSERT INTO users (firstname, lastname, email, password, role, profile_pic, provider) VALUES (?, ?, ?, ?, ?, ?, 'manual')";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ssssss", $firstname, $lastname, $email, $hashed_password, $role, $profilePic);

            if ($stmt->execute()) {
                $success = "User added successfully!";
                header("Location: users.php");
                exit();
            } else {
                $errors[] = "Error adding user.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User | Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

    <!-- Include Sidebar -->
    <?php include "sidebar.php"; ?>

    <!-- Main Content -->
    <div class="content">
        <div class="dashboard-header">
            <h2>Add User</h2>
        </div>

        <div class="container mt-4">
            <!-- Display Success Message -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <!-- Display Errors -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Add User Form -->
            <div class="card">
                <div class="card-header bg-primary text-white">Add New User</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4">
                                <label>First Name</label>
                                <input type="text" name="firstname" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Last Name</label>
                                <input type="text" name="lastname" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Profile Picture (Optional)</label>
                                <input type="file" name="profile_pic" class="form-control">
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">Add User</button>
                            <a href="users.php" class="btn btn-secondary">Back to Users</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
