<?php

// Include your database connection
include '../../conn.php'; // Adjust the path as necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $action = $_POST['action'];

    // Determine the new status based on the action
    $newStatus = ($action === 'activate') ? 1 : 0;

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("UPDATE tbl_user SET status = ? WHERE username = ?");
    $stmt->bind_param("is", $newStatus, $username);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }

    $stmt->close();
    $conn->close();
}
?>