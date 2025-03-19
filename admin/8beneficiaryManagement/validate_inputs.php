<?php
require "../../conn.php"; // Ensure this file correctly connects to your database

header("Content-Type: application/json");

$response = ["exists" => false, "message" => "", "error" => ""];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["rsbsa_no"])) {
        $rsbsa_no = trim($_POST["rsbsa_no"]);
        $rsbsa_no = str_replace("-", "", $rsbsa_no); // Remove dashes before checking

        // Ensure database connection is established
        if (!$conn) {
            $response["error"] = "Database connection failed.";
            echo json_encode($response);
            exit;
        }

        $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_beneficiary WHERE REPLACE(rsbsa_no, '-', '') = ?");
        if ($stmt) {
            $stmt->bind_param("s", $rsbsa_no);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                $response["exists"] = true;
                $response["message"] = "RSBSA Number already existing";
            }
        } else {
            $response["error"] = "Database query failed.";
        }
    }

    if (isset($_POST["contact_no"])) {
        $contact_no = trim($_POST["contact_no"]);

        $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_beneficiary WHERE contact_no = ?");
        if ($stmt) {
            $stmt->bind_param("s", $contact_no);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            if ($count > 0) {
                $response["exists"] = true;
                $response["message"] = "Contact Number already existing";
            }
        } else {
            $response["error"] = "Database query failed.";
        }
    }

    echo json_encode($response);
    exit;
}
?>
