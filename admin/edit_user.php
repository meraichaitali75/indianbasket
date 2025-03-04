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

// Get User ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$user_id = intval($_GET['id']);

// Fetch user details
$query = "SELECT user_id, firstname, lastname, email, role, provider, profile_pic FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: users.php");
    exit;
}

// Check if the user is registered with social media
$isSocialUser = ($user['provider'] !== 'manual');

// Determine Profile Picture (Default if None Exists)
$profilePic = !empty($user['profile_pic']) ? $user['profile_pic'] : '../assets/img/default-profile.png';

// Handle Form Submission for Updating User
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    // Prevent changes if user is from social media
    if ($isSocialUser) {
        $firstname = $user['firstname'];
        $lastname = $user['lastname'];
        $email = $user['email'];
        $role = $user['role'];
    }

    // If password field is not empty and the user is manual, update password
    if (!$isSocialUser && !empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $updateQuery = "UPDATE users SET firstname=?, lastname=?, email=?, password=?, role=? WHERE user_id=?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("sssssi", $firstname, $lastname, $email, $password, $role, $user_id);
    } else {
        $updateQuery = "UPDATE users SET role=? WHERE user_id=?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $role, $user_id);
    }

    if ($stmt->execute()) {
        $success = "User updated successfully!";
        header("Location: users.php");
        exit;
    } else {
        $errors[] = "Error updating user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | Admin Panel</title>
    
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
            <h2>Edit User</h2>
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

            <!-- Edit User Form -->
            <div class="card">
                <div class="card-header bg-primary text-white">Update User Information</div>
                <div class="card-body">
                    <div class="text-center">
                        <img src="<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>

                    <form method="POST">
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label>First Name</label>
                                <input type="text" name="firstname" class="form-control" value="<?php echo htmlspecialchars($user['firstname']); ?>" <?php echo $isSocialUser ? 'disabled' : ''; ?> required>
                                <?php if ($isSocialUser): ?>
                                    <input type="hidden" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>">
                                    <small class="text-danger">First name cannot be changed for social media users.</small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label>Last Name</label>
                                <input type="text" name="lastname" class="form-control" value="<?php echo htmlspecialchars($user['lastname']); ?>" <?php echo $isSocialUser ? 'disabled' : ''; ?> required>
                                <?php if ($isSocialUser): ?>
                                    <input type="hidden" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>">
                                    <small class="text-danger">Last name cannot be changed for social media users.</small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" <?php echo $isSocialUser ? 'disabled' : ''; ?> required>
                                <?php if ($isSocialUser): ?>
                                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                                    <small class="text-danger">Email cannot be changed for social media users.</small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label>Role</label>
                                <select name="role" class="form-control" <?php echo $isSocialUser ? 'disabled' : ''; ?>>
                                    <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                                    <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <?php if ($isSocialUser): ?>
                                    <input type="hidden" name="role" value="<?php echo $user['role']; ?>">
                                    <small class="text-danger">Role cannot be changed for social media users.</small>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-4">
                                <label>New Password</label>
                                <input type="password" name="password" class="form-control" <?php echo $isSocialUser ? 'disabled' : ''; ?>>
                                <?php if ($isSocialUser): ?>
                                    <small class="text-danger">Password cannot be changed for social media users.</small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">Update User</button>
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
