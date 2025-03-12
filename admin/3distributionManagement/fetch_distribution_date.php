<?php
header('Content-Type: application/json');
require '../../conn.php'; // Ensure this file contains your database connection

if (isset($_GET['distribution_id'])) {
    $distribution_id = intval($_GET['distribution_id']);

    $sql = "SELECT distribution_date FROM tbl_distribution WHERE distribution_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $distribution_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            "success" => true,
            "distribution_date" => $row['distribution_date']
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "No record found"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
