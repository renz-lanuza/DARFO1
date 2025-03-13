<?php
include '../../conn.php'; // Adjust this to your actual database connection file

if (isset($_POST['username'])) {
    $username = trim($_POST['username']);

    $query = "SELECT COUNT(*) AS count FROM tbl_user WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    echo json_encode(["exists" => $result['count'] > 0]);
}
?>
