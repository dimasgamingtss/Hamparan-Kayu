<?php
// Script untuk mengupload gambar hero
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['hero_image'])) {
    $upload_dir = 'assets/images/';
    $target_file = $upload_dir . 'hero-furniture.jpg';
    
    // Periksa apakah file adalah gambar
    $imageFileType = strtolower(pathinfo($_FILES["hero_image"]["name"], PATHINFO_EXTENSION));
    $allowed_types = array('jpg', 'jpeg', 'png', 'webp');
    
    if (!in_array($imageFileType, $allowed_types)) {
        $error = "Hanya file JPG, JPEG, PNG & WebP yang diperbolehkan.";
    } else {
        // Upload file
        if (move_uploaded_file($_FILES["hero_image"]["tmp_name"], $target_file)) {
            $success = "Gambar hero berhasil diupload!";
        } else {
            $error = "Gagal mengupload gambar.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Gambar Hero - Hamparan Kayu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 2rem;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333333;
            text-align: center;
            margin-bottom: 2rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333333;
        }
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
        }
        .btn {
            background-color: #25D366;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            width: 100%;
        }
        .btn:hover {
            background-color: #128C7E;
        }
        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            margin-bottom: 2rem;
        }
        .back-link {
            text-align: center;
            margin-top: 2rem;
        }
        .back-link a {
            color: #25D366;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📸 Upload Gambar Hero</h1>
        
        <div class="info">
            <strong>Info:</strong> Upload gambar furniture minimalis untuk halaman utama website.
            <br>Format: JPG, JPEG, PNG, WebP | Ukuran: Maksimal 5MB
        </div>
        
        <?php if (isset($success)): ?>
            <div class="message success">
                ✅ <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error">
                ❌ <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="hero_image">Pilih Gambar Furniture:</label>
                <input type="file" id="hero_image" name="hero_image" accept="image/*" required>
            </div>
            
            <button type="submit" class="btn">Upload Gambar Hero</button>
        </form>
        
        <div class="back-link">
            <a href="index.php">← Kembali ke Website</a>
        </div>
    </div>
</body>
</html>
