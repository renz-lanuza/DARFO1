<?php
include('../../conn.php');

$type = $_GET['type'] ?? '';
$code = $_GET['code'] ?? '';

if ($type === 'provinces') {
    $query = "SELECT province_code, province_name FROM provinces";
} elseif ($type === 'municipalities' && $code) {
    $query = "SELECT municipality_code, municipality_name FROM municipalities WHERE province_code = ?";
} elseif ($type === 'barangays' && $code) {
    $query = "SELECT barangay_code, barangay_name FROM barangays WHERE municipality_code = ?";
} else {
    echo json_encode(["error" => "Invalid request"]);
    exit;
}

$stmt = $conn->prepare($query);

if ($code) {
    $stmt->bind_param("s", $code);
}

$stmt->execute();
$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$stmt->close();
$conn->close();
?>
