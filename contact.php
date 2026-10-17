<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Hamparan Kayu Furniture Custom Design</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/logofavicon.png">
    <link rel="icon" type="image/png" sizes="48x48" href="/assets/images/logofavicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/logofavicon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <meta name="theme-color" content="#ffffff">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <li><a href="products.php">Products</a></li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php" class="active">Contact</a></li>
                </ul>
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </nav>
        </div>
    </header>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <h1 class="page-title">Contact Us</h1>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="success-message" style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; text-align: center;">
                    <i class="fas fa-check-circle"></i> Pesan Anda telah berhasil dikirim! Kami akan segera menghubungi Anda.
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message" style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; text-align: center;">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
                </div>
            <?php endif; ?>
            
            <div class="contact-content">
                <div class="contact-info">
                    <h2>Get in Touch</h2>
                    <p>Hubungi kami untuk informasi produk custom design, pemesanan, atau pertanyaan lainnya. 
                       Tim kami siap melayani Anda dengan ramah dan profesional. <strong>Handmade | Custom Design | Trusted</strong></p>
                    
                    <div class="contact-details">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                                                         <div class="contact-text">
                                 <h3>Address</h3>
                                 <p>Jl. Kalitekuk, RT.01/RW.01<br>Ngabul, Kec. Tahunan<br>Kabupaten Jepara, Jawa Tengah 59428</p>
                             </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                                                         <div class="contact-text">
                                 <h3>Phone</h3>
                                 <p>+6282223321203<br>(Phone & WhatsApp)</p>
                             </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                                                         <div class="contact-text">
                                 <h3>Email</h3>
                                 <p>hamparankayu@gmail.com</p>
                             </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                                                         <div class="contact-text">
                                 <h3>Business Hours</h3>
                                 <p>Monday - Friday: 8:00 AM - 6:00 PM<br>Saturday: 9:00 AM - 4:00 PM<br>Sunday: By Appointment</p>
                             </div>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form">
                    <h2>Send us a Message</h2>
                    <form action="process_contact.php" method="POST">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject *</label>
                                                         <select id="subject" name="subject" required>
                                 <option value="">Select Subject</option>
                                 <option value="custom_design">Custom Design Inquiry</option>
                                 <option value="product_inquiry">Product Inquiry</option>
                                 <option value="order">Order</option>
                                 <option value="support">Support</option>
                                 <option value="partnership">Partnership</option>
                                 <option value="other">Other</option>
                             </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message *</label>
                                                         <textarea id="message" name="message" rows="5" required placeholder="Tell us about your custom design inquiry..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn-primary">Send Message</button>
                    </form>
                </div>
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

    <!-- Floating WhatsApp Button -->
    <div class="floating-whatsapp">
        <a href="https://wa.me/6282223321203?text=Halo, saya tertarik dengan produk furniture custom design Anda" target="_blank">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
