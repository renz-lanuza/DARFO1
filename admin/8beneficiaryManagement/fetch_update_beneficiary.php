<?php
require '../../conn.php'; // Include your database connection file

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $conn->prepare("SELECT * FROM tbl_beneficiary WHERE beneficiary_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(null);
    }
    
    $stmt->close();
    $conn->close();
}
?>
