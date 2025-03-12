<?php
include('../../conn.php');

if (isset($_POST['int_type_id']) && isset($_POST['intervention_name'])) {
    $int_type_id = $_POST['int_type_id'];
    $intervention_name = $_POST['intervention_name'];

    // Prepare and execute the update query
    $query = "UPDATE tbl_intervention_type SET intervention_name = ? WHERE int_type_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $intervention_name, $int_type_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update intervention type."]);
    }
}
