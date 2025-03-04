<?php
session_start();
require_once __DIR__ . '/../includes/db/functions.php';

if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

$billingObj = new Functions();
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_POST['user_id']);
    $street_address = $_POST['street_address'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $zip_code = $_POST['zip_code'];

    if (empty($street_address) || empty($city) || empty($province) || empty($zip_code)) {
        $errors[] = "All fields are required.";
    } else {
        $conn = $billingObj->getDatabaseConnection();
        $query = "INSERT INTO billing_addresses (user_id, street_address, city, province, zip_code) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("issss", $user_id, $street_address, $city, $province, $zip_code);
        
        if ($stmt->execute()) {
            $success = "Billing address added successfully!";
        } else {
            $errors[] = "Error adding billing address.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Billing Address</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Add Billing Address</h2>
    
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

    <form action="add_billing_address.php" method="POST">
        <div class="mb-3">
            <label>User ID:</label>
            <input type="number" name="user_id" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Street Address:</label>
            <input type="text" name="street_address" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>City:</label>
            <input type="text" name="city" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Province:</label>
            <input type="text" name="province" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Zip Code:</label>
            <input type="text" name="zip_code" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Add Address</button>
        <a href="billing_address.php" class="btn btn-secondary">Back</a>
    </form>
</div>

</body>
</html>
