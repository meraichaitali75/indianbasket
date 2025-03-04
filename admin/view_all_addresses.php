<?php
session_start();
require_once __DIR__ . '/../includes/db/functions.php';

if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}

$billingObj = new Functions();
$addresses = [];
$errors = [];

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);

    $conn = $billingObj->getDatabaseConnection();
    $query = "SELECT * FROM billing_addresses WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result) {
        $addresses = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $errors[] = "Error fetching addresses.";
    }
} else {
    $errors[] = "Invalid request.";
}

// Handle Delete Request
if (isset($_GET['delete'])) {
    $address_id = intval($_GET['delete']);
    $deleteQuery = "DELETE FROM billing_addresses WHERE address_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $address_id);

    if ($stmt->execute()) {
        header("Location: view_all_addresses.php?user_id=" . $user_id);
        exit();
    } else {
        $errors[] = "Error deleting address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Addresses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>All Addresses for User ID: <?php echo htmlspecialchars($user_id); ?></h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <table class="table table-bordered mt-3">
        <thead class="table-dark">
            <tr>
                <th>Address ID</th>
                <th>Street Address</th>
                <th>City</th>
                <th>Province</th>
                <th>Zip Code</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($addresses as $address): ?>
                <tr>
                    <td><?php echo $address['address_id']; ?></td>
                    <td><?php echo $address['street_address']; ?></td>
                    <td><?php echo $address['city']; ?></td>
                    <td><?php echo $address['province']; ?></td>
                    <td><?php echo $address['zip_code']; ?></td>
                    <td>
                        <a href="edit_billing_address.php?id=<?php echo $address['address_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="view_all_addresses.php?user_id=<?php echo $user_id; ?>&delete=<?php echo $address['address_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="billing_addresses.php" class="btn btn-secondary">Back</a>
</div>

</body>
</html>
