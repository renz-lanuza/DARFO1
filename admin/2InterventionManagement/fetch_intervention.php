<?php
require '../../conn.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['intervention_id'])) {
    $intervention_id = $_POST['intervention_id'];

    $query = "SELECT 
                i.intervention_id, 
                t.intervention_name, 
                i.description, 
                i.quantity, 
                i.quantity_left,
                s.seed_name,
                u.unit_id,  
                u.unit_name  
              FROM tbl_intervention_inventory i
              JOIN tbl_intervention_type t ON i.int_type_id = t.int_type_id
              JOIN tbl_seed_type s ON i.seed_id = s.seed_id
              LEFT JOIN tbl_unit u ON i.unit_id = u.unit_id
              WHERE i.intervention_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $intervention_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        error_log("Fetched unit_id: " . $row["unit_id"]); // Debugging
        echo json_encode([
            "intervention_id" => $row["intervention_id"],
            "intervention_name" => $row["intervention_name"],
            "description" => $row["description"],
            "quantity" => $row["quantity"],
            "quantity_left" => $row["quantity_left"],
            "seed_name" => $row["seed_name"],
            "unit_id" => $row["unit_id"],  // Hidden field
            "unit_name" => $row["unit_name"] // Displayed in the dropdown
        ]);
    } else {
        echo json_encode(["error" => "No record found"]);
    }

    $stmt->close();
    $conn->close();
}
?>
