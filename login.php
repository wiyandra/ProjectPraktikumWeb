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

<!-- HTML Form for Login -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WSA TOPUP - Masuk Akun</title>
    
    <link rel="stylesheet" type="text/css" href="login-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-text" style="display: flex; align-items: center;">
                <img src="Element/LOGOWSA.PNG" alt="WSA TOPUP LOGO" style="margin-right: 10px;">
                <div class="text-beside-logo">
                    <h6>WSA TOPUP</h6>
                    <p>MAKE YOUR LEVEL UP</p>
                </div>
            </div>
            
            <h4>Masuk Akun</h4>
            <h5>Masuk menggunakan akun terdaftar milikmu.</h5>

            <?php if ($error != ''): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="login.php" method="post">
                <div class="input-group">
                    <label for="username">Username/Email</label>
                    <input type="text" name="username" id="username" placeholder="Masukkan username atau email" required>
                </div>
                <div class="input-group password-container">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Masukkan password" required>
                    <img src="image/eye-closed.png" class="password-toggle" alt="Show Password">
                </div>
                <div class="option">
                    <label>
                        <input type="checkbox" name="remember"> Ingat Saya
                    </label>
                    <a href="#" class="forgot-password">Lupa Password?</a>
                </div>

                <button type="submit" class="btn-login">Masuk</button>
                <div>
                    <p class="register-text">Belum punya akun?</p>
                    <a href="register.php" target="_blank" class="btn-register">Daftar Sekarang</a>
                </div>
            </form>
        </div>
        <footer>
            <p>&copy; 2025 WSA TOPUP | Solusi Isi Ulang Terpercaya</p>
        </footer>
    </div>
    <script src="Script.js"></script>
</body>
</html>
