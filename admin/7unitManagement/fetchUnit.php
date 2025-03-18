<?php
include('../../conn.php');

if (isset($_GET['unit_id'])) {
    $unit_id = $_GET['unit_id'];

    $stmt = $conn->prepare("SELECT unit_id, unit_name FROM tbl_unit WHERE unit_id = ?");
    $stmt->bind_param("i", $unit_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "Unit not found"]);
    }

    $stmt->close();
}
$conn->close();
?>
