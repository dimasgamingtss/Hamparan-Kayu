<?php
// Database configuration
$host = 'localhost';
$dbname = 'furniture_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Ensure category ENUM includes latest items (idempotent check)
    try {
        $enumSql = "SHOW COLUMNS FROM products LIKE 'category'";
        $col = $pdo->query($enumSql)->fetch();
        if ($col && isset($col['Type'])) {
            $type = $col['Type'];
            $needed = ["kursi","meja","lemari","sketsel","bed","other","sofa"];
            $missing = false;
            foreach ($needed as $opt) {
                if (strpos($type, "'".$opt."'") === false) { $missing = true; break; }
            }
            if ($missing) {
                $pdo->exec("ALTER TABLE products MODIFY category ENUM('kursi','meja','lemari','sketsel','bed','other','sofa') NOT NULL");
            }
        }
    } catch (Exception $e) {
        // ignore if table doesn't exist yet
    }

    // Ensure product_images table exists for multiple images
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS product_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            image VARCHAR(255) NOT NULL,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } catch (Exception $e) {
        // ignore
    }
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
