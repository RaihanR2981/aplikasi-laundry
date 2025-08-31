<?php
session_start();
include '../core/db_connection.php';
include '../core/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
   redirect('../../pages/login.php');
}

$total_orders = $conn->query("SELECT COUNT(id) as total FROM orders")->fetch_assoc()['total'];
$pending_orders = $conn->query("SELECT COUNT(id) as total FROM orders WHERE status = 'Proses'")->fetch_assoc()['total'];
$total_revenue_query = $conn->query("SELECT SUM(total_price) as total FROM orders WHERE status IN ('Selesai', 'Diambil')");
$total_revenue = $total_revenue_query ? $total_revenue_query->fetch_assoc()['total'] : 0;

$latest_orders_query = "SELECT o.id, u.full_name, s.service_name, o.weight_kg, o.total_price, o.status
                        FROM orders o
                        JOIN users u ON o.user_id = u.id
                        JOIN services s ON o.service_id = s.id
                        ORDER BY o.created_at DESC LIMIT 5";
$latest_orders = $conn->query($latest_orders_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Laundry's Time</title>
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
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="manage_orders.php">Kelola Pesanan</a></li>
                <li><a href="manage_services.php">Kelola Layanan</a></li>
                <li><a href="manage_users.php">Kelola Pengguna</a></li>
                <li><a href="report.php">Laporan</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <header class="dashboard-header">
            <h2>Selamat Datang, Admin <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
        </header>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
            <div class="dashboard-content" style="margin-top: 0;">
                <h3 style="color: var(--subtle-text-color); font-size: 1rem; margin-bottom: 0.5rem;">Total Pesanan</h3>
                <p style="font-size: 2rem; font-weight: 700; color: var(--primary-color);"><?php echo $total_orders; ?></p>
            </div>
             <div class="dashboard-content" style="margin-top: 0;">
                <h3 style="color: var(--subtle-text-color); font-size: 1rem; margin-bottom: 0.5rem;">Pesanan Proses</h3>
                <p style="font-size: 2rem; font-weight: 700; color: var(--primary-color);"><?php echo $pending_orders; ?></p>
            </div>
             <div class="dashboard-content" style="margin-top: 0;">
                <h3 style="color: var(--subtle-text-color); font-size: 1rem; margin-bottom: 0.5rem;">Total Pendapatan</h3>
                <p style="font-size: 2rem; font-weight: 700; color: var(--primary-color);">Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?></p>
            </div>
        </div>

        <div class="dashboard-content">
            <h3>5 Pesanan Terbaru</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Total Harga</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = $latest_orders->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['service_name']); ?></td>
                        <td>Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></td>
                        <td>
                            <?php
                                $status_class = 'status-' . strtolower($order['status']);
                                echo '<span class="status ' . $status_class . '">' . htmlspecialchars($order['status']) . '</span>';
                            ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
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