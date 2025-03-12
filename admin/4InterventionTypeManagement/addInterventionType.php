<?php
    header("Content-Type: application/json");

    session_start(); // Start the session to access session variables
    include('../../conn.php');

    // Check connection
    if ($conn->connect_error) {
        die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
    }

    // Check if POST data is received
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $interventionName = trim($_POST['interventionName']);

        if (empty($interventionName)) {
            echo json_encode(["success" => false, "message" => "Intervention name is required."]);
            exit();
        }

        // Retrieve uid from session
        if (!isset($_SESSION['uid'])) {
            echo json_encode(["success" => false, "message" => "User  ID is not set in the session."]);
            exit();
        }

        $uid = $_SESSION['uid']; // Get the uid from the session

        // Fetch the station_id based on uid
        $stationSql = "SELECT station_id FROM tbl_user WHERE uid = ?";
        $stationStmt = $conn->prepare($stationSql);

        if (!$stationStmt) {
            echo json_encode(["success" => false, "message" => "SQL Error: " . $conn->error]);
            exit();
        }

        $stationStmt->bind_param("i", $uid); // Assuming uid is an integer
        $stationStmt->execute();
        $stationStmt->bind_result($stationId);
        $stationStmt->fetch();
        $stationStmt->close();

        if (empty($stationId)) {
            echo json_encode(["success" => false, "message" => "Station ID not found for the given User ID."]);
            exit();
        }

        // Check if the intervention name already exists (case-insensitive)
        $checkSql = "SELECT intervention_name FROM tbl_intervention_type WHERE LOWER(intervention_name) = LOWER(?) AND station_id = ?";
        $checkStmt = $conn->prepare($checkSql);

        if (!$checkStmt) {
            echo json_encode(["success" => false, "message" => "SQL Error: " . $conn->error]);
            exit();
        }

        $checkStmt->bind_param("si", $interventionName, $stationId);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            echo json_encode(["success" => false, "message" => "Intervention name already exists."]);
            $checkStmt->close();
            $conn->close();
            exit();
        }

        $checkStmt->close();

        // Insert the new intervention name along with station_id
        $sql = "INSERT INTO tbl_intervention_type (intervention_name, station_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            echo json_encode(["success" => false, "message" => "SQL Error: " . $conn->error]);
            exit();
        }

        $stmt->bind_param("si", $interventionName, $stationId); // Assuming station_id is an integer

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Intervention name added successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request."]);
    }

    $conn->close();
?>