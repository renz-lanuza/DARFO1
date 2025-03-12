<?php
include('../../conn.php');

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['int_type_id'])) {
    $int_type_id = $_POST['int_type_id'];

    // Update archived_at to current timestamp
    $query = "UPDATE tbl_intervention_type SET archived_at = NOW() WHERE int_type_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $int_type_id);

    if ($stmt->execute()) {
        $response["success"] = true;
    }

    $stmt->close();
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>
