<?php
    header("Content-Type: application/json");
    include('../../conn.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["int_type_id"], $_POST["seed_id"])) {
        $int_type_id = intval($_POST["int_type_id"]);
        $seed_id = intval($_POST["seed_id"]);

        $sql = "SELECT quantity_left FROM tbl_intervention_inventory WHERE int_type_id = ? AND seed_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ii", $int_type_id, $seed_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                echo json_encode(["success" => true, "quantity_left" => $row['quantity_left']]);
            } else {
                echo json_encode(["success" => false, "quantity_left" => 0]);
            }
            $stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "Database query error."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request."]);
    }
    $conn->close();
?>