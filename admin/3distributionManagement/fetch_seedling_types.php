<?php
require '../../conn.php';

$int_type_id = $_GET['int_type_id'] ?? 0;

$sql = "SELECT seedling_id, seedling_type FROM tbl_seed_type WHERE int_type_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $int_type_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}   
echo json_encode($data);
?>
