<?php
session_start();
require_once '../config/database.php';

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $message_id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$message_id]);
        
        header("Location: messages.php?success=Message deleted successfully");
        exit();
    } catch (PDOException $e) {
        header("Location: messages.php?error=Database error: " . $e->getMessage());
        exit();
    }
} else {
    header("Location: messages.php?error=Invalid message ID");
    exit();
}
?>
