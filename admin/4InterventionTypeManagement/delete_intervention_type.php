<?php
include('../../conn.php');

$response = ["success" => false];

if (isset($_GET['int_type_id'])) {
    $int_type_id = $_GET['int_type_id'];

    // SQL to delete the intervention type
    $query = "DELETE FROM tbl_intervention_type WHERE int_type_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $int_type_id);

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
