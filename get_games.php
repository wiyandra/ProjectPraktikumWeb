<?php
// Include koneksi database dari config.php
include 'config.php'; // pastikan path-nya benar

// Query data dari tabel listgames
$sql = "SELECT nama_game FROM listgames";
$result = mysqli_query($conn, $sql);

$games = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $games[] = $row['nama_game'];
    }
}

// Kembalikan data dalam format JSON
header('Content-Type: application/json');
echo json_encode($games);
?>