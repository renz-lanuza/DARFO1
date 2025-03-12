<?php
include('../../conn.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['userId'])) {
    $userId = $_POST['userId'];

    // Update the status to 3 (archived) instead of deleting
    $query = "UPDATE tbl_user SET status = 3 WHERE uid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to archive user."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>
