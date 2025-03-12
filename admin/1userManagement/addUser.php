<?php
session_start(); // Start the session to access session variables

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_darfo1";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection error
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Collect form data from POST request
$firstName = $_POST['firstName'];
$middleName = $_POST['middleName'];
$lastName = $_POST['lastName'];
$username = $_POST['username'];
$password = $_POST['password'];
$userLevel = $_POST['userLevel'];
$station = $_POST['station'];

// Hash the password for security
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO tbl_user (fname, mname, lname, username, password, ulevel, station_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
if ($stmt === false) {
    die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
}

$status = 0; // Set status to 0 for newly added users
$stmt->bind_param("sssssssi", $firstName, $middleName, $lastName, $username, $hashedPassword, $userLevel, $station, $status);

// Execute and check if successful
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
