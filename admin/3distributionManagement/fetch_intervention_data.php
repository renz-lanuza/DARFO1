<?php
require '../../conn.php'; // Ensure correct database connection

if (isset($_GET['int_type_id'])) {
    $int_type_id = $_GET['int_type_id'];

    // Fetch classifications and available quantity with LEFT JOIN
    $query = "SELECT s.seed_id, s.seed_name, 
                     COALESCE(i.quantity, 0) AS quantity
              FROM tbl_seed_type s
              LEFT JOIN tbl_intervention_inventory i ON s.seed_id = i.seed_id
              WHERE s.int_type_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $int_type_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'seed_id' => $row['seed_id'],
            'seed_name' => $row['seed_name'],
            'quantity' => $row['quantity']
        ];
    }

    echo json_encode($data);
}
?>
