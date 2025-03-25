<?php
include '../../conn.php'; // Include your DB connection file
session_start(); // Start session

if (isset($_POST['cooperative_name'], $_POST['province'], $_POST['municipality'], $_POST['barangay'])) {
    $cooperative_name = trim($_POST['cooperative_name']);
    $province_code = trim($_POST['province']);
    $municipality_code = trim($_POST['municipality']);
    $barangay_code = trim($_POST['barangay']);
    $uid = $_SESSION['uid']; // Ensure this is set in the session

    // Fetch station_id from tbl_user
    $stmt = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $station_result = $stmt->get_result();
    $station_row = $station_result->fetch_assoc();
    $station_id = $station_row['station_id'] ?? null;

    if (!$station_id) {
        echo json_encode(["error" => "Station ID not found for the user"]);
        exit;
    }

    // Fetch Province Name
    $stmt = $conn->prepare("SELECT province_name FROM provinces WHERE province_code = ?");
    $stmt->bind_param("s", $province_code);
    $stmt->execute();
    $province_result = $stmt->get_result();
    $province_row = $province_result->fetch_assoc();
    $province_name = $province_row['province_name'] ?? 'Unknown Province';

    // Fetch Municipality Name
    $stmt = $conn->prepare("SELECT municipality_name FROM municipalities WHERE municipality_code = ?");
    $stmt->bind_param("s", $municipality_code);
    $stmt->execute();
    $municipality_result = $stmt->get_result();
    $municipality_row = $municipality_result->fetch_assoc();
    $municipality_name = $municipality_row['municipality_name'] ?? 'Unknown Municipality';

    // Fetch Barangay Name
    $stmt = $conn->prepare("SELECT barangay_name FROM barangays WHERE barangay_code = ?");
    $stmt->bind_param("s", $barangay_code);
    $stmt->execute();
    $barangay_result = $stmt->get_result();
    $barangay_row = $barangay_result->fetch_assoc();
    $barangay_name = $barangay_row['barangay_name'] ?? 'Unknown Barangay';

    // Check if the cooperative already exists
    $query = "SELECT COUNT(*) AS count FROM tbl_cooperative 
              WHERE cooperative_name = ? 
              AND province_name = ? 
              AND municipality_name = ? 
              AND barangay_name = ? 
              AND station_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $cooperative_name, $province_name, $municipality_name, $barangay_name, $station_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    echo json_encode(["exists" => $row['count'] > 0]);
}
?>
