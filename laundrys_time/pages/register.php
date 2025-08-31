<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Laundry's Time</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="content-wrapper">
    <div class="form-container">
        <div class="form-card">
            <h2>Buat Akun Baru</h2>
            <?php
                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
            ?>
            <form action="../php/auth/register.php" method="POST" class="has-spinner">
                <div class="form-group">
                    <label for="full_name">Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required>
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                </div>
                <button type="submit" class="btn" style="width: 100%;">Daftar</button>
            </form>
            <p style="text-align: center; margin-top: 1rem;">
                Sudah punya akun? <a href="login.php">Login di sini</a>
            </p>
        </div>
    </div>
    </div>
    <script src="../assets/js/script.js"></script>
</body>
</html>