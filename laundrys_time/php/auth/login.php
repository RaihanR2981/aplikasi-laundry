<?php
session_start();
include '../core/db_connection.php';
include '../core/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = filter_var(sanitize_input($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = sanitize_input($_POST['password']);

    if (!$email || empty($password)) {
        $_SESSION['error'] = "Format email tidak valid atau password kosong.";
        redirect('../../pages/login.php');
    }

    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ?");
    if ($stmt === false) {
        die('Prepare() failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                redirect('../admin/dashboard.php');
            } else {
                redirect('../user/dashboard.php');
            }
        } else {
            $_SESSION['error'] = "Password salah.";
            redirect('../../pages/login.php');
        }
    } else {
        $_SESSION['error'] = "Email tidak ditemukan.";
        redirect('../../pages/login.php');
    }

    $stmt->close();
    $conn->close();

} else {
    redirect('../../pages/login.php');
}
?>