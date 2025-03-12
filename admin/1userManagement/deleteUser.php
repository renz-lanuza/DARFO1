<?php
include('../../conn.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['userId'])) {
    $userId = $_POST['userId'];

    // Perform delete query
    $query = "DELETE FROM tbl_user WHERE uid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete user."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>
