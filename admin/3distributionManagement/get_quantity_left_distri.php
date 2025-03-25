<?php
// get_quantity_left.php

// Debugging: Log the received request method and content type
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Content Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'Not set'));

// Read the raw input from the request body
$input = file_get_contents('php://input');
error_log("Raw Input: " . $input);

// Decode the JSON input
$data = json_decode($input, true);

// Debugging: Log the decoded data
if ($data === null) {
    error_log("Failed to decode JSON input");
    die(json_encode(['success' => false, 'message' => 'Invalid JSON input']));
}
error_log("Decoded Data: " . print_r($data, true));

// Check if the required parameters are present
if (!isset($data['int_type_id']) || !isset($data['seed_id'])) {
    error_log("Invalid request: Missing int_type_id or seed_id");
    die(json_encode(['success' => false, 'message' => 'Invalid request']));
}

$intTypeId = $data['int_type_id'];
$seedId = $data['seed_id'];

// Debugging: Log the received parameters
error_log("Received int_type_id: " . $intTypeId);
error_log("Received seed_id: " . $seedId);

// Connect to the database
$conn = new mysqli("localhost", "root", "", "db_darfo1");

// Check connection
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die(json_encode(['success' => false, 'message' => 'Connection failed']));
}

// Fetch quantity left from tbl_intervention_inventory
$sql = "SELECT quantity_left FROM tbl_intervention_inventory 
        WHERE int_type_id = ? AND seed_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Failed to prepare statement: " . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Failed to prepare statement']));
}

$stmt->bind_param("ii", $intTypeId, $seedId);
if (!$stmt->execute()) {
    error_log("Failed to execute query: " . $stmt->error);
    die(json_encode(['success' => false, 'message' => 'Failed to execute query']));
}

$stmt->bind_result($quantityLeft);
if (!$stmt->fetch()) {
    // No rows found
    $quantityLeft = 0;
}
$stmt->close();

// Debugging: Log the fetched quantity
error_log("Fetched quantity_left: " . $quantityLeft);

// Return the quantity left as JSON
echo json_encode(['success' => true, 'quantity_left' => $quantityLeft]);

$conn->close();
