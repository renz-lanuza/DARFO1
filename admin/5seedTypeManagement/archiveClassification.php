<?php
include('../../conn.php');

$response = ["success" => false];

if (isset($_GET['seed_id'])) {
    $seed_id = $_GET['seed_id'];

    // Update query to archive the seed type instead of deleting
    $query = "UPDATE tbl_seed_type SET archived_at = NOW() WHERE seed_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $seed_id);

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
