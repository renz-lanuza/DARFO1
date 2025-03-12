<?php
require '../../conn.php'; // Ensure DB connection

$response = ["error" => true, "message" => "Invalid request"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["coop_id"])) {
    $coop_id = $_POST["coop_id"];

    $sql = "SELECT coop_id, cooperative_name, province_name, municipality_name, barangay_name 
            FROM tbl_cooperative WHERE coop_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $coop_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $response = [
                "error" => false,
                "coop_id" => $row["coop_id"],
                "cooperative_name" => $row["cooperative_name"],
                "province_name" => $row["province_name"],
                "municipality_name" => $row["municipality_name"],
                "barangay_name" => $row["barangay_name"]
            ];
        } else {
            $response["message"] = "No cooperative found.";
        }
    } else {
        $response["message"] = "Database error: " . $stmt->error;
    }
    $stmt->close();
}

echo json_encode($response);
?>
