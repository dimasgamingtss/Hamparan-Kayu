<?php
session_start();
require_once '../config/database.php';

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_products FROM products");
$total_products = $stmt->fetch()['total_products'];

$stmt = $pdo->query("SELECT COUNT(*) as total_messages FROM contact_messages");
$total_messages = $stmt->fetch()['total_messages'];

$stmt = $pdo->query("SELECT COUNT(*) as total_categories FROM (SELECT DISTINCT category FROM products) as categories");
$total_categories = $stmt->fetch()['total_categories'];

// Get recent products
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 5");
$recent_products = $stmt->fetchAll();

// Get recent messages
$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
$recent_messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hamparan Kayu Furniture Custom Design</title>
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background-color: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .stat-card i {
            font-size: 2rem;
            color: #25D366;
            margin-bottom: 1rem;
        }
        
        .stat-card h3 {
            font-size: 2rem;
            color: #333333;
            margin-bottom: 0.5rem;
        }
        
        .stat-card p {
            color: #666666;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        .dashboard-card {
            background-color: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .dashboard-card h2 {
            color: #333333;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        
        .recent-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .recent-item:last-child {
            border-bottom: none;
        }
        
        .recent-item .item-info h4 {
            color: #333333;
            margin-bottom: 0.25rem;
        }
        
        .recent-item .item-info p {
            color: #666666;
            font-size: 0.9rem;
        }
        
        .recent-item .item-date {
            color: #999999;
            font-size: 0.8rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
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
        
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
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
                        <a href="change_password.php"><i class="fas fa-key"></i> Change Password</a>
                        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </nav>
            </div>
        </header>

        <main class="dashboard-content">
            <div class="container">
                <h1 class="page-title">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h1>
                
                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <i class="fas fa-box"></i>
                        <h3><?php echo $total_products; ?></h3>
                        <p>Total Products</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-envelope"></i>
                        <h3><?php echo $total_messages; ?></h3>
                        <p>Contact Messages</p>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-tags"></i>
                        <h3><?php echo $total_categories; ?></h3>
                        <p>Product Categories</p>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <h2><i class="fas fa-box"></i> Recent Products</h2>
                        <?php if (empty($recent_products)): ?>
                            <p>No products found.</p>
                        <?php else: ?>
                            <?php foreach ($recent_products as $product): ?>
                                <div class="recent-item">
                                    <div class="item-info">
                                        <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                        <?php
                                            $dashCatMap = [
                                                'kursi' => 'Chair',
                                                'meja' => 'Table',
                                                'lemari' => 'Cabinet',
                                                'sofa' => 'Sofa',
                                                'sketsel' => 'Room Divider',
                                                'bed' => 'Bed',
                                                'lainnya' => 'Other',
                                                'other' => 'Other'
                                            ];
                                            $dcat = strtolower($product['category']);
                                            $dcatLabel = isset($dashCatMap[$dcat]) ? $dashCatMap[$dcat] : ucfirst($product['category']);
                                        ?>
                                        <p><?php echo htmlspecialchars($dcatLabel); ?> · Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                                    </div>
                                    <div class="item-date">
                                        <?php echo date('M j, Y', strtotime($product['created_at'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="action-buttons">
                            <a href="products.php" class="btn btn-primary">View All Products</a>
                            <a href="add_product.php" class="btn btn-secondary">Add New Product</a>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <h2><i class="fas fa-envelope"></i> Recent Messages</h2>
                        <?php if (empty($recent_messages)): ?>
                            <p>No messages found.</p>
                        <?php else: ?>
                            <?php foreach ($recent_messages as $message): ?>
                                <div class="recent-item">
                                    <div class="item-info">
                                        <h4><?php echo htmlspecialchars($message['name']); ?></h4>
                                        <p><?php echo htmlspecialchars($message['subject']); ?></p>
                                    </div>
                                    <div class="item-date">
                                        <?php echo date('M j, Y', strtotime($message['created_at'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="action-buttons">
                            <a href="messages.php" class="btn btn-primary">View All Messages</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
