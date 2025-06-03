<?php
include('config.php');
session_start();

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = trim($_POST['username']);  // Bisa berupa username atau email
    $password = $_POST['password'] ?? '';

    if (empty($identifier) || empty($password)) {
        $error = "Username/email dan password harus diisi.";
    } else {
        // Cek apakah input adalah email atau username
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $sql = "SELECT * FROM pengguna WHERE email = ?";
        } else {
            $sql = "SELECT * FROM pengguna WHERE username = ?";
        }

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Verifikasi password dengan hash
            if (password_verify(trim($password), $row['password'])) {
                // Simpan session
                $_SESSION['auto_id'] = $row['auto_id'];   // Sesuaikan dengan struktur database
                $_SESSION['username'] = $row['username'];
                $_SESSION['nama'] = $row['nama'];

                // Redirect ke index.html
                echo "<script>alert('Login Berhasil!'); window.location.href = 'index.html';</script>";
                exit();
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Username atau email tidak ditemukan.";
        }
    }
}

// Jika ada error, tampilkan alert
if (!empty($error)) {
    echo "<script>alert('$error'); window.location.href = 'login.html';</script>";
    exit();
}
?>
