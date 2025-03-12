<?php
    require '../../conn.php';

    if (isset($_POST['intervention_id'])) {
        $intervention_id = $_POST['intervention_id'];
    
        $query = "SELECT 
                    i.intervention_id, 
                    i.int_type_id, 
                    t.intervention_name, 
                    i.description, 
                    i.quantity, 
                    i.quantity_left,
                    i.seed_id,
                    s.seed_name
                  FROM tbl_intervention_inventory i
                  JOIN tbl_intervention_type t ON i.int_type_id = t.int_type_id
                  JOIN tbl_seed_type s ON i.seed_id = s.seed_id
                  WHERE i.intervention_id = ?";
    
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $intervention_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($row = $result->fetch_assoc()) {
            echo json_encode($row);
        } else {
            echo json_encode(["error" => "No record found"]);
        }
    
        $stmt->close();
        $conn->close();
    }
?>