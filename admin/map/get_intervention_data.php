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

// Get user station ID from session (assuming user login)
session_start();
$user_station_id = $_SESSION['station_id']; // Adjust based on your auth system

$sql = "SELECT b.municipality_name, COUNT(*) AS total_interventions
            FROM tbl_distribution d
            JOIN tbl_beneficiary b ON d.beneficiary_id = b.beneficiary_id
            WHERE d.station_id = ?
            GROUP BY b.municipality_name";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_station_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) { 
    $data[$row['municipality_name']] = $row['total_interventions'];
}

echo json_encode($data);
$conn->close();
?>
