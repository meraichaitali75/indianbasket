<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once __DIR__ . '/../includes/db/functions.php';

if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

$billingObj = new Functions();
$billing_addresses = [];
$errors = [];
$success = "";

$conn = $billingObj->getDatabaseConnection();
$query = "SELECT * FROM billing_addresses GROUP BY user_id ORDER BY address_id DESC";
$result = $conn->query($query);
if ($result) {
    $billing_addresses = $result->fetch_all(MYSQLI_ASSOC);
}

// Delete Billing Address
if (isset($_GET['delete'])) {
    $address_id = intval($_GET['delete']);
    $deleteQuery = "DELETE FROM billing_addresses WHERE address_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $address_id);
    
    if ($stmt->execute()) {
        $success = "Billing address deleted successfully!";
        header("Location: billing_address.php");
        exit();
    } else {
        $errors[] = "Error deleting billing address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Billing Addresses | Admin Panel</title>
    
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
            <h2>Manage Billing Addresses</h2>
        </div>

        <!-- Messages -->
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

        <div class="container mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Billing Addresses</h4>
                <a href="add_billing_address.php" class="btn btn-primary">➕ Add New Billing Address</a>
            </div>
        </div>

        <!-- Billing Address Table -->
        <div class="card">
            <div class="card-header bg-dark text-white">Billing Address List</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Street Address</th>
                            <th>City</th>
                            <th>Province</th>
                            <th>Zip Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php foreach ($billing_addresses as $address): ?>
        <tr>
            <td><?php echo $address['address_id']; ?></td>
            <td><?php echo $address['user_id']; ?></td>
            <td><?php echo $address['street_address']; ?></td>
            <td><?php echo $address['city']; ?></td>
            <td><?php echo $address['province']; ?></td>
            <td><?php echo $address['zip_code']; ?></td>
            <td>
                <a href="edit_billing_address.php?id=<?php echo $address['address_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="billing_address.php?delete=<?php echo $address['address_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                <a href="view_all_addresses.php?user_id=<?php echo $address['user_id']; ?>" class="btn btn-info btn-sm">View All</a>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

                </table>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
