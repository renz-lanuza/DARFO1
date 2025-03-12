<?php
include('../../conn.php');

$response = ["success" => false];

if (isset($_GET['intervention_id'])) {
    $intervention_id = $_GET['intervention_id'];

    // Update the archive_at column with the current timestamp
    $query = "UPDATE tbl_intervention_inventory SET archived_at = NOW() WHERE intervention_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $intervention_id);

    if ($stmt->execute()) {
        $response["success"] = true;
    }

    $stmt->close();
    $conn->close();
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
