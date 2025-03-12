<?php
    // Assuming you have a connection to your database
    include('../../conn.php'); // Include your DB connection file
    session_start(); // Start the session to access session variables

    // Check if the user is logged in
    if (!isset($_SESSION['uid'])) {
        die(json_encode(["error" => "User  ID not found in session."]));
    }

    $uid = $_SESSION['uid']; // Get the uid from the session

    // Retrieve the station_id based on the logged-in user's uid
    $stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
    $stationQuery->bind_param("i", $uid);
    $stationQuery->execute();
    $stationQuery->bind_result($stationId);
    $stationQuery->fetch();
    $stationQuery->close();

    // Check if station_id was found
    if (empty($stationId)) {
        die(json_encode(["error" => "No station found for the user."]));
    }

    // Fetch the selected intervention type ID from the GET request
    $int_type_id = $_GET['int_type_id'];

    // Get all existing seed_ids from tbl_intervention_inventory
    $query = "SELECT DISTINCT seed_id FROM tbl_intervention_inventory WHERE int_type_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $int_type_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $existing_seed_ids = [];
    while ($row = $result->fetch_assoc()) {
        $existing_seed_ids[] = $row['seed_id']; // Store the existing seed_ids
    }

    // Now fetch the seedlings from your seedling table, filtered by station_id
    $query = "SELECT * FROM tbl_seed_type WHERE int_type_id = ? AND station_id = ?"; // Assuming tbl_seed_type has a station_id column
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $int_type_id, $stationId);
    $stmt->execute();
    $seedlings_result = $stmt->get_result();

    $seedlings = [];
    while ($row = $seedlings_result->fetch_assoc()) {
        // Add only those seedlings which are not in the existing_seed_ids
        if (!in_array($row['seed_id'], $existing_seed_ids)) {
            $seedlings[] = $row; // Add to the seedlings array
        }
    }

    // Return the seedlings array as a JSON response
    echo json_encode($seedlings);

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
?>