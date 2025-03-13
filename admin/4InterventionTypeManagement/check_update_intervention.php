<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_darfo1";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (isset($_POST["interventionName"]) && isset($_POST["station_id"])) {
        $interventionName = trim($_POST["interventionName"]);
        $stationId = intval($_POST["station_id"]);

        // Query to check if the intervention name exists within the same station
        $query = "SELECT COUNT(*) FROM tbl_intervention_type WHERE intervention_name = ? AND station_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $interventionName, $stationId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        echo ($count > 0) ? "exists" : "available";
    }

    $conn->close();
?>
