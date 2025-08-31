<?php
session_start();
include '../core/db_connection.php';
include '../core/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../../pages/login.php');
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_order') {
    $order_id_post = intval($_POST['order_id']);
    $service_id = sanitize_input($_POST['service_id']);
    $weight = filter_var($_POST['weight'], FILTER_VALIDATE_FLOAT);
    $notes = sanitize_input($_POST['notes']);

    if ($order_id_post > 0 && !empty($service_id) && $weight > 0) {
        $price_stmt = $conn->prepare("SELECT price_per_kg FROM services WHERE id = ?");
        $price_stmt->bind_param("i", $service_id);
        $price_stmt->execute();
        $service = $price_stmt->get_result()->fetch_assoc();
        $price_per_kg = $service['price_per_kg'];
        $price_stmt->close();

        $total_price = $weight * $price_per_kg;

        $stmt = $conn->prepare("UPDATE orders SET service_id = ?, weight_kg = ?, total_price = ?, notes = ? WHERE id = ?");
        $stmt->bind_param("iddsi", $service_id, $weight, $total_price, $notes, $order_id_post);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Pesanan #$order_id_post berhasil diupdate.";
            redirect('manage_orders.php');
        } else {
            $_SESSION['error'] = "Gagal mengupdate pesanan.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Data tidak valid.";
        redirect('edit_order.php?id=' . $order_id);
    }
}

$order = null;
if ($order_id > 0) {
    $query = "SELECT o.id, o.user_id, o.service_id, o.weight_kg, o.notes, u.full_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $order = $result->fetch_assoc();
    }
    $stmt->close();
}

if ($order === null) {
    $_SESSION['error'] = "Pesanan tidak ditemukan.";
    redirect('manage_orders.php');
}

$services = $conn->query("SELECT id, service_name FROM services");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pesanan #<?php echo $order_id; ?> - Admin</title>
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

    <div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
        <div class="form-card" style="max-width: 600px; margin: auto;">
            <h2>Edit Pesanan #<?php echo htmlspecialchars($order['id']); ?></h2>
            <p style="text-align:center; margin-top:-1.5rem; margin-bottom:1.5rem;">Pelanggan: <strong><?php echo htmlspecialchars($order['full_name']); ?></strong></p>
            
            <form action="edit_order.php?id=<?php echo $order_id; ?>" method="POST" class="has-spinner">
                <input type="hidden" name="action" value="update_order">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                
                <div class="form-group">
                    <label for="service_id">Jenis Layanan</label>
                    <select name="service_id" id="service_id" required>
                        <?php mysqli_data_seek($services, 0); ?>
                        <?php while($service_item = $services->fetch_assoc()): ?>
                            <option value="<?php echo $service_item['id']; ?>" <?php if($service_item['id'] == $order['service_id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($service_item['service_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="weight">Berat (kg)</label>
                    <input type="number" id="weight" name="weight" step="0.1" min="0.1" value="<?php echo htmlspecialchars($order['weight_kg']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="notes">Catatan</label>
                    <textarea name="notes" id="notes" rows="3"><?php echo htmlspecialchars($order['notes']); ?></textarea>
                </div>
                
                <button type="submit" class="btn" style="width: 100%;">Update Pesanan</button>
                <a href="manage_orders.php" class="btn" style="width: 100%; text-align: center; margin-top: 10px; background-color: #6c757d;">Batal & Kembali</a>
            </form>
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