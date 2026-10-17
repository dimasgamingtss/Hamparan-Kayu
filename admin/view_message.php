<?php
session_start();
require_once '../config/database.php';

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$message = null;

// Get message data
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $message_id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
        $stmt->execute([$message_id]);
        $message = $stmt->fetch();
        
        if (!$message) {
            header("Location: messages.php?error=Message not found");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: messages.php?error=Database error: " . $e->getMessage());
        exit();
    }
} else {
    header("Location: messages.php?error=Invalid message ID");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Message - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-dashboard {
            min-height: 100vh;
            background-color: #f5f5f5;
        }
        
        .admin-header {
            background-color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .admin-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-nav h1 {
            color: #333333;
            font-size: 1.5rem;
        }
        
        .admin-nav .nav-links {
            display: flex;
            gap: 1rem;
        }
        
        .admin-nav a {
            color: #333333;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        
        .admin-nav a:hover {
            background-color: #f5f5f5;
        }
        
        .admin-nav .logout {
            color: #dc3545;
        }
        
        .dashboard-content {
            padding: 2rem 0;
        }
        
        .message-container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .message-header {
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        
        .message-header h1 {
            color: #333333;
            margin-bottom: 0.5rem;
        }
        
        .message-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .meta-item {
            display: flex;
            flex-direction: column;
        }
        
        .meta-label {
            font-weight: 600;
            color: #333333;
            margin-bottom: 0.25rem;
        }
        
        .meta-value {
            color: #666666;
        }
        
        .meta-value a {
            color: #25D366;
            text-decoration: none;
        }
        
        .meta-value a:hover {
            text-decoration: underline;
        }
        
        .subject-badge {
            background-color: #e0e0e0;
            color: #333333;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-block;
        }
        
        .message-content {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #25D366;
        }
        
        .message-content p {
            color: #333333;
            line-height: 1.6;
            margin: 0;
            white-space: pre-wrap;
        }
        
        .message-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #e0e0e0;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background-color: #25D366;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #128C7E;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #545b62;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        @media (max-width: 768px) {
            .message-container {
                margin: 0 1rem;
            }
            
            .message-meta {
                grid-template-columns: 1fr;
            }
            
            .message-actions {
                flex-direction: column;
            }
            
            .admin-nav {
                flex-direction: column;
                gap: 1rem;
            }
            
            .admin-nav .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="admin-dashboard">
        <header class="admin-header">
            <div class="container">
                <nav class="admin-nav">
                    <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
                    <div class="nav-links">
                        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                        <a href="products.php"><i class="fas fa-box"></i> Products</a>
                        <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
                        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </nav>
            </div>
        </header>

        <main class="dashboard-content">
            <div class="container">
                <div class="message-container">
                    <div class="message-header">
                        <h1><i class="fas fa-envelope"></i> Message Details</h1>
                        <span class="subject-badge"><?php echo htmlspecialchars($message['subject']); ?></span>
                    </div>
                    
                    <div class="message-meta">
                        <div class="meta-item">
                            <span class="meta-label">From:</span>
                            <span class="meta-value"><?php echo htmlspecialchars($message['name']); ?></span>
                        </div>
                        
                        <div class="meta-item">
                            <span class="meta-label">Email:</span>
                            <span class="meta-value">
                                <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>">
                                    <?php echo htmlspecialchars($message['email']); ?>
                                </a>
                            </span>
                        </div>
                        
                        <div class="meta-item">
                            <span class="meta-label">Phone:</span>
                            <span class="meta-value">
                                <?php if ($message['phone']): ?>
                                    <a href="tel:<?php echo htmlspecialchars($message['phone']); ?>">
                                        <?php echo htmlspecialchars($message['phone']); ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color: #999;">Not provided</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="meta-item">
                            <span class="meta-label">Date:</span>
                            <span class="meta-value"><?php echo date('F j, Y \a\t g:i A', strtotime($message['created_at'])); ?></span>
                        </div>
                    </div>
                    
                    <div class="message-content">
                        <p><?php echo htmlspecialchars($message['message']); ?></p>
                    </div>
                    
                    <div class="message-actions">
                        <a href="messages.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Messages
                        </a>
                        <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>?subject=Re: <?php echo urlencode($message['subject']); ?>" 
                           class="btn btn-primary">
                            <i class="fas fa-reply"></i> Reply via Email
                        </a>
                        <a href="delete_message.php?id=<?php echo $message['id']; ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('Are you sure you want to delete this message?')">
                            <i class="fas fa-trash"></i> Delete Message
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
