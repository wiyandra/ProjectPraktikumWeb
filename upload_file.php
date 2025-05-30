<?php
session_start();
$userId = $_SESSION['user_id'] ?? 1;

$targetDir = "uploads/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {
    $fileType = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileType, $allowedTypes)) {
        $targetFile = $targetDir . $userId . "_profile.jpg";
        
        // Hapus file lama jika ada
        if (file_exists($targetFile)) {
            unlink($targetFile);
        }

        // Upload file baru
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            header("Location: profil.php");
            exit();
        } else {
            echo "Gagal mengunggah file.";
        }
    } else {
        echo "Hanya file JPG, PNG, JPEG, dan GIF yang diperbolehkan.";
    }
} else {
    echo "Tidak ada file dipilih atau terjadi error.";
}
?>
