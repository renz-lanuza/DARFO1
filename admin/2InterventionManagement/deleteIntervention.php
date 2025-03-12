<?php
include('../../conn.php');

$response = ["success" => false];

if (isset($_GET['intervention_id'])) {
    $intervention_id = $_GET['intervention_id'];

    $query = "DELETE FROM tbl_intervention_inventory WHERE intervention_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $intervention_id);

    if ($stmt->execute()) {
        $response["success"] = true;
    }

    $stmt->close();
    $conn->close();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
