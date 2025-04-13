<?php
// Mengambil file koneksi database
include('config.php');

$error = '';  // Variabel untuk pesan error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil nilai dari form
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $no_hp = $_POST['no_hp'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

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
                // Jika berhasil, arahkan ke halaman login
                echo "<script>alert('Registrasi berhasil!'); window.location.href = 'login.php';</script>";
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

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WSA TOPUP - Daftar Akun</title>
    <link rel="stylesheet" type="text/css" href="register-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="register-container">
        <div class="register-card">
            <div class="logo-text" style="display: flex; align-items: center;">
                <img src="Element/LOGOWSA.PNG" alt="WSA TOPUP LOGO" style="margin-right: 10px;">
                <div class="text-beside-logo">
                    <h6>WSA TOPUP</h6>
                    <p>MAKE YOUR LEVEL UP</p>
                </div>
            </div>    
            <div class="register-title">
                <h3>Daftar Akun</h3>
                <p>Daftar akun dengan mengisi form di bawah</p>
            </div>

            <!-- Menampilkan pesan error jika ada -->
            <?php if ($error != ''): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="input-row">
                    <div class="input-group half">
                        <label for="nama">Nama</label>
                        <input type="text" name="nama" id="nama" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="input-group half">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" placeholder="Masukkan username" required>
                    </div>
                </div>
                <div class="input-group">
                    <label for="no_hp">No Handphone</label>
                    <input type="text" name="no_hp" id="no_hp" placeholder="Masukkan nomor handphone" required>
                </div>

                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="Masukkan email" required>
                </div>

                <div class="input-row">
                    <div class="input-group half password-container">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Masukkan password" required>
                        <img src="image/eye-closed.png" class="password-toggle" alt="Show Password">
                    </div>

                    <div class="input-group half password-container">
                        <label for="confirm_password">Konfirmasi Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Ulangi password" required>
                        <img src="image/eye-closed.png" class="password-toggle" alt="Show Password">
                    </div>
                </div>

                <button type="submit" class="btn-register">Daftar Sekarang</button>
                
                <p class="login-text">Sudah punya akun?</p>
                <a href="login.php" class="btn-login">Masuk</a>
            </form>
        </div>

        <footer>
            <p>&copy; 2025 WSA TOPUP | Solusi Isi Ulang Terpercaya</p>
        </footer>
    </div>

    <script src="Script.js"></script>
</body>
</html>
