<?php
include '../../conn.php'; // Include your database connection

if (isset($_POST['unit_name'])) {
    $unitName = trim($_POST['unit_name']);
    $unitId = isset($_POST['unit_id']) ? $_POST['unit_id'] : 0;

    $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_unit WHERE unit_name = ? AND unit_id != ?");
    $stmt->bind_param("si", $unitName, $unitId);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    echo json_encode(["exists" => $count > 0]);
}
?>
