<?php
// update_seed.php

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_darfo1";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the form data
$seed_id = $_POST['seed_id'];
$seed_name = $_POST['seed_name'];

// Update the seed data in the database
$sql = "UPDATE tbl_seed_type SET seed_name = ? WHERE seed_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $seed_name, $seed_id);

if ($stmt->execute()) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>