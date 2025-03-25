<?php
session_start();
require_once __DIR__ . '/../includes/db/functions.php';

// Redirect to login if not an admin
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

$functionsObj = new Functions();
$conn = $functionsObj->getDatabaseConnection();

// Fetch Total Users
$query = "SELECT COUNT(*) AS total_users FROM users";
$result = $conn->query($query);
$totalUsers = $result->fetch_assoc()['total_users'];

// Fetch Total Orders
$query = "SELECT COUNT(*) AS total_orders FROM orders";
$result = $conn->query($query);
$totalOrders = $result->fetch_assoc()['total_orders'];

// Fetch Total Products
$query = "SELECT COUNT(*) AS total_products FROM products";
$result = $conn->query($query);
$totalProducts = $result->fetch_assoc()['total_products'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Indian Basket</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Admin Panel CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

    <!-- Include Sidebar -->
    <?php include "sidebar.php"; ?>

    <!-- Main Content Area -->
    <div class="content">
        <div class="dashboard-header">
            <h2>Admin Dashboard</h2>
        </div>

        <div class="container mt-4">
            <h4>Welcome, Admin!</h4>
            <p>Use the left menu to navigate through the admin panel.</p>

            <div class="row">
                <!-- Total Users Card -->
                <div class="col-md-4">
                    <div class="card bg-primary text-white mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Users</h5>
                            <p class="card-text fs-4">👤 <?php echo $totalUsers; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Orders Card -->
                <div class="col-md-4">
                    <div class="card bg-success text-white mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Orders</h5>
                            <p class="card-text fs-4">📦 <?php echo $totalOrders; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Products Card -->
                <div class="col-md-4">
                    <div class="card bg-warning text-dark mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Products</h5>
                            <p class="card-text fs-4">🛒 <?php echo $totalProducts; ?></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>