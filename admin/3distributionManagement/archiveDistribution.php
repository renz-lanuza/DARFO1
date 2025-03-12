<?php
include('../../conn.php');

$response = ["success" => false];

if (isset($_GET['distribution_id'])) {
    $distribution_id = $_GET['distribution_id'];

    // Update the archived_at column with the current timestamp
    $query = "UPDATE tbl_distribution SET archived_at = NOW() WHERE distribution_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $distribution_id);

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
