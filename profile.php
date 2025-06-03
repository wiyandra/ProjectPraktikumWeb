<?php
include('config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['auto_id'])) {
    header("Location: login.html");
    exit();
}

// Get user information from database
$user_id = $_SESSION['auto_id'];
$sql = "SELECT * FROM pengguna WHERE auto_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Handle profile image
    if (!empty($user['profile_image']) && file_exists($user['profile_image'])) {
        $profileImage = $user['profile_image'];
    } elseif (file_exists('user_file/default.png')) {
        $profileImage = 'user_file/default.png';
    } else {
        // Use a CSS-based avatar as fallback
        $profileImage = 'data:image/svg+xml;base64,' . base64_encode('
            <svg width="140" height="140" xmlns="http://www.w3.org/2000/svg">
                <rect width="140" height="140" fill="#424242"/>
                <circle cx="70" cy="50" r="25" fill="#666"/>
                <circle cx="70" cy="110" r="35" fill="#666"/>
                <text x="70" y="130" text-anchor="middle" fill="white" font-size="12">No Image</text>
            </svg>
        ');
    }
} else {
    // User not found, redirect to login
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Profile</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body>  <div class="profilehtml">
    <!-- Header Navigation -->
    <header>
      <div class="container">
        <div class="logo">
          <a href="index.html">
            <img src="Element/LOGOWSA.PNG" alt="WSA Logo" class="logo-img">
          </a>
        </div>        <nav>
          <ul>
            <li><a href="index.html">Dashboard</a></li>
            <li><a href="leaderboard.html">Leaderboard</a></li>
            <li><a href="riwayat-page.html">Riwayat</a></li>
            <li><a href="kalkulator-page.html">Kalkulator</a></li>
          </ul>
        </nav>
        <div class="search-profile">
          <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Cari Games...">
            <ul id="searchResults"></ul>
          </div>
        </div>
      </div>
    </header>    <?php
    // Display success/error messages
    if (isset($_GET['success']) && $_GET['success'] == '1') {
        echo '<div class="profile-message success">Foto profil berhasil diperbarui!</div>';
    } elseif (isset($_GET['error'])) {
        $error_msg = '';
        switch ($_GET['error']) {
            case 'filetype':
                $error_msg = 'Hanya file JPG, PNG, JPEG, dan GIF yang diperbolehkan.';
                break;
            case 'filesize':
                $error_msg = 'Ukuran file terlalu besar. Maksimal 5MB.';
                break;
            case 'upload':
                $error_msg = 'Gagal mengunggah file.';
                break;
            case 'database':
                $error_msg = 'Gagal menyimpan informasi ke database.';
                break;
            case 'nofile':
                $error_msg = 'Tidak ada file yang dipilih.';
                break;
            default:
                $error_msg = 'Terjadi kesalahan yang tidak diketahui.';
        }
        echo '<div class="profile-message error">' . htmlspecialchars($error_msg) . '</div>';
    }
    ?>

    <form action="upload_profile.php" method="POST" enctype="multipart/form-data">
      <div class="profile-header">
        <div class="profile-img-wrapper">
          <img class="img" id="profileImg" src="<?= htmlspecialchars($profileImage) ?>" alt="Profile Image" />
          <label for="upload-photo" class="btn-profile-plus" title="Ganti Foto Profil">
            <i class="fas fa-plus"></i>
          </label>
          <input type="file" name="photo" id="upload-photo" accept="image/*" style="display: none;" />
        </div>
        <div class="profile-nama">
          <div class="text-wrapper-5">Hi, <?= htmlspecialchars($user['nama']) ?></div>
          <p class="text-wrapper-10">
            Informasi ini bersifat pribadi, berhati-hatilah dalam membagikan informasi.
          </p>
        </div>
      </div>
    </form>

    <div class="profile-form-grid">
      <div class="profile-form-group">
        <label>Nama Kamu</label>
        <div class="profile-info-display"><?= htmlspecialchars($user['nama']) ?></div>
      </div>
      <div class="profile-form-group">
        <label>Username</label>
        <div class="profile-info-display"><?= htmlspecialchars($user['username']) ?></div>
      </div>
      <div class="profile-form-group">
        <label>Alamat Email</label>
        <div class="profile-info-display"><?= htmlspecialchars($user['email']) ?></div>
      </div>
      <div class="profile-form-group">
        <label>No. Handphone</label>
        <div class="profile-info-display"><?= htmlspecialchars($user['no_hp']) ?></div>
      </div>
    </div>

    <div class="profile-action-row">
      <a href="index.html" class="btn" style="position:static;width:180px;text-decoration:none;display:flex;align-items:center;justify-content:center;">Kembali ke Beranda</a>
    </div>

    <div class="profile-password-row">
      <div class="profile-password-group">
        <label for="password-lama">Password Lama</label>
        <input type="password" id="password-lama" class="input-field" placeholder="Masukkan password lama">
      </div>
      <div class="profile-password-group">
        <label for="password-baru">Password Baru</label>
        <input type="password" id="password-baru" class="input-field" placeholder="Masukkan password baru">
      </div>
      <a href="forget_password.php" class="lupapasswrd">Lupa Password?</a>
      <button class="profile-password-btn" type="button" onclick="changePassword()">Ubah Password</button>
    </div>
  </div>

  <script>
    const fileInput = document.getElementById('upload-photo');
    const profileImg = document.getElementById('profileImg');

    fileInput.addEventListener('change', function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          profileImg.src = e.target.result;
        };
        reader.readAsDataURL(file);
        this.form.submit();
      }
    });

    function changePassword() {
      const oldPassword = document.getElementById('password-lama').value;
      const newPassword = document.getElementById('password-baru').value;
      
      if (!oldPassword || !newPassword) {
        alert('Silahkan isi password lama dan password baru');
        return;
      }
      
      if (newPassword.length < 6) {
        alert('Password baru minimal 6 karakter');
        return;
      }
      
      // You can implement password change functionality here
      alert('Fitur ubah password akan dikembangkan lebih lanjut');
    }
  </script>
</body>
</html>
