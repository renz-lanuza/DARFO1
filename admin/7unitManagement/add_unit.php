<?php
header("Content-Type: application/json");
session_start();
include('../../conn.php');

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Check if POST data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $unit_name = trim($_POST['unit_name']);

    if (empty($unit_name)) {
        echo json_encode(["success" => false, "message" => "Unit name is required."]);
        exit();
    }

    // Allow only alphabetic characters and spaces
    if (!preg_match("/^[a-zA-Z\s]+$/", $unit_name)) {
        echo json_encode(["success" => false, "message" => "Invalid unit name. Only letters and spaces are allowed."]);
        exit();
    }

    // Retrieve uid from session
    if (!isset($_SESSION['uid'])) {
        echo json_encode(["success" => false, "message" => "User ID is not set in the session."]);
        exit();
    }

    $uid = $_SESSION['uid']; // Get the uid from the session

    // Fetch the station_id based on uid
    $stationSql = "SELECT station_id FROM tbl_user WHERE uid = ?";
    $stationStmt = $conn->prepare($stationSql);

    if (!$stationStmt) {
        echo json_encode(["success" => false, "message" => "SQL Error: " . $conn->error]);
        exit();
    }

    $stationStmt->bind_param("i", $uid);
    $stationStmt->execute();
    $stationStmt->bind_result($stationId);
    $stationStmt->fetch();
    $stationStmt->close();

    if (empty($stationId)) {
        echo json_encode(["success" => false, "message" => "Station ID not found for the given User ID."]);
        exit();
    }

    // Check if the unit name already exists for the same station
    $checkSql = "SELECT unit_name FROM tbl_unit WHERE LOWER(unit_name) = LOWER(?) AND station_id = ?";
    $checkStmt = $conn->prepare($checkSql);

    if (!$checkStmt) {
        echo json_encode(["success" => false, "message" => "SQL Error: " . $conn->error]);
        exit();
    }

    $checkStmt->bind_param("si", $unit_name, $stationId);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Unit name already exists for this station."]);
        $checkStmt->close();
        $conn->close();
        exit();
    }

    $checkStmt->close();

    // Insert the new unit along with station_id
    $sql = "INSERT INTO tbl_unit (unit_name, station_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "SQL Error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("si", $unit_name, $stationId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Unit added successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}

$conn->close();
