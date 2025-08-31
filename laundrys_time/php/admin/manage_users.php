<?php
session_start();
include '../core/db_connection.php';
include '../core/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../../pages/login.php');
}

$current_admin_id = $_SESSION['user_id'];

if (isset($_GET['delete'])) {
    $user_id_to_delete = intval($_GET['delete']);
    
    if ($user_id_to_delete === $current_admin_id) {
        $_SESSION['error'] = "Anda tidak dapat menghapus akun Anda sendiri.";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
        $stmt->bind_param("i", $user_id_to_delete);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $_SESSION['success'] = "Pengguna berhasil dihapus.";
        } else {
            $_SESSION['error'] = "Gagal menghapus pengguna atau pengguna tidak ditemukan.";
        }
        $stmt->close();
    }
    redirect('manage_users.php');
}

$users = $conn->query("SELECT id, full_name, email, created_at FROM users WHERE role = 'user' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="content-wrapper">
    <nav class="navbar">
        <div class="container">
            <a href="dashboard.php" class="logo">
                <img src="../../assets/img/logo.png" alt="Laundry's Time Icon">
                <span>Admin Panel</span>
            </a>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_orders.php">Kelola Pesanan</a></li>
                <li><a href="manage_services.php">Kelola Layanan</a></li>
                <li><a href="manage_users.php" class="active">Kelola Pengguna</a></li>
                <li><a href="report.php">Laporan</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <header class="dashboard-header">
            <h2>Manajemen Pengguna</h2>
            <p>Daftar semua akun pelanggan yang terdaftar di sistem.</p>
        </header>

        <div class="dashboard-content">
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
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Tanggal Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($users->num_rows > 0): ?>
                    <?php while($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <a href="manage_users.php?delete=<?php echo $user['id']; ?>" class="btn" onclick="return confirm('Yakin ingin menghapus pengguna ini? Semua pesanan milik pengguna ini juga akan terhapus.');" style="background-color:#dc3545; padding: 5px 10px; font-size: 0.8rem;">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center;">Belum ada pengguna yang terdaftar.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
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
<?php $conn->close(); ?>