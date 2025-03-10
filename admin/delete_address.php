<?php
require_once __DIR__ . '/../includes/db/functions.php';

$usersObj = new Functions();
$conn = $usersObj->getDatabaseConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("DELETE FROM billing_addresses WHERE address_id = ?");
    $stmt->bind_param("i", $_POST['address_id']);

    if ($stmt->execute()) {
        echo "Address deleted successfully!";
    } else {
        echo "Error deleting address.";
    }
}
?>
