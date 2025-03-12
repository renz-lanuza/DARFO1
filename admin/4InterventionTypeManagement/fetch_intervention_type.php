<?php
include('../../conn.php');

if (isset($_POST['int_type_id'])) {
    $int_type_id = $_POST['int_type_id'];

    // Prepare and execute the query
    $query = "SELECT int_type_id, intervention_name, station_id FROM tbl_intervention_type WHERE int_type_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $int_type_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(["success" => true, "data" => $row]);
    } else {
        echo json_encode(["success" => false, "message" => "No record found."]);
    }
}
