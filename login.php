<?php
session_start();
include 'config.php'; // Koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameOrEmail = trim($_POST['username']);
    $password = $_POST['password'];

    // Cek data user di database (bisa login pakai username atau email)
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    // Cek apakah user ada
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Set session (bisa dikembangkan sesuai kebutuhan)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Kalau mau ada perbedaan admin/user

            // Redirect ke halaman utama
            header("Location: dashboard.php");
            exit;
        } else {
            // Password salah
            header("Location: login.html?error=Password salah!");
            exit;
        }
    } else {
        // User tidak ditemukan
        header("Location: login.html?error=Akun tidak ditemukan!");
        exit;
    }
}
?>
