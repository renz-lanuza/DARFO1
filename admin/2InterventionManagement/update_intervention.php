<?php
require '../../conn.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if required fields are set
if (isset($_POST['intervention_id'], $_POST['description'], $_POST['quantity'], $_POST['quantity_left'], $_POST['unit'])) {
    // Get data from POST
    $intervention_id = intval($_POST['intervention_id']);
    $description = trim($_POST['description']);
    $quantity = intval($_POST['quantity']);
    $quantity_left = intval($_POST['quantity_left']);
    $unit_id = intval($_POST['unit']);

    // Debug: Print received data
    if ($intervention_id == 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid intervention ID.']);
        exit;
    }

    // Debug: Print SQL Query before execution
    $query = "UPDATE tbl_intervention_inventory 
              SET description = ?, quantity = ?, quantity_left = ?, unit_id = ? 
              WHERE intervention_id = ?";

    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'SQL prepare failed: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("siiii", $description, $quantity, $quantity_left, $unit_id, $intervention_id);

    // Execute the query and check for success
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Intervention updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No changes made or intervention ID not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'SQL execution failed: ' . $stmt->error]);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
}
?>
