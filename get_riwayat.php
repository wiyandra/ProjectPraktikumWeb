<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json');
session_start(); 

require_once 'config.php'; 

// Check if the connection was successful from config.php
if ($conn->connect_error) {
    die(json_encode(['error' => "Database connection failed: " . $conn->connect_error]));
}

$riwayatData = [];

// Check if user is logged in and has an ID
if (!isset($_SESSION['auto_id'])) {
    die(json_encode(['error' => 'User not authenticated. Please log in.', 'not_logged_in' => true]));
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT
            r.tanggal,      -- Changed from tanggal_pembelian to tanggal
            r.idbeli,       -- Changed from id_pembelian to idbeli
            -- r.katalog_pembelian, -- REMOVED: This column does not exist in your provided CREATE TABLE
            r.harga
        FROM
            riwayat r
        WHERE
            r.id = ?
        ORDER BY
            r.tanggal DESC"; // Changed to tanggal for ordering

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $riwayatData[] = $row;
        }
    }
    $stmt->close();
} else {
    $riwayatData = ['error' => 'SQL query preparation failed: ' . $conn->error];
}

$conn->close();

echo json_encode($riwayatData);
?>