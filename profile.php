<?php
session_start();

// Default gambar profil
$profileImage = 'user_file/default.png';

if (isset($_SESSION['profile_image']) && file_exists($_SESSION['profile_image'])) {
    $profileImage = $_SESSION['profile_image'];
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
<body>
  <div class="profilehtml">
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
          <div class="text-wrapper-5">Hi, Username</div>
          <p class="text-wrapper-10">
            Informasi ini bersifat pribadi, berhati-hatilah dalam membagikan informasi.
          </p>
        </div>
      </div>
    </form>
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
  </script>
</body>
</html>
