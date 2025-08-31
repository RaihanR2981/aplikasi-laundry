<?php
session_start();
include '../core/db_connection.php';
include '../core/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    redirect('../../pages/login.php');
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

if ($order_id <= 0) {
    redirect('dashboard.php');
}

$query = "SELECT 
            o.id, o.order_date, o.weight_kg, o.total_price, o.status, o.notes, o.created_at,
            s.service_name, s.price_per_kg
          FROM orders o
          JOIN services s ON o.service_id = s.id
          WHERE o.id = ? AND o.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    redirect('dashboard.php');
}

$logs_query = "SELECT status, changed_at FROM status_logs WHERE order_id = ? ORDER BY changed_at ASC";
$log_stmt = $conn->prepare($logs_query);
$log_stmt->bind_param("i", $order_id);
$log_stmt->execute();
$logs = $log_stmt->get_result();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?php echo $order['id']; ?> - Laundry's Time</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .timeline { list-style: none; padding: 0; }
        .timeline li { margin-bottom: 10px; padding-left: 20px; border-left: 2px solid var(--border-color); position: relative; }
        .timeline li::before {
            content: ''; width: 12px; height: 12px; background: var(--border-color);
            border-radius: 50%; position: absolute; left: -7px; top: 5px;
        }
        .timeline li.active { border-left-color: var(--primary-color); }
        .timeline li.active::before { background: var(--primary-color); }
    </style>
</head>
<body>
    <div class="content-wrapper">    
    <nav class="navbar">
        <div class="container">
            <a href="../../index.php" class="logo">
                <img src="../../assets/img/logo.png" alt="Laundry's Time Icon">
                <span>Laundry's Time</span>
            </a>
             <ul>
                <li><a href="dashboard.php" class="active">Status Laundry</a></li>
                <li><a href="order.php">Pesan Baru</a></li>
                <li><a href="profile.php">Profil Saya</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <header class="dashboard-header">
            <h2>Detail Pesanan #<?php echo $order['id']; ?></h2>
            <p>Dipesan pada tanggal: <?php echo date('d F Y', strtotime($order['order_date'])); ?></p>
        </header>

        <div class="dashboard-content" style="display: flex; flex-wrap: wrap; gap: 2rem;">
            <div style="flex: 2; min-width: 300px;">
                <h3>Informasi Pesanan</h3>
                <dl class="detail-grid">
                    <dt>Status Saat Ini</dt>
                    <dd>
                        <?php
                            $status_class = 'status-' . strtolower($order['status']);
                            echo '<span class="status ' . $status_class . '">' . htmlspecialchars($order['status']) . '</span>';
                        ?>
                    </dd>
                    <dt>Layanan</dt>
                    <dd><?php echo htmlspecialchars($order['service_name']); ?></dd>
                    <dt>Berat</dt>
                    <dd><?php echo htmlspecialchars($order['weight_kg']); ?> kg</dd>
                    <dt>Harga per kg</dt>
                    <dd>Rp <?php echo number_format($order['price_per_kg'], 0, ',', '.'); ?></dd>
                    <dt>Total Harga</dt>
                    <dd><strong>Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></strong></dd>
                    <dt>Catatan</dt>
                    <dd><?php echo !empty($order['notes']) ? htmlspecialchars($order['notes']) : '-'; ?></dd>
                </dl>
            </div>
            <div style="flex: 1; min-width: 250px;">
                <h3>Riwayat Status</h3>
                <ul class="timeline">
                    <?php if ($logs->num_rows > 0): ?>
                        <?php while($log = $logs->fetch_assoc()): ?>
                            <li class="active">
                                <strong><?php echo $log['status']; ?></strong><br>
                                <small><?php echo date('d M Y, H:i', strtotime($log['changed_at'])); ?></small>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="active">
                            <strong>Pesanan Dibuat</strong><br>
                            <small><?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></small>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <div style="margin-top: 2rem;">
            <a href="dashboard.php" class="btn" style="background-color: #6c757d;">&laquo; Kembali ke Riwayat</a>
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
<?php
$stmt->close();
$log_stmt->close();
$conn->close();
?>