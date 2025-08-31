<?php
session_start();
include '../core/db_connection.php';
include '../core/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    redirect('../../pages/login.php');
}

$user_id = $_SESSION['user_id'];

$query = "SELECT o.id, s.service_name, o.weight_kg, o.total_price, o.order_date, o.status
          FROM orders o
          JOIN services s ON o.service_id = s.id
          WHERE o.user_id = ?
          ORDER BY o.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Laundry's Time</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
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
            <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
            <p>Di sini Anda dapat melihat riwayat dan status pesanan laundry Anda.</p>
        </header>

        <div class="dashboard-content">
            <h3>Riwayat Pesanan Anda</h3>
            <?php
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
            ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Layanan</th>
                        <th>Total Harga</th>
                        <th>Tgl Pesan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                                <td>Rp <?php echo number_format($row['total_price'], 0, ',', '.'); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['order_date'])); ?></td>
                                <td>
                                    <?php
                                        $status_class = 'status-' . strtolower($row['status']);
                                        echo '<span class="status ' . $status_class . '">' . htmlspecialchars($row['status']) . '</span>';
                                    ?>
                                </td>
                                <td>
                                    <a href="order_detail.php?id=<?php echo $row['id']; ?>" class="btn" style="padding: 5px 10px; font-size: 0.8rem;">Detail</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center;">Anda belum memiliki pesanan.</td>
                        </tr>
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
<?php
$stmt->close();
$conn->close();
?>