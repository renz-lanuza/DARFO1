<?php
    // Include the connection to the database
    include('../../conn.php');

    // Start the session to access session variables
    session_start();

    // Check if the form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve and trim the POST data
        $intTypeId = trim($_POST['interventionName11']); // This is the int_type_id
        $seedTypeName = trim($_POST['seed_type_name']); // The seed type name entered by the user

        // Check if the fields are empty
        if (empty($intTypeId) || empty($seedTypeName)) {
            echo json_encode(["success" => false, "message" => "Both Intervention ID and Seed Name are required."]);
            exit();
        }

        // Retrieve the uid from the session
        if (!isset($_SESSION['uid'])) {
            echo json_encode(["success" => false, "message" => "User  ID not found in session."]);
            exit();
        }

        $userId = $_SESSION['uid']; // Get the uid from the session

        // Retrieve the station_id based on the logged-in user's uid
        $stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
        $stationQuery->bind_param("i", $userId);
        $stationQuery->execute();
        $stationQuery->bind_result($stationId);
        $stationQuery->fetch();
        $stationQuery->close();

        // Check if station_id was found
        if (empty($stationId)) {
            echo json_encode(["success" => false, "message" => "No station found for the user."]);
            exit();
        }

        // Debugging output - Log inputs
        error_log("Checking for duplicate: int_type_id = $intTypeId, seed_name = '$seedTypeName', station_id = $stationId");

        // Check for duplicate combination of int_type_id, seed_name, and station_id (case-insensitive)
        $checkSql = "SELECT * FROM tbl_seed_type WHERE int_type_id = ? AND LOWER(TRIM(seed_name)) = LOWER(TRIM(?)) AND station_id = ?";

        $checkStmt = $conn->prepare($checkSql);

        if (!$checkStmt) {
            echo json_encode(["success" => false, "message" => "SQL Error: " . $conn->error]);
            exit();
        }

        $checkStmt->bind_param("isi", $intTypeId, $seedTypeName, $stationId);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            echo json_encode(["success" => false, "message" => "The combination of Intervention ID, Seed Name, and Station ID already exists."]);
            $checkStmt->close();
            $conn->close();
            exit();
        }

        // Insert the new record if no duplicates are found
        $sql = "INSERT INTO tbl_seed_type (int_type_id, seed_name, station_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            echo json_encode(["success" => false, "message" => "SQL Error: " . $conn->error]);
            exit();
        }

        $stmt->bind_param("isi", $intTypeId, $seedTypeName, $stationId); // Bind the station_id

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Seed type added successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
        }

        $stmt->close();
    }

    // Close the database connection
    $conn->close();
?>