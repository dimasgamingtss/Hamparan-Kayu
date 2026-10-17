<?php
require_once '../config/database.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS product_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        image VARCHAR(255) NOT NULL,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql);
    echo "Table 'product_images' created successfully or already exists.";
} catch (PDOException $e) {
    die("Error creating table: " . $e->getMessage());
}
?>
