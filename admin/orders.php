<?php
session_start();
require_once __DIR__ . '/../includes/db/functions.php';

// Redirect to login if not an admin
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

$ordersObj = new Functions();
$conn = $ordersObj->getDatabaseConnection();

$errors = [];
$success = "";

// Create Order
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_order"])) {
    $user_id = intval($_POST["user_id"]);
    $total_amount = floatval($_POST["total_amount"]);
    $status = trim($_POST["status"]);

    if (empty($user_id) || empty($total_amount) || empty($status)) {
        $errors[] = "All fields are required.";
    } else {
        $query = "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ids", $user_id, $total_amount, $status);

        if ($stmt->execute()) {
            $success = "Order created successfully!";
            header("Location: orders.php");
            exit();
        } else {
            $errors[] = "Error creating order: " . $stmt->error;
        }
    }
}

// Update Order
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_order"])) {
    $order_id = intval($_POST["order_id"]);
    $user_id = intval($_POST["user_id"]);
    $total_amount = floatval($_POST["total_amount"]);
    $status = trim($_POST["status"]);

    if (empty($user_id) || empty($total_amount) || empty($status)) {
        $errors[] = "All fields are required.";
    } else {
        $query = "UPDATE orders SET user_id = ?, total_amount = ?, status = ? WHERE order_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("idsi", $user_id, $total_amount, $status, $order_id);

        if ($stmt->execute()) {
            $success = "Order updated successfully!";
            header("Location: orders.php");
            exit();
        } else {
            $errors[] = "Error updating order: " . $stmt->error;
        }
    }
}

// Delete Order
if (isset($_GET["delete"])) {
    $order_id = intval($_GET["delete"]);

    $query = "DELETE FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        $success = "Order deleted successfully!";
        header("Location: orders.php");
        exit();
    } else {
        $errors[] = "Error deleting order: " . $stmt->error;
    }
}

// Fetch All Orders
$query = "SELECT o.order_id, o.user_id, o.total_amount, o.status, o.order_date, u.firstname, u.lastname 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.user_id";
$result = $conn->query($query);
$orders = $result->fetch_all(MYSQLI_ASSOC);

// Fetch All Users for Dropdown
$query = "SELECT user_id, firstname, lastname FROM users";
$usersResult = $conn->query($query);
$users = $usersResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>
    <?php include "sidebar.php"; ?>

    <div class="content">
        <div class="dashboard-header">
            <h2>Manage Orders</h2>
        </div>

        <div class="container mt-4">
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"> <?php echo $success; ?> </div>
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

            <!-- <div class="card mb-4">
                <div class="card-header bg-dark text-white">Add New Order</div>
                <div class="card-body">
                    <form method="POST">
                        <label>User:</label>
                        <select name="user_id" class="form-control" required>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user["user_id"]; ?>">
                                    <?php echo $user["firstname"] . " " . $user["lastname"]; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label>Total Amount:</label>
                        <input type="number" step="0.01" name="total_amount" class="form-control" required>
                        <label>Status:</label>
                        <input type="text" name="status" class="form-control" required>
                        <button type="submit" name="create_order" class="btn btn-primary mt-3">Add Order</button>
                    </form>
                </div>
            </div> -->

            <div class="card">
                <div class="card-header bg-dark text-white">Order List</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo $order["order_id"]; ?></td>
                                    <td><?php echo $order["firstname"] . " " . $order["lastname"]; ?></td>
                                    <td><?php echo $order["total_amount"]; ?></td>
                                    <td><?php echo $order["status"]; ?></td>
                                    <td><?php echo $order["order_date"]; ?></td>
                                    <td>
                                        <a href="orders.php?delete=<?php echo $order["order_id"]; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
