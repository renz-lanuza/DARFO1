<?php
require '../../conn.php'; // Adjust the path as needed

if (isset($_GET['int_type_id']) && isset($_GET['seed_id'])) {
    $int_type_id = $_GET['int_type_id'];
    $seed_id = $_GET['seed_id'];

    $conn = new mysqli("localhost", "root", "", "db_darfo1");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT quantity_left FROM tbl_intervention_inventory WHERE int_type_id = ? AND seed_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $int_type_id, $seed_id);
    $stmt->execute();
    $stmt->bind_result($quantity_left);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    echo $quantity_left ? $quantity_left : '0';
}
?>
