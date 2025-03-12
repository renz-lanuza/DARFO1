<?php
include('../../conn.php');

$response = ["success" => false];

if (isset($_GET['coop_id'])) {
    $coop_id = $_GET['coop_id'];

    // Update query to archive the seed type instead of deleting
    $query = "UPDATE tbl_cooperative SET archived_at = NOW() WHERE coop_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $coop_id);

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
