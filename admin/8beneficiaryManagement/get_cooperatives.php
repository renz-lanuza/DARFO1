<?php
include '../../conn.php'; // Ensure this file has a valid $conn connection
session_start();


header("Content-Type: application/json");

// Get station_id from the logged-in user
$uid = $_SESSION['uid'] ?? null; // Ensure session is started before using $_SESSION

if (!$uid) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

// Fetch station_id of the logged-in user
$userQuery = "SELECT station_id FROM tbl_user WHERE uid = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $uid);
$stmt->execute();
$userResult = $stmt->get_result();
$userData = $userResult->fetch_assoc();

if (!$userData) {
    echo json_encode(["error" => "User not found"]);
    exit;
}

$station_id = $userData['station_id'];

// Fetch cooperatives based on station_id and exclude archived ones
$sql = "SELECT coop_id, cooperative_name FROM tbl_cooperative 
        WHERE station_id = ? AND archived_at IS NULL";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $station_id);
$stmt->execute();
$result = $stmt->get_result();

$cooperatives = [];

while ($row = $result->fetch_assoc()) {
    $cooperatives[] = [
        "id" => $row["coop_id"],
        "name" => $row["cooperative_name"]
    ];
}

// Return JSON response
echo json_encode($cooperatives);
?>
