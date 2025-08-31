<?php
session_start();
include '../php/core/functions.php';?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat & Ketentuan - Laundry's Time</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="content-wrapper">
    <nav class="navbar">
        <div class="container">
            <a href="../index.php" class="logo">
                <img src="../assets/img/logo.png" alt="Laundry's Time Icon">
                <span>Laundry's Time</span>
            </a>
            <ul>
                <?php if (check_login_status()) : ?>
                    <li><a href="<?php echo $_SESSION['role'] === 'admin' ? 'php/admin/dashboard.php' : 'php/user/dashboard.php'; ?>">Dashboard</a></li>
                    <li><a href="php/auth/logout.php">Logout</a></li>
                <?php else : ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="btn">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
        <div class="dashboard-content">
            <a href="../index.php" class="btn" style="margin-bottom: 2rem; background-color: #6c757d;">&laquo; Kembali ke Beranda</a>
            <header class="dashboard-header">
                <h2>Syarat & Ketentuan</h2>
            </header>
            <div style="line-height: 1.8;">
                <p>Dengan menggunakan layanan dari Laundry's Time, Anda setuju untuk mematuhi syarat dan ketentuan di bawah ini:</p>
                <br>
                <h4>1. Penerimaan Pakaian</h4>
                <p>Kami berhak menolak untuk mencuci pakaian tertentu yang berisiko luntur, rusak, atau mengandung bahan berbahaya tanpa pemberitahuan sebelumnya.</p>
                <br>
                <h4>2. Penghitungan Berat</h4>
                <p>Penghitungan berat yang sah adalah yang dilakukan saat penimbangan di workshop kami. Minimum berat per pesanan adalah 1 kg.</p>
                <br>
                <h4>3. Barang Tertinggal</h4>
                <p>Kami tidak bertanggung jawab atas barang-barang pribadi (uang, perhiasan, dll.) yang tertinggal di dalam saku pakaian. Pelanggan dihimbau untuk memeriksa kembali pakaiannya sebelum diserahkan kepada kami.</p>
                <br>
                <h4>4. Klaim & Kompensasi</h4>
                <p>Klaim atas kerusakan atau kehilangan pakaian harus diajukan maksimal 1x24 jam setelah pakaian diterima. Kompensasi yang diberikan adalah maksimal 10 kali dari biaya cuci pakaian yang bersangkutan, sesuai dengan nota.</p>
                <br>
                <h4>5. Pengambilan Pakaian</h4>
                <p>Pakaian yang tidak diambil dalam waktu 30 hari sejak tanggal selesai bukan lagi menjadi tanggung jawab kami.</p>
            </div>
        </div>
    </div>
                    
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Laundry's Time. All Rights Reserved.</p>
        </div>
    </footer>
</div>
</body>
</html>