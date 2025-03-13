<?php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "db_darfo1";

   $conn = new mysqli($servername, $username, $password, $dbname);

    if (isset($_POST["interventionName"])) {
        $interventionName = trim($_POST["interventionName"]);

        // Query to check if the intervention name exists
        $query = "SELECT COUNT(*) FROM tbl_intervention_type WHERE intervention_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $interventionName);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        echo ($count > 0) ? "exists" : "available";
    }
?>
