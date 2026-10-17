# Hamparan Kayu - Furniture Custom Design Website

Website toko furniture berbasis PHP + MySQL dengan tampilan clean minimalis.

## Fitur

### Frontend
- ✅ Halaman Utama (Home) dengan hero section dan kategori produk
- ✅ Halaman Produk dengan pencarian dan filter kategori
- ✅ Halaman About dengan informasi perusahaan
- ✅ Halaman Contact dengan form pesan
- ✅ Tombol WhatsApp floating untuk setiap produk
- ✅ Desain responsive dan mobile-friendly
- ✅ Animasi smooth dan interaktif

### Admin Panel
- ✅ Login admin dengan autentikasi
- ✅ Dashboard dengan statistik
- ✅ CRUD produk (Create, Read, Update, Delete)
- ✅ Manajemen pesan contact
- ✅ Upload gambar produk

### Database
- ✅ Tabel products untuk menyimpan data produk
- ✅ Tabel admin untuk autentikasi admin
- ✅ Tabel contact_messages untuk pesan dari pengunjung

## Teknologi yang Digunakan

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Font**: Poppins (Google Fonts)
- **Icons**: Font Awesome 6.0
- **Styling**: Custom CSS dengan desain minimalis

## Warna Utama

- **Putih**: #ffffff (Background utama)
- **Abu Muda**: #f5f5f5 / #e0e0e0 (Card, navbar, footer)
- **Hitam Lembut**: #333333 (Teks)
- **Hijau WhatsApp**: #25D366 (Tombol beli)

## Instalasi

### 1. Persyaratan Sistem
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web server (Apache/Nginx)
- Ekstensi PHP: PDO, PDO_MySQL

### 2. Setup Database
1. Buat database MySQL baru dengan nama `furniture_db`
2. Import file `database.sql` ke database tersebut
3. Atau jalankan query SQL berikut:

```sql
-- Create database
CREATE DATABASE IF NOT EXISTS furniture_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE furniture_db;

-- Create tables
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category ENUM('kursi', 'meja', 'lemari') NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (username: admin, password: admin123)
INSERT INTO admin (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@indonesianfurniture.com');
```

### 3. Konfigurasi Database
1. Edit file `config/database.php`
2. Sesuaikan konfigurasi database:
   ```php
   $host = 'localhost';
   $dbname = 'furniture_db';
   $username = 'root';
   $password = '';
   ```

### 4. Setup Folder Uploads
1. Buat folder `uploads/` di root direktori
2. Pastikan folder memiliki permission write (755 atau 777)

### 5. Akses Website
- **Frontend**: `http://localhost/nama-folder/`
- **Admin Panel**: `http://localhost/nama-folder/admin/`
- **Login Admin**: 
  - Username: `admin`
  - Password: `admin123`

## Struktur File

```
├── index.php                 # Halaman utama
├── products.php             # Halaman produk
├── about.php                # Halaman about
├── contact.php              # Halaman contact
├── process_contact.php      # Proses form contact
├── database.sql             # File SQL database
├── config/
│   └── database.php         # Konfigurasi database
├── assets/
│   ├── css/
│   │   └── style.css        # File CSS utama
│   ├── js/
│   │   └── script.js        # File JavaScript
│   └── images/              # Folder gambar
├── uploads/                 # Folder upload gambar produk
└── admin/
    ├── login.php            # Halaman login admin
    ├── dashboard.php        # Dashboard admin
    ├── products.php         # Manajemen produk
    ├── logout.php           # Logout admin
    └── ...                  # File admin lainnya
```

## Fitur WhatsApp Integration

Website ini terintegrasi dengan WhatsApp untuk pemesanan produk:

1. **Tombol Floating**: Selalu muncul di pojok kiri bawah
2. **Tombol Produk**: Setiap produk memiliki tombol "Beli via WhatsApp"
3. **Format Pesan**: Otomatis mengirim pesan dengan format:
   ```
   Halo, saya ingin memesan produk: [Nama Produk] - [Harga Produk]
   ```

### Konfigurasi Nomor WhatsApp
Edit nomor WhatsApp di file-file berikut:
- `index.php` (floating button)
- `products.php` (tombol produk)
- `about.php` (floating button)
- `contact.php` (floating button)

Ganti `6282223321203` dengan nomor WhatsApp yang sebenarnya.

## Keamanan

- ✅ Password hashing menggunakan `password_hash()`
- ✅ Prepared statements untuk mencegah SQL injection
- ✅ Input validation dan sanitization
- ✅ Session management untuk admin
- ✅ CSRF protection (basic)

## Responsive Design

Website ini fully responsive dengan breakpoint:
- **Desktop**: > 768px
- **Tablet**: 768px - 480px
- **Mobile**: < 480px

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Kontribusi

1. Fork repository ini
2. Buat branch baru untuk fitur
3. Commit perubahan
4. Push ke branch
5. Buat Pull Request

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

Untuk bantuan atau pertanyaan, silakan hubungi:
- Email: hamparankayu@gmail.com
- WhatsApp: +6282223321203
- Alamat: Jl. Kalitekuk, RT.01/RW.01, Ngabul, Kec. Tahunan, Kabupaten Jepara, Jawa Tengah 59428

---

**Hamparan Kayu** - Furniture Custom Design | Handmade | Trusted
