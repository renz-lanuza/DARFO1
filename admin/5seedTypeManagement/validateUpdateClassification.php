<?php
include '../../conn.php'; // Adjust this according to your project

$seed_name = $_POST['seed_name'];
$seed_id = $_POST['seed_id']; // Seed ID for update

$query = "SELECT COUNT(*) as count FROM tbl_seed_type WHERE seed_name = ? AND seed_id != ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $seed_name, $seed_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

echo json_encode(["exists" => $result['count'] > 0]);
?>
