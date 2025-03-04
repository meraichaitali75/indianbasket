<?php
session_start();
session_destroy();
header("Location: login.php"); // Redirect to admin login page
exit;
?>
