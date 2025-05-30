<?php
include('config.php');
session_start();

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = trim($_POST['username']);  // Bisa username atau email
    $password = $_POST['password'];

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

        // Jika username ditemukan
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Trim untuk menghapus spasi yang tidak diinginkan
            $password = trim($password);

            // Memeriksa apakah password yang dimasukkan cocok dengan yang ada di database
            if (password_verify($password, $row['password'])) {
                // Login berhasil, simpan session
                $_SESSION['user_id'] = $row['id'];  // Menyimpan ID pengguna di session
                $_SESSION['username'] = $row['username'];  // Menyimpan username di session
                $_SESSION['nama'] = $row['nama'];  // Menyimpan nama di session

                // Arahkan ke halaman dashboard
                echo "<script>alert('Login Berhasil!'); window.location.href = 'index.html';</script>";
                exit();
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Username tidak ditemukan.";
        }
    }
}
?>