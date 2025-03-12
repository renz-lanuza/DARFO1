<?php
    session_start();
    header("Content-Type: application/json"); // Ensure response is JSON

    include('../../conn.php'); // Include your database connection file

    // Ensure all required fields are set
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["interventionName11"], $_POST["interventionDescription"], $_POST["interventionQty"])) {
        $int_type_id = htmlspecialchars($_POST["interventionName11"]);
        $description = htmlspecialchars($_POST["interventionDescription"]);
        $quantity = intval($_POST["interventionQty"]); // Ensure it's an integer
        $seed_id = $_POST["seedling_type"];
        $unit = $_POST["unit"];

        // Check if quantity is valid
        if ($quantity <= 0) {
            echo json_encode(["status" => "error", "message" => "Quantity must be greater than zero."]);
            exit;
        }

        // Retrieve the user's station name from the tbl_user table
        if (!isset($_SESSION['uid'])) {
            echo json_encode(["status" => "error", "message" => "User  not logged in."]);
            exit;
        }

        $user_id = $_SESSION['uid']; // Assuming you stored the user ID in the session

        // Fetch the station name for the logged-in user
        $stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
        $stationQuery->bind_param("i", $user_id);
        $stationQuery->execute();
        $stationQuery->bind_result($station_id);
        $stationQuery->fetch();
        $stationQuery->close();

        // Check if the station name was found
        if ($station_id === null) {
            echo json_encode(["status" => "error", "message" => "Station not found for the user."]);
            exit;
        }

        // Insert intervention first
        $sql = "INSERT INTO tbl_intervention_inventory (int_type_id, description, quantity, quantity_left, seed_id, unit, station_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(["status" => "error", "message" => "SQL error: " . $conn->error]);
            exit;
        }

        // Set quantity_left to be the same as quantity
        $quantity_left = $quantity;
        $stmt->bind_param("ssiiisi", $int_type_id, $description, $quantity, $quantity_left, $seed_id, $unit, $station_id);

        if ($stmt->execute()) {
            $int_type_id = $stmt->insert_id; // Get the inserted intervention ID
        } else {
            echo json_encode(["status" => "error", "message" => "Error inserting intervention: " . $stmt->error]);
            exit;   
        }

        // Return success response
        echo json_encode(["status" => "success", "message" => "Intervention added successfully"]);

        // Close statement and connection
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid form submission."]);
    }
?>