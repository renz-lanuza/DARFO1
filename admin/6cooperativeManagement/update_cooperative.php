<?php
require '../../conn.php'; // Database connection

$response = ["success" => false, "message" => "Invalid request"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $update_id = $_POST["update_id"] ?? null;
    $cooperative_name = $_POST["cooperative_name"] ?? null;
    $province = $_POST["province"] ?? null;
    $municipality = $_POST["municipality"] ?? null;
    $barangay = $_POST["barangay"] ?? null;

    if ($update_id && $cooperative_name && $province && $municipality && $barangay) {
        $sql = "UPDATE tbl_cooperative SET cooperative_name = ?, province_name = ?, municipality_name = ?, barangay_name = ? WHERE coop_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $cooperative_name, $province, $municipality, $barangay, $update_id);

        if ($stmt->execute()) {
            $response["success"] = true;
        } else {
            $response["message"] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $response["message"] = "Missing required fields.";
    }
}

echo json_encode($response);
?>
