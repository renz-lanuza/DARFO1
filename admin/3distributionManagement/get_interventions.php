<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['uid'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$uid = $_SESSION['uid']; // Get the uid from the session

// Connect to the database
$conn = new mysqli("localhost", "root", "", "db_darfo1");

// Check for connection errors
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

// Retrieve the station_id based on the logged-in user's uid
$stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
$stationQuery->bind_param("i", $uid);
$stationQuery->execute();
$stationQuery->bind_result($stationId);
$stationQuery->fetch();
$stationQuery->close();

// Check if station_id was found
if (empty($stationId)) {
    echo json_encode(["error" => "No station found for the user"]);
    exit;
}

// Fetch intervention names from the database filtered by station_id
$sql = "SELECT int_type_id, intervention_name FROM tbl_intervention_type WHERE station_id = ? ORDER BY int_type_id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $stationId);
$stmt->execute();
$result = $stmt->get_result();

$interventions = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $interventions[] = $row;
    }
}

// Return JSON response
echo json_encode($interventions);

// Close the database connection
$conn->close();
?>
