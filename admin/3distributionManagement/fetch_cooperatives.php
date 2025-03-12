<?php
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "db_darfo1");

if (!$conn) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// Modify the query to exclude archived records
$query = "SELECT coop_id, cooperative_name FROM tbl_cooperative WHERE archived_at IS NULL ORDER BY cooperative_name";

$result = mysqli_query($conn, $query);
$cooperatives = [];

while ($row = mysqli_fetch_assoc($result)) {
    $cooperatives[] = [
        "id" => $row["coop_id"],
        "name" => $row["cooperative_name"]
    ];
}

echo json_encode($cooperatives);
mysqli_close($conn);
?>
