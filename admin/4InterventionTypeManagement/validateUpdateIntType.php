<?php
include '../../conn.php'; // Include your DB connection file
session_start(); // Ensure session is started

if (isset($_POST['intervention_name'])) {
    $intervention_name = trim($_POST['intervention_name']);
    $int_type_id = $_POST['int_type_id'];
    
    // Get station_id from session
    $station_id = $_SESSION['station_id']; 

    $query = "SELECT COUNT(*) AS count FROM tbl_intervention_type 
              WHERE intervention_name = ? AND int_type_id != ? AND station_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $intervention_name, $int_type_id, $station_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo json_encode(["exists" => $row['count'] > 0]);
}
?>
