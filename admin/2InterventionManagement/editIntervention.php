<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('../../conn.php');

    $intervention_id = $_POST["intervention_id"];
    $intervention_name = $_POST["interventionName11"];
    $description = $_POST["description"];
    $quantity = $_POST["quantity"];

    $sql = "UPDATE tbl_intervention 
            SET intervention_name=?, description=?, quantity=? 
            WHERE intervention_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $intervention_name, $description, $quantity, $intervention_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Intervention updated successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Update failed!"]);
    }

    $stmt->close();
    $conn->close();
}
