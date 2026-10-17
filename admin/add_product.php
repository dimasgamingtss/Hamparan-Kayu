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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = null; // price removed
    $category = $_POST['category'];
    
    // Validation
    if (empty($name) || empty($description) || empty($category)) {
        $error = "All fields are required";
    } else {
        $image_name = '';
        
        // Handle file upload
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
                    
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                        $error = "Failed to upload image";
                    }
                }
            }
        }
        
        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO products (name, description, category, image) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $description, $category, $image_name]);
                $newProductId = $pdo->lastInsertId();

                // Handle multiple additional images upload
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
                                $ins->execute([$newProductId, $imgName, $i]);
                            }
                        }
                    }
                }

                $success = "Product added successfully!";
                
                // Clear form data
                $name = $description = $category = '';
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
    <title>Add Product - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Image upload and preview styles */
        .image-upload-container {
            margin-bottom: 20px;
        }
        
        .image-preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }
        
        .image-preview {
            position: relative;
            width: 150px;
            height: 150px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .image-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255,0,0,0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
        }
        
        .file-input-wrapper {
            position: relative;
            margin-bottom: 10px;
        }
        
        .file-input-wrapper input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        
        .file-input-label {
            display: inline-block;
            padding: 10px 15px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .file-input-label:hover {
            background-color: #e0e0e0;
        }
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
                    <h1 class="page-title">Add New Product</h1>
                    
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
                            <input type="text" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea id="description" name="description" required><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                        </div>
                        
                        
                        
                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="kursi" <?php echo (isset($category) && $category === 'kursi') ? 'selected' : ''; ?>>Kursi / Chair</option>
                                <option value="meja" <?php echo (isset($category) && $category === 'meja') ? 'selected' : ''; ?>>Meja / Table</option>
                                <option value="lemari" <?php echo (isset($category) && $category === 'lemari') ? 'selected' : ''; ?>>Lemari / Cabinet</option>
                                <option value="sketsel" <?php echo (isset($category) && $category === 'sketsel') ? 'selected' : ''; ?>>Sketsel / Room Divider</option>
                                <option value="bed" <?php echo (isset($category) && $category === 'bed') ? 'selected' : ''; ?>>Bed</option>
                                <option value="other" <?php echo (isset($category) && $category === 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Main Image</label>
                            <input type="file" id="image" name="image" accept="image/*">
                            <small style="color: #666666;">Max size: 5MB. Allowed formats: JPG, PNG, GIF</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Additional Images</label>
                            <div class="image-upload-container">
                                <div class="file-input-wrapper">
                                    <label class="file-input-label">
                                        <i class="fas fa-images"></i> Select Multiple Images
                                        <input type="file" id="images" name="images[]" accept="image/*" multiple>
                                    </label>
                                    <small style="display: block; color: #666666; margin-top: 5px;">Max size per image: 5MB. You can select multiple images.</small>
                                </div>
                                <div id="imagePreview" class="image-preview-container">
                                    <!-- Image previews will be added here -->
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <a href="products.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Products
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
    
    <script>
    // Image preview functionality
    document.addEventListener('DOMContentLoaded', function() {
        const imageInput = document.getElementById('images');
        const imagePreview = document.getElementById('imagePreview');
        
        imageInput.addEventListener('change', function(e) {
            // Clear existing previews
            imagePreview.innerHTML = '';
            
            // Get selected files
            const files = e.target.files;
            
            // Process each file
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                // Check file type
                if (!file.type.match('image.*')) {
                    continue;
                }
                
                // Create preview element
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'image-preview';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'remove-image';
                    removeBtn.innerHTML = '&times;';
                    removeBtn.onclick = function() {
                        preview.remove();
                        updateFileInput(files, file);
                    };
                    
                    preview.appendChild(img);
                    preview.appendChild(removeBtn);
                    imagePreview.appendChild(preview);
                };
                
                reader.readAsDataURL(file);
            }
        });
        
        // Function to update file input when an image is removed
        function updateFileInput(allFiles, fileToRemove) {
            const newFiles = [];
            for (let i = 0; i < allFiles.length; i++) {
                if (allFiles[i] !== fileToRemove) {
                    newFiles.push(allFiles[i]);
                }
            }
            
            // Create new DataTransfer and set it as the files property of the input
            const dataTransfer = new DataTransfer();
            newFiles.forEach(file => dataTransfer.items.add(file));
            imageInput.files = dataTransfer.files;
        }
    });
    </script>
</body>
</html>
