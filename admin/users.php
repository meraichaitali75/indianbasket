<?php
session_start();
require_once __DIR__ . '/../includes/db/functions.php';

if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

$usersObj = new Functions();
$users = [];
$errors = [];
$success = "";

$conn = $usersObj->getDatabaseConnection();

// Set Default Pagination
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20; // Default: 20 per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Search Feature
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$searchQuery = "";
if (!empty($search)) {
    $searchQuery = "WHERE firstname LIKE ? OR lastname LIKE ? OR email LIKE ? OR role LIKE ?";
}

// Count Total Users for Pagination
$countQuery = "SELECT COUNT(*) AS total FROM users $searchQuery";
$stmt = $conn->prepare($countQuery);

if (!empty($search)) {
    $searchTerm = "%$search%";
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
}

$stmt->execute();
$countResult = $stmt->get_result();
$totalUsers = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalUsers / $limit);

// Fetch Users
$query = "SELECT user_id, firstname, lastname, email, role FROM users $searchQuery ORDER BY user_id DESC LIMIT ?, ?";
$stmt = $conn->prepare($query);

if (!empty($search)) {
    $stmt->bind_param("ssssii", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $offset, $limit);
} else {
    $stmt->bind_param("ii", $offset, $limit);
}

$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
}

// Delete User
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    $deleteQuery = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $success = "User deleted successfully!";
        header("Location: users.php");
        exit();
    } else {
        $errors[] = "Error deleting user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Admin Panel</title>
    
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
            <h2>Manage Users</h2>
        </div>

        <div class="container mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Manage Users</h4>
                <a href="add_user.php" class="btn btn-primary">➕ Add New User</a>
            </div>

            <!-- Search Bar & Filter -->
            <form method="GET" class="mb-3 d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Search by Name, Email, or Role" value="<?php echo htmlspecialchars($search); ?>">
                <select name="limit" class="form-select me-2">
                    <option value="10" <?php echo ($limit == 10) ? 'selected' : ''; ?>>Show 10</option>
                    <option value="20" <?php echo ($limit == 20) ? 'selected' : ''; ?>>Show 20</option>
                    <option value="50" <?php echo ($limit == 50) ? 'selected' : ''; ?>>Show 50</option>
                </select>
                <button type="submit" class="btn btn-success">Filter</button>
            </form>
        </div>

        <!-- Display Messages -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Users Table -->
        <div class="card">
            <div class="card-header bg-dark text-white">User List</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['user_id']; ?></td>
                                <td><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo ucfirst($user['role']); ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="users.php?delete=<?php echo $user['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?search=<?php echo urlencode($search); ?>&limit=<?php echo $limit; ?>&page=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>

            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
