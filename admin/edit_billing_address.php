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
$address = [];

if (isset($_GET['id'])) {
    $address_id = intval($_GET['id']);

    $conn = $billingObj->getDatabaseConnection();
    $query = "SELECT * FROM billing_addresses WHERE address_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $address_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $address = $result->fetch_assoc();
    } else {
        $errors[] = "Address not found.";
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $street_address = $_POST['street_address'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $zip_code = $_POST['zip_code'];

    if (empty($street_address) || empty($city) || empty($province) || empty($zip_code)) {
        $errors[] = "All fields are required.";
    } else {
        $query = "UPDATE billing_addresses SET street_address = ?, city = ?, province = ?, zip_code = ? WHERE address_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $street_address, $city, $province, $zip_code, $address_id);

        if ($stmt->execute()) {
            $success = "Address updated successfully!";
            header("Location: view_all_addresses.php?user_id=" . $address['user_id']);
            exit();
        } else {
            $errors[] = "Error updating address.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Address</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Edit Address</h2>

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

    <form action="" method="POST">
        <div class="mb-3">
            <label>Street Address:</label>
            <input type="text" name="street_address" class="form-control" value="<?php echo htmlspecialchars($address['street_address']); ?>" required>
        </div>
        <div class="mb-3">
            <label>City:</label>
            <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($address['city']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Province:</label>
            <input type="text" name="province" class="form-control" value="<?php echo htmlspecialchars($address['province']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Zip Code:</label>
            <input type="text" name="zip_code" class="form-control" value="<?php echo htmlspecialchars($address['zip_code']); ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Update Address</button>
        <a href="view_all_addresses.php?user_id=<?php echo $address['user_id']; ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>
