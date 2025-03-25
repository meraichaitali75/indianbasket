<?php
session_start();
require_once __DIR__ . '/../includes/db/functions.php';

// Redirect to login if not an admin
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

$orderDetailsObj = new Functions();
$conn = $orderDetailsObj->getDatabaseConnection();

$errors = [];
$success = "";

// Create Order Detail
// if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_detail"])) {
//     $order_id = intval($_POST["order_id"]);
//     $product_id = intval($_POST["product_id"]);
//     $quantity = intval($_POST["quantity"]);
//     $price = floatval($_POST["price"]);

//     if (empty($order_id) || empty($product_id) || empty($quantity) || empty($price)) {
//         $errors[] = "All fields are required.";
//     } else {
//         $query = "INSERT INTO orderdetails (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
//         $stmt = $conn->prepare($query);
//         $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);

//         if ($stmt->execute()) {
//             header("Location: orderdetails.php?success=Order detail added successfully!");
//             exit();
//         } else {
//             $errors[] = "Error adding order detail: " . $conn->error;
//         }
//     }
// }

// Delete Order Detail
if (isset($_GET["delete"])) {
    $order_detail_id = intval($_GET["delete"]);

    $query = "DELETE FROM orderdetails WHERE order_detail_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_detail_id);

    if ($stmt->execute()) {
        header("Location: orderdetails.php?success=Order detail deleted successfully!");
        exit();
    } else {
        $errors[] = "Error deleting order detail: " . $conn->error;
    }
}

// Fetch All Order Details
$query = "SELECT od.order_detail_id, od.order_id, od.product_id, od.quantity, od.price, p.name AS product_name 
          FROM orderdetails od 
          LEFT JOIN products p ON od.product_id = p.product_id";

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error); // Display the SQL error
}

$orderDetails = $result->fetch_all(MYSQLI_ASSOC);

// Fetch Orders for Dropdown
$ordersQuery = "SELECT order_id FROM orders";
$ordersResult = $conn->query($ordersQuery);

if (!$ordersResult) {
    die("Orders query failed: " . $conn->error); // Display the SQL error
}
$orders = $ordersResult->fetch_all(MYSQLI_ASSOC);

// Fetch Products for Dropdown
$productsQuery = "SELECT product_id, name FROM products";
$productsResult = $conn->query($productsQuery);

if (!$productsResult) {
    die("Products query failed: " . $conn->error); // Display the SQL error
}
$products = $productsResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Order Details | Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">

</head>

<body>
    <?php include "sidebar.php"; ?>
    <div class="content">
        <div class="container mt-4">
            <h2>Manage Order Details</h2>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"> <?php echo htmlspecialchars($_GET['success']); ?> </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Add Order Detail -->
            <!-- <div class="card mb-4">
                <div class="card-header">Add New Order Detail</div>
                <div class="card-body">
                    <form method="POST">
                        <label>Order:</label>
                        <select name="order_id" class="form-control" required>
                            <?php foreach ($orders as $order): ?>
                                <option value="<?php echo $order['order_id']; ?>">Order #<?php echo $order['order_id']; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label>Product:</label>
                        <select name="product_id" class="form-control" required>
                            <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['product_id']; ?>"> <?php echo htmlspecialchars($product['name']); ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label>Quantity:</label>
                        <input type="number" name="quantity" class="form-control" required>

                        <label>Price:</label>
                        <input type="text" name="price" class="form-control" required>

                        <button type="submit" name="create_detail" class="btn btn-primary mt-3">Add Order Detail</button>
                    </form>
                </div>
            </div> -->

            <!-- Order Details Table -->
            <div class="card">
                <div class="card-header">Order Details List</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Order ID</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderDetails as $detail): ?>
                                <tr>
                                    <td><?php echo $detail['order_detail_id']; ?></td>
                                    <td><?php echo $detail['order_id']; ?></td>
                                    <td><?php echo htmlspecialchars($detail['product_name']); ?></td>
                                    <td><?php echo $detail['quantity']; ?></td>
                                    <td><?php echo $detail['price']; ?></td>
                                    <td>
                                        <a href="orderdetails.php?delete=<?php echo $detail['order_detail_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>