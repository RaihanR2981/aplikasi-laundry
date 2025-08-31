<?php
session_start();
include '../core/db_connection.php';
include '../core/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = sanitize_input($_POST['full_name']);
    $email = filter_var(sanitize_input($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = sanitize_input($_POST['password']);

    if (empty($full_name) || empty($email) || empty($password)) {
        $_SESSION['error'] = "Semua kolom wajib diisi.";
        redirect('../../pages/register.php');
    }
    if (!$email) {
        $_SESSION['error'] = "Format email tidak valid.";
        redirect('../../pages/register.php');
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email sudah terdaftar.";
        redirect('../../pages/register.php');
    }
    $stmt->close();

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->bind_param("sss", $full_name, $email, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
        redirect('../../pages/login.php');
    } else {
        $_SESSION['error'] = "Terjadi kesalahan. Coba lagi.";
        redirect('../../pages/register.php');
    }

    $stmt->close();
    $conn->close();
}
?>