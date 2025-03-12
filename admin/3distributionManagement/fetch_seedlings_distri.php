<?php
    // Assuming you have a connection to your database
    include('../../conn.php'); // Include your DB connection file

    // Fetch the selected intervention type ID from the GET request
    $int_type_id = $_GET['int_type_id'];

    // Fetch all seedlings from the seedling table for the selected intervention type
    $query = "SELECT * FROM tbl_seed_type WHERE int_type_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $int_type_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $seedlings = [];
    while ($row = $result->fetch_assoc()) {
        $seedlings[] = $row; // Add all seedlings to the array
    }

    // Return the seedlings array as a JSON response
    echo json_encode($seedlings);
?>