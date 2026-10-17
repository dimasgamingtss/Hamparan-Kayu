<?php
require_once 'config/database.php';

// Get search query and category filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Build query
$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $query .= " AND category = ?";
    $params[] = $category;
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Load additional images for each product
$productIds = array_column($products, 'id');
$imagesByProduct = [];
if (!empty($productIds)) {
    $in = implode(',', array_fill(0, count($productIds), '?'));
    $imgStmt = $pdo->prepare("SELECT product_id, image, sort_order FROM product_images WHERE product_id IN ($in) ORDER BY sort_order, id");
    $imgStmt->execute($productIds);
    foreach ($imgStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $imagesByProduct[$row['product_id']][] = $row['image'];
    }
}

// Get categories for filter
$cat_stmt = $pdo->query("SELECT DISTINCT category FROM products");
$categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);

// Category label mapping to English
$categoryLabels = [
    'kursi' => 'Chair',
    'meja' => 'Table',
    'lemari' => 'Cabinet',
    'sofa' => 'Sofa',
    'tempat tidur' => 'Bed',
    'ranjang' => 'Bed',
    'bed' => 'Bed',
    'sketsel' => 'Room Divider',
    'lainnya' => 'Other',
    'other' => 'Other'
];

// Ensure Bed and Other appear in the filter options
$categories = array_values(array_unique(array_merge($categories, ['bed', 'other', 'sketsel'])));

// Sanitize description to avoid accidental CSS showing as text
function sanitizeDescription($text) {
    if (!is_string($text)) return '';
    $text = preg_replace('/<style[\s\S]*?<\\/style>/i', '', $text);
    $text = preg_replace('/[.#]?[a-zA-Z][a-zA-Z0-9_-]*\s*\{[\s\S]*?\}/', '', $text);
    $text = preg_replace('/\s{2,}/', ' ', $text);
    return trim($text);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Hamparan Kayu Custom Furniture Design</title>
    <meta name="description" content="Lihat koleksi furniture custom Hamparan Kayu. Kursi, meja, lemari dengan desain minimalis modern dan kualitas premium. Harga terjangkau!">
    <meta name="keywords" content="furniture custom, kursi custom, meja custom, lemari custom, furniture handmade, furniture minimalis, hamparan kayu">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/logofavicon.png">
    <link rel="icon" type="image/png" sizes="48x48" href="/assets/images/logofavicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/logofavicon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <meta name="theme-color" content="#ffffff">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/magnific-popup.min.css">
    <style>
        /* Product Image Slider */
        .product-slider {
            position: relative;
            margin-bottom: 10px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .product-slider .slick-slide { padding: 0; position: relative; }
        .product-slider .slick-slide img {
            width: 100%; height: 250px; object-fit: cover; border-radius: 6px; cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease; user-select: none; pointer-events: auto;
        }
        .product-slider .slick-slide img:hover { transform: scale(1.02); }

        /* Navigation Arrows */
        .slick-prev, .slick-next {
            width: 40px; height: 40px; background: rgba(255,255,255,0.9); border-radius: 50%;
            display: flex !important; align-items: center; justify-content: center; z-index: 10;
            transition: all 0.3s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.2); border: none; outline: none; cursor: pointer;
            position: absolute; top: 50%; transform: translateY(-50%); opacity: 1;
        }
        .product-slider .slick-prev { left: 15px; }
        .product-slider .slick-next { right: 15px; }
        .product-slider .slick-prev i, .product-slider .slick-next i { color: #222; font-size: 16px; }
        .product-slider .slick-prev:hover, .product-slider .slick-next:hover { background: #fff !important; transform: translateY(-50%) scale(1.1); }

        /* Hide default slick dots */
        .slick-dots { display: none !important; }

        /* Thumbnail Navigation */
        .thumbnail-slider { margin: 10px 0 0; padding: 0 20px; }
        .thumbnail-slider .slick-slide { padding: 0 3px; cursor: pointer; opacity: 0.6; transition: all 0.3s ease; }
        .thumbnail-slider .slick-slide.slick-current, .thumbnail-slider .slick-slide:hover { opacity: 1; }
        .thumbnail-slider img { width: 100%; height: 60px; object-fit: cover; border-radius: 4px; border: 2px solid transparent; transition: all 0.3s ease; }
        .thumbnail-slider .slick-current img { border-color: #4CAF50; }

        /* Magnific close button */
        .mfp-close { color: #fff !important; opacity: 1 !important; font-size: 32px !important; z-index: 10000 !important; }

        /* Ensure CTA buttons align at the bottom across varying descriptions */
        .product-card { display: flex; flex-direction: column; height: 100%; }
        .product-image { flex: 0 0 auto; }
        .product-info { display: flex; flex-direction: column; flex: 1 1 auto; }
        .product-info .btn-whatsapp { margin-top: auto; width: 100%; }

        @media (max-width: 768px) {
            .product-slider .slick-slide img { height: 200px; }
            .thumbnail-slider img { height: 50px; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <div class="logo">
                    <img src="assets/images/logo.png" alt="Hamparan Kayu Logo" class="logo-image" height="24" style="height:24px;width:auto;">
                    <h2>Hamparan Kayu</h2>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php" class="active">Products</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
                <div class="hamburger"><span></span><span></span><span></span></div>
            </nav>
        </div>
    </header>

    <!-- Products Section -->
    <section class="products-section">
        <div class="container">
            <h1 class="page-title">Our Products</h1>

            <div class="search-filter">
                <form method="GET" action="products.php" class="search-form">
                    <div class="search-input">
                        <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="category-filter">
                        <select name="category" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <?php $label = isset($categoryLabels[strtolower($cat)]) ? $categoryLabels[strtolower($cat)] : ucfirst($cat); ?>
                                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($label); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>

            <div class="products-grid">
                <?php if (empty($products)): ?>
                    <div class="no-products">
                        <i class="fas fa-box-open"></i>
                        <h3>No products found</h3>
                        <p>Try adjusting your search or filter criteria.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php 
                                    $gallery = [];
                                    if (!empty($product['image'])) { $gallery[] = $product['image']; }
                                    if (isset($imagesByProduct[$product['id']])) { $gallery = array_merge($gallery, $imagesByProduct[$product['id']]); }
                                ?>
                                <?php if (!empty($gallery)): ?>
                                    <div class="product-slider">
                                        <?php foreach ($gallery as $img): ?>
                                            <div>
                                                <a href="uploads/<?php echo htmlspecialchars($img); ?>" class="image-zoom">
                                                    <img src="uploads/<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <?php if (count($gallery) > 1): ?>
                                    <div class="thumbnail-slider">
                                        <?php foreach ($gallery as $img): ?>
                                            <div>
                                                <img src="uploads/<?php echo htmlspecialchars($img); ?>" alt="Thumbnail">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-image"></i>
                                        <span>No Image Available</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="product-description"><?php echo htmlspecialchars(sanitizeDescription($product['description'])); ?></p>
                                <div class="product-meta">
                                    <?php $catKey = strtolower($product['category']); $catLabel = isset($categoryLabels[$catKey]) ? $categoryLabels[$catKey] : ucfirst($product['category']); ?>
                                    <span class="category"><?php echo htmlspecialchars($catLabel); ?></span>
                                </div>
                                <a href="https://wa.me/6282223321203?text=Halo, saya ingin memesan produk: <?php echo urlencode($product['name']); ?>" 
                                   class="btn-whatsapp" target="_blank">
                                    <i class="fab fa-whatsapp"></i> Buy via WhatsApp
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Hamparan Kayu</h3>
                    <p>Menghadirkan furniture custom design berkualitas dengan desain minimalis yang elegan. Handmade | Custom Design | Trusted.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php">Products</a></li>
                        <li><a href="about.php">About</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <p><i class="fas fa-phone"></i> +6282223321203</p>
                    <p><i class="fas fa-envelope"></i> hamparankayu@gmail.com</p>
                    <p><i class="fas fa-map-marker-alt"></i> Jl. Kalitekuk, RT.01/RW.01, Ngabul, Kec. Tahunan, Kabupaten Jepara, Jawa Tengah 59428</p>
                    <p><i class="fas fa-tag"></i> Handmade | Custom Design | Trusted</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Hamparan Kayu. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts: jQuery first, then Slick, Magnific, then site script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.1.0/jquery.magnific-popup.min.js"></script>
    <script>
    $(document).ready(function() {
        // Initialize product sliders
        $('.product-slider').each(function() {
            var $slider = $(this);
            var $thumbnailSlider = $(this).siblings('.thumbnail-slider');

            $slider.slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: true,
                fade: true,
                asNavFor: $thumbnailSlider.length ? $thumbnailSlider : null,
                prevArrow: '<button type="button" class="slick-prev" aria-label="Sebelumnya"><i class="fa-solid fa-chevron-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next" aria-label="Berikutnya"><i class="fa-solid fa-chevron-right"></i></button>',
                dots: false,
                infinite: true,
                speed: 300,
                autoplay: false,
                pauseOnHover: true
            });

            if ($thumbnailSlider.length) {
                $thumbnailSlider.slick({
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    asNavFor: $slider,
                    dots: false,
                    centerMode: false,
                    focusOnSelect: true,
                    arrows: false,
                    infinite: true,
                    speed: 300,
                    responsive: [
                        { breakpoint: 768, settings: { slidesToShow: 3, arrows: false } },
                        { breakpoint: 480, settings: { slidesToShow: 2, arrows: false } }
                    ]
                });

                $thumbnailSlider.on('click', '.slick-slide', function(e) {
                    e.preventDefault();
                    var slideIndex = $(this).data('slick-index');
                    $slider.slick('slickGoTo', slideIndex);
                });
            }
        });

        // Magnific Popup gallery per product
        $('.product-card .product-image').each(function() {
            $(this).magnificPopup({
                delegate: 'a.image-zoom',
                type: 'image',
                gallery: { enabled: true, tPrev: 'Sebelumnya', tNext: 'Berikutnya', tCounter: '%curr% / %total%' },
                closeBtnInside: false,
                closeMarkup: '<button title="Tutup" type="button" class="mfp-close">&times;</button>',
                fixedContentPos: true,
                mainClass: 'mfp-fade mfp-with-zoom',
                removalDelay: 200,
                image: { verticalFit: true },
                zoom: { enabled: true, duration: 300, opener: function(el){ return el.find('img'); } }
            });
        });
    });
    </script>
    <script src="assets/js/script.js"></script>
</body>
</html>


