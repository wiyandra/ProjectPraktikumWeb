<?php
include('config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['auto_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['auto_id'];
$targetDir = "uploads/";

// Create uploads directory if it doesn't exist
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {
    $fileType = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    // Check file size (max 5MB)
    if ($_FILES["photo"]["size"] > 5000000) {
        header("Location: profile.php?error=filesize");
        exit();
    }

    if (in_array($fileType, $allowedTypes)) {
        // Generate unique filename
        $fileName = $user_id . "_profile_" . time() . "." . $fileType;
        $targetFile = $targetDir . $fileName;
        
        // Get current profile image from database to delete old one
        $sql = "SELECT profile_image FROM pengguna WHERE auto_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $oldImage = $row['profile_image'];
            
            // Delete old profile image if exists
            if (!empty($oldImage) && file_exists($oldImage) && $oldImage !== 'user_file/default.png') {
                unlink($oldImage);
            }
        }

        // Upload new file
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            // Update database with new profile image path
            $sql_update = "UPDATE pengguna SET profile_image = ? WHERE auto_id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("si", $targetFile, $user_id);
            
            if ($stmt_update->execute()) {
                header("Location: profile.php?success=1");
                exit();
            } else {
                // If database update fails, delete the uploaded file
                unlink($targetFile);
                header("Location: profile.php?error=database");
                exit();
            }
        } else {
            header("Location: profile.php?error=upload");
            exit();
        }
    } else {
        header("Location: profile.php?error=filetype");
        exit();
    }
} else {
    header("Location: profile.php?error=nofile");
    exit();
}
?>
            
            if ($stmt_update->execute()) {
                header("Location: profile.php?success=1");
                exit();
            } else {
                // If database update fails, delete the uploaded file
                unlink($targetFile);
                header("Location: profile.php?error=database");
                exit();
            }
        } else {
            header("Location: profile.php?error=upload");
            exit();
        }
    } else {
        header("Location: profile.php?error=filetype");
        exit();
    }
} else {
    header("Location: profile.php?error=nofile");
    exit();
}
?>
