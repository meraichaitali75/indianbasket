<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/db/functions.php';
$functions = new Functions();

// Clear all session data including cart
$_SESSION = [];
session_unset();
session_destroy();

// Start a fresh session with empty cart
session_start();

// Add a success message
$_SESSION['logout_message'] = "You have been successfully logged out.";

// Redirect to login page
header("Location: ../login.php");
exit();
?>