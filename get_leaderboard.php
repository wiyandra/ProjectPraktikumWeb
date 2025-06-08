<?php
header('Content-Type: application/json');

// Include your database configuration file
require_once 'config.php'; // Adjust the path if config.php is in a different directory

// Check if the connection was successful from config.php
if ($conn->connect_error) {
    // If there's an error, it means the connection failed in config.php.
    // We'll output an error message as JSON.
    die(json_encode(['error' => "Database connection failed: " . $conn->connect_error]));
}

// SQL query to get the top 10 users by total_pengeluaran_pesanan
// We use the 'pengguna' table directly as it holds the live updated data.
$sql = "SELECT nama, total_pesanan, total_pengeluaran_pesanan FROM pengguna ORDER BY total_pengeluaran_pesanan DESC LIMIT 10";
$result = $conn->query($sql);

$leaderboardData = [];

if ($result) { // Check if the query was successful
    if ($result->num_rows > 0) {
        // Fetch data row by row
        while($row = $result->fetch_assoc()) {
            $leaderboardData[] = $row;
        }
    }
} else {
    // Handle SQL query error
    $leaderboardData = ['error' => 'SQL query failed: ' . $conn->error];
}

$conn->close();

echo json_encode($leaderboardData);
?>