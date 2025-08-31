<?php
session_start();
include '../core/db_connection.php';
include '../core/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
   redirect('../../pages/login.php');
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'change_password') {
    $old_password = sanitize_input($_POST['old_password']);
    $new_password = sanitize_input($_POST['new_password']);
    $confirm_password = sanitize_input($_POST['confirm_password']);

    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error'] = "Semua kolom password wajib diisi.";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Password baru dan konfirmasi tidak cocok.";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (password_verify($old_password, $user['password'])) {
            $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $new_hashed_password, $user_id);
            if ($update_stmt->execute()) {
                $_SESSION['success'] = "Password berhasil diubah.";
            } else {
                $_SESSION['error'] = "Gagal mengubah password.";
            }
            $update_stmt->close();
        } else {
            $_SESSION['error'] = "Password lama salah.";
        }
    }
    redirect('profile.php');
}

$stmt = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Laundry's Time</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-pattern">
    <nav class="navbar">
        <div class="container">
            <a href="../../index.php" class="logo">
                <img src="../../assets/img/logo.png" alt="Laundry's Time Icon">
                <span>Laundry's Time</span>
            </a>
            <ul>
                <li><a href="dashboard.php">Status Laundry</a></li>
                <li><a href="order.php">Pesan Baru</a></li>
                <li><a href="profile.php" class="active">Profil Saya</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="content-wrapper">
        <div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
            <header class="dashboard-header">
                <h2>Profil Saya</h2>
            </header>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div class="dashboard-content">
                    <h3>Informasi Akun</h3>
                    <dl style="display: grid; grid-template-columns: auto 1fr; gap: 10px 20px; align-items: center;">
                        <dt style="font-weight: 600; color: var(--subtle-text-color);">Nama Lengkap</dt>
                        <dd><?php echo htmlspecialchars($user_data['full_name']); ?></dd>
                        
                        <dt style="font-weight: 600; color: var(--subtle-text-color);">Email</dt>
                        <dd><?php echo htmlspecialchars($user_data['email']); ?></dd>
                    </dl>
                </div>

                <div class="dashboard-content">
                    <h3>Ubah Password</h3>
                    <?php
                    if (isset($_SESSION['success'])) {
                        echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                        unset($_SESSION['success']);
                    }
                    if (isset($_SESSION['error'])) {
                        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                        unset($_SESSION['error']);
                    }
                    ?>
                    <form action="profile.php" method="POST" class="has-spinner">
                        <input type="hidden" name="action" value="change_password">
                        <div class="form-group">
                            <label for="old_password">Password Lama</label>
                            <div class="password-wrapper">
                                <input type="password" name="old_password" required>
                                <i class="fas fa-eye toggle-password"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="new_password">Password Baru</label>
                            <div class="password-wrapper">
                                <input type="password" name="new_password" required>
                                <i class="fas fa-eye toggle-password"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi Password Baru</label>
                            <div class="password-wrapper">
                                <input type="password" name="confirm_password" required>
                                <i class="fas fa-eye toggle-password"></i>
                            </div>
                        </div>
                        <button type="submit" class="btn" style="width: 100%;">Ubah Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Laundry's Time. All Rights Reserved.</p>
        </div>
    </footer>
    </div>
    <script src="../../assets/js/script.js"></script>
</body>
</html>
<?php $conn->close(); ?>