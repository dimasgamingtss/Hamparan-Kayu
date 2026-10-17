<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($subject)) {
        $errors[] = "Subject is required";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $subject, $message]);
            
            // Redirect with success message
            header("Location: contact.php?success=1");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
    
    if (!empty($errors)) {
        // Redirect with error message
        $error_msg = urlencode(implode(", ", $errors));
        header("Location: contact.php?error=" . $error_msg);
        exit();
    }
} else {
    // If not POST request, redirect to contact page
    header("Location: contact.php");
    exit();
}
?>
