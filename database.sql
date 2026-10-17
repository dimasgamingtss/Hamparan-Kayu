-- Create database
CREATE DATABASE IF NOT EXISTS furniture_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE furniture_db;

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category ENUM('kursi', 'meja', 'lemari', 'sketsel', 'bed', 'other', 'sofa') NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create admin table
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create contact_messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create product_images table for multiple product images
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert default admin user (password: admin123)
INSERT INTO admin (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'hamparankayu@gmail.com');

-- Insert sample products
INSERT INTO products (name, description, price, category, image) VALUES 
('Kursi Minimalis Modern', 'Kursi dengan desain minimalis modern yang nyaman untuk ruang tamu atau ruang kerja', 850000, 'kursi', 'chair1.jpg'),
('Meja Makan Kayu Jati', 'Meja makan dengan bahan kayu jati premium, cocok untuk keluarga 4-6 orang', 2500000, 'meja', 'table1.jpg'),
('Lemari Pakaian 3 Pintu', 'Lemari pakaian dengan 3 pintu dan cermin, kapasitas besar untuk menyimpan pakaian', 1800000, 'lemari', 'wardrobe1.jpg'),
('Kursi Kantor Ergonomic', 'Kursi kantor dengan desain ergonomis untuk kenyamanan kerja yang maksimal', 1200000, 'kursi', 'chair2.jpg'),
('Meja Kerja Minimalis', 'Meja kerja dengan desain minimalis, cocok untuk home office atau ruang belajar', 950000, 'meja', 'table2.jpg'),
('Lemari Buku Kayu', 'Lemari buku dengan bahan kayu solid, desain klasik yang elegan', 1500000, 'lemari', 'bookshelf1.jpg'),
('Kursi Bar Stool', 'Kursi bar dengan tinggi standar, cocok untuk kitchen bar atau bar area', 650000, 'kursi', 'stool1.jpg'),
('Meja Kopi Modern', 'Meja kopi dengan desain modern, cocok untuk ruang tamu minimalis', 750000, 'meja', 'coffee-table1.jpg'),
('Lemari Dapur Modular', 'Lemari dapur modular dengan sistem rak yang dapat disesuaikan', 2200000, 'lemari', 'kitchen-cabinet1.jpg'),
('Kursi Gaming Premium', 'Kursi gaming dengan bahan berkualitas tinggi dan kenyamanan maksimal', 1800000, 'kursi', 'gaming-chair1.jpg');
