<?php
session_start();
include '../../conn.php';
header('Content-Type: application/json');

if (!isset($_POST['userId'], $_POST['fname'], $_POST['lname'], $_POST['username'], $_POST['ulevel'], $_POST['station'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$userId = intval($_POST['userId']);
$firstName = trim($_POST['fname']);
$middleName = trim($_POST['mname'] ?? '');
$lastName = trim($_POST['lname']);
$username = trim($_POST['username']);
$userLevel = trim($_POST['ulevel']);
$station = intval($_POST['station']);

$stmt = $conn->prepare("UPDATE tbl_user SET fname=?, mname=?, lname=?, username=?, ulevel=?, station_id=? WHERE uid=?");
$stmt->bind_param("ssssssi", $firstName, $middleName, $lastName, $username, $userLevel, $station, $userId);

echo json_encode(['success' => $stmt->execute(), 'message' => $stmt->execute() ? 'User updated successfully' : 'Update failed']);

$stmt->close();
$conn->close();
?>
