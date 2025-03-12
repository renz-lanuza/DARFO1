<?php
header('Content-Type: application/json');
include('../../conn.php'); // Include your database connection file
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['uid'])) {
        echo json_encode(["status" => "error", "message" => "User not logged in."]);
        exit;
    }

    // Retrieve input values from AJAX request
    $cooperative_name = $_POST['cooperative_name'] ?? null;
    $province_code = $_POST['province_code'] ?? null;
    $province_name = $_POST['province_name'] ?? null;
    $municipality_code = $_POST['municipality_code'] ?? null;
    $municipality_name = $_POST['municipality_name'] ?? null;
    $barangay_code = $_POST['barangay_code'] ?? null;
    $barangay_name = $_POST['barangay_name'] ?? null;
    $user_id = $_SESSION['uid'];

    // Validate required fields
    if (empty($cooperative_name) || empty($province_code) || empty($municipality_code) || empty($barangay_code)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    // Retrieve the station_id based on the logged-in user's uid
    $stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
    $stationQuery->bind_param("i", $user_id);
    $stationQuery->execute();
    $stationQuery->bind_result($station_id);
    $stationQuery->fetch();
    $stationQuery->close();

    if (empty($station_id)) {
        echo json_encode(["status" => "error", "message" => "No station found for the user."]);
        exit;
    }

    // Insert province (if not exists)
    $sql_insert_province = "INSERT IGNORE INTO provinces (province_code, province_name) VALUES (?, ?)";
    $stmt_insert_province = $conn->prepare($sql_insert_province);
    if ($stmt_insert_province) {
        $stmt_insert_province->bind_param("is", $province_code, $province_name);
        $stmt_insert_province->execute();
        $stmt_insert_province->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Error preparing province insert statement."]);
        exit;
    }

    // Insert municipality (if not exists)
    $sql_insert_municipality = "INSERT IGNORE INTO municipalities (municipality_code, municipality_name, province_code) VALUES (?, ?, ?)";
    $stmt_insert_municipality = $conn->prepare($sql_insert_municipality);
    if ($stmt_insert_municipality) {
        $stmt_insert_municipality->bind_param("isi", $municipality_code, $municipality_name, $province_code);
        $stmt_insert_municipality->execute();
        $stmt_insert_municipality->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Error preparing municipality insert statement."]);
        exit;
    }

    // Insert barangay (if not exists)
    $sql_insert_barangay = "INSERT IGNORE INTO barangays (barangay_code, barangay_name, municipality_code) VALUES (?, ?, ?)";
    $stmt_insert_barangay = $conn->prepare($sql_insert_barangay);
    if ($stmt_insert_barangay) {
        $stmt_insert_barangay->bind_param("isi", $barangay_code, $barangay_name, $municipality_code);
        $stmt_insert_barangay->execute();
        $stmt_insert_barangay->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Error preparing barangay insert statement."]);
        exit;
    }

    // Insert cooperative into the database
    $insertQuery = "INSERT INTO tbl_cooperative 
        (cooperative_name, province_name,municipality_name, barangay_name, station_id) 
        VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insertQuery);
    if ($stmt) {
        $stmt->bind_param("ssssi", $cooperative_name,  $province_name,  $municipality_name, $barangay_name, $station_id);
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Cooperative added successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to add cooperative."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Error preparing cooperative insert statement."]);
    }

    $conn->close();
}
