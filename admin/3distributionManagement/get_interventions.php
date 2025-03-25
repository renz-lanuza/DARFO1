<?php
session_start();
header('Content-Type: application/json');

// Connect to the database
$conn = new mysqli("localhost", "root", "", "db_darfo1");

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Get the station_id from the session
$uid = $_SESSION['uid'];
$stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
$stationQuery->bind_param("i", $uid);
$stationQuery->execute();
$stationQuery->bind_result($stationId);
$stationQuery->fetch();
$stationQuery->close();

// Debugging: Log the station ID
error_log("Station ID: " . $stationId);

// Fetch interventions for the station
$sql = "SELECT DISTINCT int_type_id, intervention_name FROM tbl_intervention_type WHERE station_id = ? ORDER BY int_type_id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $stationId);
$stmt->execute();
$result = $stmt->get_result();

// Prepare the response array
$interventions = [];
while ($row = $result->fetch_assoc()) {
    $interventions[] = $row;
}

// Debugging: Log the fetched interventions
error_log("Fetched Interventions: " . json_encode($interventions));

// Return the JSON response
echo json_encode($interventions);

// Close the connection
$stmt->close();
$conn->close();
