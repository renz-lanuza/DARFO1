<?php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "db_darfo1";

   $conn = new mysqli($servername, $username, $password, $dbname);

    if (isset($_POST["interventionName"]) && isset($_POST["int_type_id"])) {
        $interventionName = trim($_POST["interventionName"]);
        $intTypeId = intval($_POST["int_type_id"]);

        // Query to check if the intervention name exists, excluding the current intervention
        $query = "SELECT COUNT(*) FROM tbl_intervention_type WHERE intervention_name = ? AND int_type_id != ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $interventionName, $intTypeId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        echo ($count > 0) ? "exists" : "available";
    }
?>