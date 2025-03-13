<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_darfo1";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if (isset($_POST["cooperative_name"])) {
        $cooperativeName = trim($_POST["cooperative_name"]);

        // Query to check if the cooperative name exists and is not archived
        $query = "SELECT COUNT(*) FROM tbl_cooperative WHERE cooperative_name = ? AND archived_at IS NULL";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $cooperativeName);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        echo ($count > 0) ? "exists" : "available";
    }
?>
