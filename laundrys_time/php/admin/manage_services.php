<?php
session_start();
include '../core/db_connection.php';
include '../core/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../../pages/login.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_service'])) {
    $service_name = sanitize_input($_POST['service_name']);
    $price = filter_var($_POST['price_per_kg'], FILTER_VALIDATE_FLOAT);

    if (!empty($service_name) && $price >= 0) {
        $stmt = $conn->prepare("INSERT INTO services (service_name, price_per_kg) VALUES (?, ?)");
        $stmt->bind_param("sd", $service_name, $price);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Layanan baru berhasil ditambahkan.";
        } else {
            $_SESSION['error'] = "Gagal menambahkan layanan.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Nama layanan dan harga tidak valid.";
    }
    redirect('manage_services.php');
}

if (isset($_GET['delete'])) {
    $service_id = intval($_GET['delete']);
    $check_stmt = $conn->prepare("SELECT COUNT(id) as total FROM orders WHERE service_id = ?");
    $check_stmt->bind_param("i", $service_id);
    $check_stmt->execute();
    $is_used = $check_stmt->get_result()->fetch_assoc()['total'] > 0;
    $check_stmt->close();

    if ($is_used) {
        $_SESSION['error'] = "Layanan tidak dapat dihapus karena sudah digunakan dalam transaksi.";
    } else {
        $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
        $stmt->bind_param("i", $service_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Layanan berhasil dihapus.";
        } else {
            $_SESSION['error'] = "Gagal menghapus layanan.";
        }
        $stmt->close();
    }
    redirect('manage_services.php');
}

$services = $conn->query("SELECT * FROM services ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Layanan - Admin</title>
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
                <li><a href="manage_services.php" class="active">Kelola Layanan</a></li>
                <li><a href="manage_users.php">Kelola Pengguna</a></li>
                <li><a href="report.php">Laporan</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <header class="dashboard-header">
            <h2>Manajemen Layanan Laundry</h2>
        </header>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <div class="dashboard-content">
                <h3>Daftar Layanan</h3>
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
                            <th>Nama Layanan</th>
                            <th>Harga/kg</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($services->num_rows > 0): ?>
                        <?php while($service = $services->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $service['id']; ?></td>
                            <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                            <td>Rp <?php echo number_format($service['price_per_kg'], 0, ',', '.'); ?></td>
                            <td>
                                <a href="edit_service.php?id=<?php echo $service['id']; ?>" class="btn" style="background-color:#ffc107; padding: 5px 10px; font-size: 0.8rem;">Edit</a>
                                <a href="manage_services.php?delete=<?php echo $service['id']; ?>" class="btn" onclick="return confirm('Yakin ingin menghapus layanan ini?');" style="background-color:#dc3545; padding: 5px 10px; font-size: 0.8rem;">Hapus</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align: center;">Belum ada data layanan.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="dashboard-content">
                <h3>Tambah Layanan Baru</h3>
                <form action="manage_services.php" method="POST">
                    <div class="form-group">
                        <label for="service_name">Nama Layanan</label>
                        <input type="text" name="service_name" required>
                    </div>
                    <div class="form-group">
                        <label for="price_per_kg">Harga per Kg</label>
                        <input type="number" name="price_per_kg" step="500" min="0" required>
                    </div>
                    <button type="submit" name="add_service" class="btn" style="width: 100%;">Tambah</button>
                </form>
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