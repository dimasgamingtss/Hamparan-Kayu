<?php
session_start();
require_once '../config/database.php';

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';
$product = null;

// Get product data
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
        
        if (!$product) {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = null; // price removed
    $category = $_POST['category'];
    
    // Validation
    if (empty($name) || empty($description) || empty($category)) {
        $error = "All fields are required";
    } else {
        $image_name = $product['image']; // Keep existing image by default
        
        // Handle file upload if new image is uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $file_type = $_FILES['image']['type'];
            
            if (!in_array($file_type, $allowed_types)) {
                $error = "Only JPG, PNG, and GIF images are allowed";
            } else {
                $file_size = $_FILES['image']['size'];
                if ($file_size > 5 * 1024 * 1024) { // 5MB limit
                    $error = "Image size must be less than 5MB";
                } else {
                    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $image_name = uniqid() . '.' . $file_extension;
                    $upload_path = '../uploads/' . $image_name;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                        // Delete old image if exists
                        if ($product['image'] && file_exists('../uploads/' . $product['image'])) {
                            unlink('../uploads/' . $product['image']);
                        }
                    } else {
                        $error = "Failed to upload image";
                    }
                }
            }
        }
        
        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, category = ?, image = ? WHERE id = ?");
                $stmt->execute([$name, $description, $category, $image_name, $product_id]);
                
                $success = "Product updated successfully!";
                
                // Update product data for display
                $product['name'] = $name;
                $product['description'] = $description;
                $product['category'] = $category;
                $product['image'] = $image_name;
                // Handle additional images upload
                if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
                    $total = count($_FILES['images']['name']);
                    for ($i = 0; $i < $total; $i++) {
                        if (!empty($_FILES['images']['name'][$i]) && $_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                            $fileType = $_FILES['images']['type'][$i];
                            $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                            if (!in_array($fileType, $allowed)) { continue; }
                            $size = $_FILES['images']['size'][$i];
                            if ($size > 5 * 1024 * 1024) { continue; }
                            $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
                            $imgName = uniqid() . '.' . $ext;
                            $path = '../uploads/' . $imgName;
                            if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $path)) {
                                $ins = $pdo->prepare("INSERT INTO product_images (product_id, image, sort_order) VALUES (?, ?, ?)");
                                $ins->execute([$product_id, $imgName, $i]);
                            }
                        }
                    }
                }

            } catch (PDOException $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin</title>
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
        
        .form-container {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333333;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #25D366;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .current-image {
            margin: 1rem 0;
            text-align: center;
        }
        
        .current-image img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .btn {
            padding: 12px 24px;
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
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        
        @media (max-width: 768px) {
            .form-container {
                margin: 0 1rem;
            }
            
            .form-actions {
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
                <div class="form-container">
                    <h1 class="page-title">Edit Product</h1>
                    
                    <?php if (!empty($success)): ?>
                        <div class="success-message">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($error)): ?>
                        <div class="error-message">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">Product Name *</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                        </div>
                        
                        
                        
                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="kursi" <?php echo $product['category'] === 'kursi' ? 'selected' : ''; ?>>Kursi / Chair</option>
                                <option value="meja" <?php echo $product['category'] === 'meja' ? 'selected' : ''; ?>>Meja / Table</option>
                                <option value="lemari" <?php echo $product['category'] === 'lemari' ? 'selected' : ''; ?>>Lemari / Cabinet</option>
                                <option value="sketsel" <?php echo $product['category'] === 'sketsel' ? 'selected' : ''; ?>>Sketsel / Room Divider</option>
                                <option value="bed" <?php echo $product['category'] === 'bed' ? 'selected' : ''; ?>>Bed</option>
                                <option value="other" <?php echo $product['category'] === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Product Image</label>
                            <?php if ($product['image']): ?>
                                <div class="current-image">
                                    <p><strong>Current Image:</strong></p>
                                    <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </div>
                            <?php endif; ?>
                            <input type="file" id="image" name="image" accept="image/*">
                            <small style="color: #666666;">Leave empty to keep current image. Max size: 5MB. Allowed formats: JPG, PNG, GIF</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="images">Additional Images (you can select multiple)</label>
                            <input type="file" id="images" name="images[]" accept="image/*" multiple>
                        </div>
                        
                        <div class="form-actions">
                            <a href="products.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Products
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
