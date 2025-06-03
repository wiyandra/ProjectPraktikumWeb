<?php
session_start();
include('config.php');

$response = ['success' => false, 'message' => ''];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'User not logged in';
    echo json_encode($response);
    exit();
}

$userId = $_SESSION['user_id'];

// Get user profile data
$sql = "SELECT username, nama, email, no_hp, profile_image FROM pengguna WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $response['success'] = true;
    $response['username'] = $row['username'];
    $response['nama'] = $row['nama'];
    $response['email'] = $row['email'];
    $response['no_hp'] = $row['no_hp'];
    $response['profile_image'] = $row['profile_image'];
} else {
    $response['message'] = 'User not found';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
