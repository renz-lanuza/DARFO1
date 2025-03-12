<?php
include '../../conn.php';
header('Content-Type: application/json');

if (!isset($_POST['userId']) || empty($_POST['userId'])) {
    echo json_encode(["status" => "error", "message" => "Invalid user ID"]);
    exit;
}

$userId = intval($_POST['userId']);

$query = "SELECT u.uid, u.username, u.ulevel, u.fname, u.mname, u.lname, 
                 s.station_id, s.station_name 
          FROM tbl_user u
          LEFT JOIN tbl_station s ON u.station_id = s.station_id
          WHERE u.uid = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "success", "data" => $result->fetch_assoc()]);
} else {
    echo json_encode(["status" => "error", "message" => "User not found"]);
}

$stmt->close();
$conn->close();
?>
