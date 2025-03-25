<?php
session_start();
require_once __DIR__ . '/../includes/db/functions.php';
if (!isset($_SESSION["admin_email"])) {
    header("Location: login.php");
    exit;
}
$functions = new Functions();
$conn = $functions->getDatabaseConnection();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("UPDATE blogs SET status = IF(status = 'active', 'inactive', 'active') WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
header("Location: blogs.php");
exit;
