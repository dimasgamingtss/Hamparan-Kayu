<?php
session_start();

// Check if admin is logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // If logged in, redirect to dashboard
    header("Location: dashboard.php");
    exit();
} else {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}
?>
