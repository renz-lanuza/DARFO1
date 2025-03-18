<?php
include('../../conn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $unit_id = $_POST['unit_id'];
    $unit_name = $_POST['unit_name'];

    $stmt = $conn->prepare("UPDATE tbl_unit SET unit_name = ? WHERE unit_id = ?");
    $stmt->bind_param("si", $unit_name, $unit_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Update failed"]);
    }

    $stmt->close();
}
$conn->close();
?>
