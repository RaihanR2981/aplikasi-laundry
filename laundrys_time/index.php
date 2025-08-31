<?php
session_start();
include 'php/core/db_connection.php';
include 'php/core/functions.php';

$services = $conn->query("SELECT service_name, price_per_kg FROM services ORDER BY id ASC LIMIT 3");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry's Time - Cepat, Bersih, Terpercaya</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-pattern">
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">
                <img src="assets/img/logo.png" alt="Laundry's Time Icon">
                <span>Laundry's Time</span>
            </a>
            <ul>
                <?php if (check_login_status()) : ?>
                    <?php if ($_SESSION['role'] === 'admin') : ?>
                        <li><a href="php/admin/dashboard.php">Dashboard Admin</a></li>
                    <?php else : ?>
                        <li><a href="php/user/dashboard.php">Dashboard User</a></li>
                    <?php endif; ?>
                    <li><a href="php/auth/logout.php">Logout</a></li>
                <?php else : ?>
                    <li><a href="pages/login.php">Login</a></li>
                    <li><a href="pages/register.php" class="btn">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-content">
            <h1>Solusi Laundry Terbaik untuk Anda</h1>
            <p>Hemat waktu Anda, biarkan kami yang mengurus cucian kotor Anda.</p>
            <a href="<?php echo check_login_status() ? 'php/user/order.php' : 'pages/register.php'; ?>" class="btn">Pesan Sekarang</a>
        </div>
    </header>

    <main>
        <div class="content-wrapper">
        <section id="tentang-kami" class="about-section">
            <div class="container about-grid">
                <div class="about-content">
                    <h2>Tentang Laundry's Time</h2>
                    <p><strong>Laundry's Time</strong> didirikan dengan satu tujuan: memberikan solusi laundry yang cepat, bersih, dan terpercaya. Kami mengerti bahwa waktu Anda sangat berharga, oleh karena itu kami hadir untuk mengambil alih tugas mencuci sehingga Anda bisa fokus pada hal yang lebih penting.</p>
                    <p>Kami berkomitmen untuk menggunakan teknologi modern dan deterjen ramah lingkungan untuk memastikan setiap helai pakaian Anda dirawat dengan baik demi hasil yang tidak hanya bersih, tetapi juga rapi dan wangi.</p>
                </div>
                <div class="about-image">
                    <img src="assets/img/laundry.jpeg" alt="Interior laundry yang bersih">
                </div>
            </div>
        </section>
        <section id="layanan" class="services-section">
            <div class="container">
                <h2 class="section-title">Layanan Populer Kami</h2>
                <div class="services-grid">
                    <?php if ($services && $services->num_rows > 0): ?>
                        <?php while($service = $services->fetch_assoc()): ?>
                            <div class="service-card">
                                <h3><?php echo htmlspecialchars($service['service_name']); ?></h3>
                                <p class="price">Rp <?php echo number_format($service['price_per_kg'], 0, ',', '.'); ?><span>/kg</span></p>
                                <p>Proses cepat dan hasil bersih maksimal untuk pakaian Anda.</p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>Layanan tidak tersedia saat ini.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        
        <section class="promo-section">
            <div class="container promo-grid">
                <div class="promo-image">
                    <img src="assets/img/mesin-cuci.png" alt="Mesin cuci modern">
                </div>
                <div class="promo-content">
                    <h2>Teknologi Modern untuk Hasil Sempurna</h2>
                    <p>Kami menggunakan mesin cuci dan pengering canggih yang hemat energi serta deterjen ramah lingkungan. Kombinasi ini tidak hanya membersihkan pakaian Anda secara maksimal, tapi juga menjaga serat kain agar tetap awet dan warnanya tidak pudar.</p>
                    <a href="<?php echo check_login_status() ? 'php/user/order.php' : 'pages/register.php'; ?>"Coba Layanan Kami</a>
                </div>
            </div>
        </section>

        <section class="how-it-works-section">
            <div class="container">
                <h2 class="section-title">Hanya 3 Langkah Mudah</h2>
                <div class="grid-3">
                    <div class="step-card"><div class="icon"><i class="fas fa-file-alt"></i></div><h3>1. Pesan Online</h3><p>Buat pesanan dengan mudah melalui form pemesanan kami.</p></div>
                    <div class="step-card"><div class="icon"><i class="fas fa-truck"></i></div><h3>2. Penjemputan</h3><p>Tim kami akan menjemput cucian Anda sesuai jadwal.</p></div>
                    <div class="step-card"><div class="icon"><i class="fas fa-tshirt"></i></div><h3>3. Terima Bersih</h3><p>Pakaian Anda kami antar kembali dalam keadaan bersih dan wangi.</p></div>
                </div>
            </div>
        </section>
        

        <section class="testimonials-section">
            <div class="container">
                <h2 class="section-title">Apa Kata Pelanggan Kami?</h2>
                <div class="grid-3">
                    <div class="testimonial-card">
                        <p>"Pelayanannya cepat dan hasilnya sangat memuaskan. Pakaian saya jadi wangi dan rapi. Pasti langganan di sini!"</p>
                        <div class="testimonial-author">
                            <img src="assets/img/pelanggan_1.jpeg" alt="Pelanggan 1">
                            <div><h4>Budi Santoso</h4></div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <p>"Sangat membantu untuk saya yang sibuk. Fitur pesan online-nya praktis, tidak perlu repot datang langsung. Recommended!"</p>
                        <div class="testimonial-author">
                            <img src="assets/img/pelanggan_2.jpeg" alt="Pelanggan 2">
                            <div><h4>Rina Amelia</h4></div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <p>"Harga terjangkau dengan kualitas yang tidak murahan. Stafnya juga ramah-ramah. Terima kasih Laundry's Time!"</p>
                        <div class="testimonial-author">
                            <img src="assets/img/pelanggan_3.jpeg" alt="Pelanggan 3">
                            <div><h4>Joko Prasetyo</h4></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <div class="container">
                <h2>Siap Membuat Hidup Lebih Mudah?</h2>
                <p>Daftar sekarang dan nikmati kemudahan mengurus cucian Anda dengan layanan profesional kami.</p>
                <a href="pages/register.php" class="btn" style="background-color: #fff; color: var(--primary-color);">Daftar Gratis Sekarang</a>
            </div>
        </section>
        </div>
    </main>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <a href="index.php" class="logo">
                        <img src="assets/img/logo.png" alt="Laundry's Time Icon">
                    </a>
                    <p>Layanan laundry profesional yang mengutamakan kualitas, kebersihan, dan ketepatan waktu untuk kepuasan Anda.</p>
                    <div class="social-icons">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Menu</h4>
                    <ul>
                        <li><a href="#">Beranda</a></li>
                        <li><a href="#layanan">Layanan</a></li>
                        <li><a href="#tentang-kami">Tentang Kami</a></li>
                        <li><a href="pages/syarat_ketentuan.php">Syarat Ketentuan</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Info</h4>
                    <ul>
                        <li><i class="fas fa-phone"></i> <span>0812-3456-7890</span></li>
                        <li><i class="fas fa-envelope"></i> <span>kontak@laundrytime.com</span></li>
                        <li><i class="fas fa-map-marker-alt"></i> <span>Jl. Pembangunan No. 123, Medan</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
<div class="footer-bottom"><p>&copy; <?php echo date('Y'); ?> Laundry's Time. All Rights Reserved.</p>

    <script src="assets/js/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>