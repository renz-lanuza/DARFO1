<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_darfo1";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Start session and get user station ID
session_start();
$user_station_id = $_SESSION['station_id'] ?? null; // Use null as fallback

if (!$user_station_id) {
    echo json_encode(["error" => "User station ID missing"]);
    exit;
}

// Query all municipalities (even those with 0 interventions)
$sql = "SELECT DISTINCT b.municipality_name FROM tbl_beneficiary b WHERE b.station_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_station_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $municipality = trim(strtolower($row['municipality_name']));
    $data[$municipality] = 0; // Default to 0 interventions
}
$stmt->close();

// Query municipalities with interventions
$sql = "SELECT b.municipality_name, COUNT(*) AS total_interventions
        FROM tbl_distribution d
        JOIN tbl_beneficiary b ON d.beneficiary_id = b.beneficiary_id
        WHERE d.station_id = ?
        GROUP BY b.municipality_name";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_station_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $municipality = trim(strtolower($row['municipality_name']));
    $data[$municipality] = $row['total_interventions'];
}

echo json_encode($data);
$conn->close();
?>
