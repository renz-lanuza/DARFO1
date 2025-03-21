<?php
include('../../conn.php');

if (isset($_GET['int_type_id'])) {
    $inttypeId = $_GET['int_type_id'];
    $response = ["status" => "error", "data" => []];

    $sql = "SELECT seed_name FROM tbl_seed_type WHERE int_type_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $inttypeId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $classifications = [];
        while ($row = $result->fetch_assoc()) {
            $classifications[] = strtolower($row['seed_name']);
        }
        $response["status"] = "success";
        $response["data"] = $classifications;
    }

    echo json_encode($response);
    $stmt->close();
    $conn->close();
}
?>
