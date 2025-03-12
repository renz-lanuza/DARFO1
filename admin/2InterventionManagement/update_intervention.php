<?php
require '../../conn.php';

if (isset($_POST['intervention_id'])) {
    // Retrieve posted data from form
    $intervention_id = $_POST['intervention_id'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $quantity_left = $_POST['quantity_left'];

    // SQL query to update the intervention data (excluding intervention_name and seed_name)
    $query = "UPDATE tbl_intervention_inventory
              SET description = ?, quantity = ?, quantity_left = ?
              WHERE intervention_id = ?";

    // Prepare and bind parameters
    $stmt = $conn->prepare($query);
    $stmt->bind_param("siii", $description, $quantity, $quantity_left, $intervention_id);

    // Execute the query and send response
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    // Close the prepared statement and connection
    $stmt->close();
    $conn->close();
}
