<?php
session_start();
require_once '../config/database.php';

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Get all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin</title>
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
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
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
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background-color: #e0a800;
        }
        
        .products-table {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .table-header {
            background-color: #f5f5f5;
            padding: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .table-header h2 {
            color: #333333;
            margin: 0;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333333;
        }
        
        td {
            color: #666666;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .category-badge {
            background-color: #e0e0e0;
            color: #333333;
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #666666;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #e0e0e0;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }
            
            .admin-nav {
                flex-direction: column;
                gap: 1rem;
            }
            
            .admin-nav .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .table-container {
                font-size: 0.9rem;
            }
            
            th, td {
                padding: 0.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
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
                <?php if (isset($_GET['success'])): ?>
                    <div class="success-message" style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; text-align: center;">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars(urldecode($_GET['success'])); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="error-message" style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; text-align: center;">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
                    </div>
                <?php endif; ?>
                
                <div class="page-header">
                    <h1 class="page-title">Manage Products</h1>
                    <a href="add_product.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                </div>
                
                <div class="products-table">
                    <div class="table-header">
                        <h2>All Products</h2>
                    </div>
                    
                    <div class="table-container">
                        <?php if (empty($products)): ?>
                            <div class="empty-state">
                                <i class="fas fa-box-open"></i>
                                <h3>No products found</h3>
                                <p>Start by adding your first product.</p>
                                <a href="add_product.php" class="btn btn-primary">Add Product</a>
                            </div>
                        <?php else: ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td>
                                                <?php if ($product['image']): ?>
                                                    <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                         class="product-image">
                                                <?php else: ?>
                                                    <div class="product-image" style="background-color: #e0e0e0; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-image" style="color: #999;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                                <br>
                                                <small><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                    $adminCatMap = [
                                                        'kursi' => 'Chair',
                                                        'meja' => 'Table',
                                                        'lemari' => 'Cabinet',
                                                        'sofa' => 'Sofa',
                                                        'sketsel' => 'Room Divider',
                                                        'bed' => 'Bed',
                                                        'lainnya' => 'Other',
                                                        'other' => 'Other'
                                                    ];
                                                    $catKey = strtolower($product['category']);
                                                    $catLabel = isset($adminCatMap[$catKey]) ? $adminCatMap[$catKey] : ucfirst($product['category']);
                                                ?>
                                                <span class="category-badge"><?php echo htmlspecialchars($catLabel); ?></span>
                                            </td>
                                            
                                            <td>
                                                <?php echo date('M j, Y', strtotime($product['created_at'])); ?>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                                                       class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <a href="delete_product.php?id=<?php echo $product['id']; ?>" 
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirm('Are you sure you want to delete this product?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
