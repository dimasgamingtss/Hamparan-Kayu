<?php
session_start();
require_once '../config/database.php';

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];
    
    try {
        // Get product info first
        $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if ($product) {
            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            
            // Delete image file if exists
            if ($product['image'] && file_exists('../uploads/' . $product['image'])) {
                unlink('../uploads/' . $product['image']);
            }
            
            header("Location: products.php?success=Product deleted successfully");
            exit();
        } else {
            header("Location: products.php?error=Product not found");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: products.php?error=Database error: " . $e->getMessage());
        exit();
    }
} else {
    header("Location: products.php?error=Invalid product ID");
    exit();
}
?>
