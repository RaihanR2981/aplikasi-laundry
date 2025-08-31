<?php
session_start();
include '../core/db_connection.php';
include '../core/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../../pages/login.php');
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$base_query = "SELECT o.id, u.full_name, s.service_name, o.weight_kg, o.total_price, o.order_date, o.status
               FROM orders o
               JOIN users u ON o.user_id = u.id
               JOIN services s ON o.service_id = s.id";
$params = [];
$types = '';

if (!empty($start_date) && !empty($end_date)) {
    $base_query .= " WHERE o.order_date BETWEEN ? AND ?";
    $params[] = &$start_date;
    $params[] = &$end_date;
    $types = "ss";
}

$base_query .= " ORDER BY o.order_date DESC";
$stmt = $conn->prepare($base_query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$transactions = $stmt->get_result();

$total_orders_period = 0;
$total_revenue_period = 0;
$transactions_data = [];
if ($transactions->num_rows > 0) {
    $transactions_data = $transactions->fetch_all(MYSQLI_ASSOC);
    $total_orders_period = count($transactions_data);
    foreach ($transactions_data as $transaction) {
        $total_revenue_period += $transaction['total_price'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - Admin</title>
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
                <li><a href="manage_users.php">Kelola Pengguna</a></li>
                <li><a href="report.php" class="active">Laporan</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <header class="dashboard-header">
            <h2>Laporan Rekap Transaksi</h2>
            <p>Filter transaksi berdasarkan rentang tanggal yang dipilih.</p>
        </header>

        <div class="dashboard-content" style="margin-bottom: 2rem;">
            <form action="report.php" method="GET" style="display: flex; align-items: center; gap: 15px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="start_date">Dari Tanggal</label>
                    <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="end_date">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" required>
                </div>
                <button type="submit" class="btn" style="align-self: flex-end;">Filter</button>
            </form>
        </div>
        
        <?php if (!empty($start_date)): ?>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div class="dashboard-content" style="margin-top: 0;">
                <h3 style="color: var(--subtle-text-color); font-size: 1rem; margin-bottom: 0.5rem;">Total Pesanan (Periode Ini)</h3>
                <p style="font-size: 2rem; font-weight: 700; color: var(--primary-color);"><?php echo $total_orders_period; ?></p>
            </div>
            <div class="dashboard-content" style="margin-top: 0;">
                <h3 style="color: var(--subtle-text-color); font-size: 1rem; margin-bottom: 0.5rem;">Total Pendapatan (Periode Ini)</h3>
                <p style="font-size: 2rem; font-weight: 700; color: var(--primary-color);">Rp <?php echo number_format($total_revenue_period, 0, ',', '.'); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <div class="dashboard-content">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Layanan</th>
                        <th>Total Harga</th>
                        <th>Tanggal Pesan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($transactions_data)): ?>
                    <?php foreach ($transactions_data as $row): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                        <td>Rp <?php echo number_format($row['total_price'], 0, ',', '.'); ?></td>
                        <td><?php echo date('d M Y', strtotime($row['order_date'])); ?></td>
                        <td>
                            <?php
                                $status_class = 'status-' . strtolower($row['status']);
                                echo '<span class="status ' . $status_class . '">' . htmlspecialchars($row['status']) . '</span>';
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center;">Silakan pilih rentang tanggal untuk melihat laporan, atau tidak ada data pada periode yang dipilih.</td></tr>
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