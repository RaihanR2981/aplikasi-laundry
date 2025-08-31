<?php
session_start();
include '../core/db_connection.php';
include '../core/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    redirect('../../pages/login.php');
}

$services = $conn->query("SELECT id, service_name, price_per_kg FROM services");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $service_id = sanitize_input($_POST['service_id']);
    $weight = filter_var($_POST['weight'], FILTER_VALIDATE_FLOAT);
    $order_date = sanitize_input($_POST['order_date']);
    $notes = sanitize_input($_POST['notes']);

    if (empty($service_id) || !$weight || $weight <= 0 || empty($order_date)) {
        $_SESSION['error'] = "Harap isi semua kolom yang wajib diisi dengan benar.";
    } else {
        $price_stmt = $conn->prepare("SELECT price_per_kg FROM services WHERE id = ?");
        $price_stmt->bind_param("i", $service_id);
        $price_stmt->execute();
        $result = $price_stmt->get_result();
        $service = $result->fetch_assoc();
        $price_per_kg = $service['price_per_kg'];
        $price_stmt->close();

        $total_price = $weight * $price_per_kg;

        $stmt = $conn->prepare("INSERT INTO orders (user_id, service_id, weight_kg, total_price, order_date, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiddss", $user_id, $service_id, $weight, $total_price, $order_date, $notes);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Pesanan berhasil dibuat!";
            redirect('dashboard.php');
        } else {
            $_SESSION['error'] = "Gagal membuat pesanan. Silakan coba lagi.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Pesanan - Laundry's Time</title>
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
                <li><a href="dashboard.php">Status Laundry</a></li>
                <li><a href="order.php" class="active">Pesan Baru</a></li>
                <li><a href="profile.php">Profil Saya</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
        <div class="form-card" style="max-width: 600px; margin: auto;">
             <h2>Formulir Pemesanan Laundry</h2>
            <?php
                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);
                }
            ?>
            <form action="order.php" method="POST" class="has-spinner">
                <div class="form-group">
                    <label for="service_id">Jenis Layanan</label>
                    <select name="service_id" id="service_id" required>
                        <option value="">-- Pilih Layanan --</option>
                        <?php mysqli_data_seek($services, 0); ?>
                        <?php while($service = $services->fetch_assoc()): ?>
                            <option value="<?php echo $service['id']; ?>">
                                <?php echo htmlspecialchars($service['service_name']) . " (Rp " . number_format($service['price_per_kg'], 0, ',', '.') . "/kg)"; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="weight">Berat (kg)</label>
                    <input type="number" id="weight" name="weight" step="0.1" min="0.1" required>
                </div>
                <div class="form-group">
                    <label for="order_date">Tanggal Masuk</label>
                    <input type="date" id="order_date" name="order_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label for="notes">Catatan (Opsional)</label>
                    <textarea name="notes" id="notes" rows="3" placeholder="Contoh: Pisahkan baju putih"></textarea>
                </div>
                <button type="submit" class="btn" style="width: 100%;">Buat Pesanan</button>
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