<?php
$servername = "localhost";
$username = "root"; // Sesuaikan dengan username MySQL kamu
$password = ""; // Kosongkan jika default, atau isi jika ada
$dbname = "wsatopup"; // Sesuaikan dengan nama database kamu

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
