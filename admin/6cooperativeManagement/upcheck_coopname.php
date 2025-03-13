<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_darfo1";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if (isset($_POST["cooperative_name"], $_POST["coop_id"])) {
        $cooperativeName = trim($_POST["cooperative_name"]);
        $coopId = intval($_POST["coop_id"]); // Convert to integer for safety

        // Query to check if the cooperative name exists, excluding the current record
        $query = "SELECT COUNT(*) FROM tbl_cooperative WHERE cooperative_name = ? AND archived_at IS NULL AND coop_id != ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $cooperativeName, $coopId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        echo ($count > 0) ? "exists" : "available";
    }
?>