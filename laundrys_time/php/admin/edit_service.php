<?php
session_start();
include '../core/db_connection.php';
include '../core/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    redirect('../../pages/login.php');
}

$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_service') {
    $service_id_post = intval($_POST['service_id']);
    $service_name = sanitize_input($_POST['service_name']);
    $price = filter_var($_POST['price_per_kg'], FILTER_VALIDATE_FLOAT);

    if ($service_id_post > 0 && !empty($service_name) && $price >= 0) {
        $stmt = $conn->prepare("UPDATE services SET service_name = ?, price_per_kg = ? WHERE id = ?");
        $stmt->bind_param("sdi", $service_name, $price, $service_id_post);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Layanan #$service_id_post berhasil diupdate.";
            redirect('manage_services.php');
        } else {
            $_SESSION['error'] = "Gagal mengupdate layanan.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Data layanan tidak valid.";
        redirect('edit_service.php?id=' . $service_id);
    }
}

$service = null; 

if ($service_id > 0) {
    $stmt = $conn->prepare("SELECT id, service_name, price_per_kg FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $service = $result->fetch_assoc();
    }
    $stmt->close();
}

if ($service === null) {
    $_SESSION['error'] = "Layanan tidak ditemukan.";
    redirect('manage_services.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Layanan #<?php echo $service_id; ?> - Admin</title>
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

    <div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
        <div class="form-card" style="max-width: 600px; margin: auto;">
            <h2>Edit Layanan #<?php echo htmlspecialchars($service['id']); ?></h2>

            <form action="edit_service.php?id=<?php echo $service_id; ?>" method="POST" class="has-spinner">
                <input type="hidden" name="action" value="update_service">
                <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                
                <div class="form-group">
                    <label for="service_name">Nama Layanan</label>
                    <input type="text" name="service_name" value="<?php echo htmlspecialchars($service['service_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="price_per_kg">Harga per Kg</label>
                    <input type="number" name="price_per_kg" step="500" min="0" value="<?php echo htmlspecialchars($service['price_per_kg']); ?>" required>
                </div>
                
                <button type="submit" class="btn" style="width: 100%;">Update Layanan</button>
                <a href="manage_services.php" class="btn" style="width: 100%; text-align: center; margin-top: 10px; background-color: #6c757d;">Batal</a>
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