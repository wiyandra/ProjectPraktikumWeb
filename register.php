<?php
include('config.php');

$error = '';  // Variabel untuk pesan error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil nilai dari form
    $nama = $_POST['nama'] ?? '';
    $username = $_POST['username']?? '';
    $no_hp = $_POST['no_hp']?? '';
    $email = $_POST['email']?? '';
    $password = $_POST['password']?? '';
    $confirm_password = $_POST['confirm_password']?? '';

    // Validasi input
    if (empty($nama) || empty($username) || empty($no_hp) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Semua kolom harus diisi.";
    } elseif ($password != $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {
        // Hash password sebelum disimpan
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        // Mengecek apakah username atau email sudah ada di database
        $sql = "SELECT * FROM pengguna WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username atau email sudah terdaftar.";
        } else {
            // Menyimpan data pengguna baru ke database
            $sql_insert = "INSERT INTO pengguna (nama, username, no_hp, email, password) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("sssss", $nama, $username, $no_hp, $email, $password_hashed);

            if ($stmt_insert->execute()) {
                session_start();
                $_SESSION['nama'] = $nama;
                header("Location: login.html");
                exit();

            } else {
                $error = "Terjadi kesalahan saat menyimpan data.";
                // Debugging untuk melihat pesan error dari MySQL
                echo "Error: " . $stmt_insert->error;
            }
        }
    }
}
?>