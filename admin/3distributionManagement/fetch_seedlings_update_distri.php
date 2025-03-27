
<?php
header('Content-Type: application/json');

include('../../conn.php');

$interventionId = $_GET['intervention_id'];

$sql = "SELECT seed_id, seed_name FROM tbl_seed_type WHERE int_type_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $interventionId);
$stmt->execute();
$result = $stmt->get_result();

$seedlings = [];
while ($row = $result->fetch_assoc()) {
    $seedlings[] = $row;
}

echo json_encode($seedlings);

$conn->close();
