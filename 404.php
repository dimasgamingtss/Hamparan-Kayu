<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found - Hamparan Kayu Furniture Custom Design</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f5f5f5;
        }
        
        .error-container {
            text-align: center;
            background-color: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 0 1rem;
        }
        
        .error-icon {
            font-size: 5rem;
            color: #25D366;
            margin-bottom: 1rem;
        }
        
        .error-title {
            font-size: 2.5rem;
            font-weight: 600;
            color: #333333;
            margin-bottom: 1rem;
        }
        
        .error-message {
            font-size: 1.1rem;
            color: #666666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
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
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #545b62;
            transform: translateY(-2px);
        }
        
        @media (max-width: 480px) {
            .error-container {
                padding: 2rem 1rem;
            }
            
            .error-title {
                font-size: 2rem;
            }
            
            .error-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-container">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            
            <h1 class="error-title">404</h1>
            
            <p class="error-message">
                Oops! Halaman yang Anda cari tidak ditemukan. 
                Mungkin halaman tersebut telah dipindahkan atau dihapus.
            </p>
            
            <div class="error-actions">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> Kembali ke Beranda
                </a>
                <a href="products.php" class="btn btn-secondary">
                    <i class="fas fa-box"></i> Lihat Produk
                </a>
            </div>
        </div>
    </div>
</body>
</html>
