<?php
session_start();
include '../core/db_connection.php';
include '../core/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../../pages/login.php');
}

$search_query_string = isset($_GET['search']) && !empty($_GET['search']) ? '?search=' . urlencode($_GET['search']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = sanitize_input($_POST['new_status']);
    
    $update_stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_status, $order_id);
    if($update_stmt->execute()){
        $log_stmt = $conn->prepare("INSERT INTO status_logs (order_id, status) VALUES (?, ?)");
        $log_stmt->bind_param("is", $order_id, $new_status);
        $log_stmt->execute();
        $log_stmt->close();
        $_SESSION['success'] = "Status pesanan #$order_id berhasil diubah.";
    } else {
        $_SESSION['error'] = "Gagal mengubah status.";
    }
    $update_stmt->close();
    redirect('manage_orders.php' . $search_query_string);
}

if (isset($_GET['delete'])) {
    $order_id = intval($_GET['delete']);
    $delete_stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $delete_stmt->bind_param("i", $order_id);
    if($delete_stmt->execute()){
         $_SESSION['success'] = "Pesanan #$order_id berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Gagal menghapus pesanan.";
    }
    $delete_stmt->close();
    redirect('manage_orders.php' . $search_query_string);
}

$search_term = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$base_query = "SELECT o.id, u.full_name, s.service_name, o.weight_kg, o.total_price, o.order_date, o.status
               FROM orders o
               JOIN users u ON o.user_id = u.id
               JOIN services s ON o.service_id = s.id";
$params = [];
$types = '';

if (!empty($search_term)) {
    $base_query .= " WHERE u.full_name LIKE ? OR o.id = ?";
    $like_term = "%" . $search_term . "%";
    $params[] = $like_term;
    $params[] = $search_term;
    $types = "ss";
}

$base_query .= " ORDER BY o.created_at DESC";
$stmt = $conn->prepare($base_query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$orders = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin</title>
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
                <li><a href="manage_orders.php" class="active">Kelola Pesanan</a></li>
                <li><a href="manage_services.php">Kelola Layanan</a></li>
                <li><a href="manage_users.php">Kelola Pengguna</a></li>
                <li><a href="report.php">Laporan</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <header class="dashboard-header">
            <h2>Manajemen Data Pesanan</h2>
        </header>

        <div class="dashboard-content">
            <form action="manage_orders.php" method="GET" style="margin-bottom: 1.5rem; display: flex; gap: 10px;">
                <input type="text" name="search" placeholder="Cari nama pelanggan atau ID pesanan..." value="<?php echo htmlspecialchars($search_term); ?>" style="flex-grow: 1; padding: 10px; border-radius: var(--border-radius); border: 1px solid var(--border-color);">
                <button type="submit" class="btn">Cari</button>
                <?php if (!empty($search_term)): ?>
                    <a href="manage_orders.php" class="btn" style="background-color: #6c757d;">Reset</a>
                <?php endif; ?>
            </form>

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
            <div class="data-table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Layanan</th>
                            <th>Harga</th>
                            <th>Tgl Pesan</th>
                            <th>Status</th>
                            <th style="min-width: 320px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($orders->num_rows > 0): ?>
                        <?php while ($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['service_name']); ?></td>
                            <td>Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></td>
                            <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                            <td>
                                 <?php
                                    $status_class = 'status-' . strtolower($order['status']);
                                    echo '<span class="status ' . $status_class . '">' . htmlspecialchars($order['status']) . '</span>';
                                ?>
                            </td>
                            <td>
                                <form action="manage_orders.php<?php echo $search_query_string; ?>" method="POST" style="display:inline-flex; align-items:center; gap: 5px;">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="new_status" style="padding: 5px; border-radius: var(--border-radius); border: 1px solid var(--border-color);">
                                        <option value="Proses" <?php if($order['status'] == 'Proses') echo 'selected'; ?>>Proses</option>
                                        <option value="Selesai" <?php if($order['status'] == 'Selesai') echo 'selected'; ?>>Selesai</option>
                                        <option value="Diambil" <?php if($order['status'] == 'Diambil') echo 'selected'; ?>>Diambil</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn" style="padding: 5px 10px; font-size: 0.8rem;">Update</button>
                                    <a href="edit_order.php?id=<?php echo $order['id']; ?>" class="btn" style="background-color:#ffc107; padding: 5px 10px; font-size: 0.8rem;">Edit</a>
                                    <a href="manage_orders.php?delete=<?php echo $order['id']; ?><?php echo !empty($search_term) ? '&search='.urlencode($search_term) : ''; ?>" class="btn" onclick="return confirm('Yakin ingin menghapus pesanan ini?');" style="background-color:#dc3545; padding: 5px 10px; font-size: 0.8rem;">Hapus</a>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">
                                <?php if (!empty($search_term)): ?>
                                    Tidak ada pesanan yang cocok dengan kata kunci "<?php echo htmlspecialchars($search_term); ?>".
                                <?php else: ?>
                                    Belum ada data pesanan.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
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
<?php
$stmt->close();
$conn->close();
?>