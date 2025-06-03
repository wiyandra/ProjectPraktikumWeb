<?php
session_start();
include('config.php');

// Set the response content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to update your profile.']);
    exit();
}

$userId = $_SESSION['user_id'];
$response = ['success' => false, 'message' => ''];

// Create directory if it doesn't exist
$targetDir = "user_file/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Handle file upload
if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {
    $fileInfo = getimagesize($_FILES["photo"]["tmp_name"]);
    
    // Check if file is an image
    if ($fileInfo !== false) {
        $fileType = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        
        // Validate file type
        if (in_array(strtolower($fileType), $allowedTypes)) {
            // Validate file size (limit to 5MB)
            if ($_FILES["photo"]["size"] <= 5000000) {
                // Generate unique filename based on user ID
                $newFileName = $userId . "_profile." . $fileType;
                $targetFile = $targetDir . $newFileName;
                
                // Delete old file if exists (except default)
                $sql = "SELECT profile_image FROM pengguna WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    $oldImage = $row['profile_image'];
                    if ($oldImage != "user_file/default.png" && file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                }
                
                // Upload new image
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
                    // Update database with new image path
                    $sql = "UPDATE pengguna SET profile_image = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $targetFile, $userId);
                    
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = "Profile image updated successfully!";
                        $response['image_path'] = $targetFile;
                    } else {
                        $response['message'] = "Database error: " . $stmt->error;
                    }
                } else {
                    $response['message'] = "Failed to upload image.";
                }
            } else {
                $response['message'] = "File too large. Maximum size is 5MB.";
            }
        } else {
            $response['message'] = "Only JPG, JPEG, PNG and GIF files are allowed.";
        }
    } else {
        $response['message'] = "Uploaded file is not a valid image.";
    }
} else {
    $response['message'] = "No file was uploaded or there was an error.";
}

// Handle profile information update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nama']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['phone'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    // Check if username already exists (for another user)
    $checkSql = "SELECT id FROM pengguna WHERE username = ? AND id != ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ss", $username, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $response['message'] = "Username already exists. Please choose another one.";
    } else {        // Update profile information
        $sql = "UPDATE pengguna SET nama = ?, username = ?, email = ?, no_hp = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nama, $username, $email, $phone, $userId);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            if (empty($response['message'])) {
                $response['message'] = "Profil berhasil diperbarui!";
            }
        } else {
            $response['message'] = "Gagal memperbarui profil: " . $stmt->error;
        }
    }
}

// Return JSON response
echo json_encode($response);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Profile updated successfully!";
            
            // Update session data
            $_SESSION['username'] = $username;
            $_SESSION['nama'] = $nama;
        } else {
            $response['message'] = "Error updating profile: " . $stmt->error;
        }
    }
}

// Return to profile page with result
if ($response['success']) {
    echo "<script>alert('" . $response['message'] . "'); window.location.href = 'profile.html';</script>";
} else {
    echo "<script>alert('Error: " . $response['message'] . "'); window.location.href = 'profile.html';</script>";
}
?>
