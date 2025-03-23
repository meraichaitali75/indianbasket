<?php
session_start();
require_once __DIR__ . '/../includes/db/functions.php';

// Check if admin is logged in
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit();
}

$functions = new Functions();
$conn = $functions->getDatabaseConnection();

// Get Banner ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: banners.php");
    exit();
}

$banner_id = intval($_GET['id']);

// Fetch Banner to Delete Image File
$query = "SELECT image FROM banners WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $banner_id);
$stmt->execute();
$result = $stmt->get_result();
$banner = $result->fetch_assoc();

if ($banner) {
    $filePath = __DIR__ . "/../" . $banner['image'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Delete from Database
    $deleteQuery = "DELETE FROM banners WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $banner_id);
    
    if ($stmt->execute()) {
        header("Location: banners.php?success=Banner deleted successfully.");
        exit();
    } else {
        header("Location: banners.php?error=Failed to delete banner.");
        exit();
    }
} else {
    header("Location: banners.php?error=Banner not found.");
    exit();
}
?>
