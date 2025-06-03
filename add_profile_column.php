<?php
// Quick script to add profile_image column to pengguna table
include('config.php');

// Check if column already exists
$result = $conn->query("SHOW COLUMNS FROM pengguna LIKE 'profile_image'");
if ($result->num_rows == 0) {
    // Column doesn't exist, add it
    $sql = "ALTER TABLE pengguna ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL";
    if ($conn->query($sql) === TRUE) {
        echo "Column 'profile_image' added successfully to 'pengguna' table.\n";
    } else {
        echo "Error adding column: " . $conn->error . "\n";
    }
} else {
    echo "Column 'profile_image' already exists in 'pengguna' table.\n";
}

$conn->close();
?>
