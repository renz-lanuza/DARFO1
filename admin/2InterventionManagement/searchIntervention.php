<?php
include('../../conn.php');
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['uid'])) {
    echo "<tr><td colspan='7' class='text-center'>User not logged in.</td></tr>";
    exit;
}

$user_id = $_SESSION['uid'];

// Retrieve the user's station ID
$stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
$stationQuery->bind_param("i", $user_id);
$stationQuery->execute();
$stationQuery->store_result();

if ($stationQuery->num_rows > 0) {
    $stationQuery->bind_result($station_name);
    $stationQuery->fetch();
} else {
    echo "<tr><td colspan='7' class='text-center'>No station assigned to this user.</td></tr>";
    exit;
}
$stationQuery->close();

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : "";

// SQL query with search condition
$sql = "SELECT 
            tbl_intervention_inventory.intervention_id,
            tbl_intervention_inventory.int_type_id, 
            tbl_intervention_inventory.description, 
            tbl_intervention_inventory.quantity, 
            tbl_intervention_inventory.quantity_left,
            tbl_intervention_inventory.seed_id,
            tbl_unit.unit_name AS unit_name,
            tbl_intervention_type.intervention_name AS intervention_name,
            tbl_seed_type.seed_name AS seedling_name
        FROM tbl_intervention_inventory
        INNER JOIN tbl_intervention_type 
            ON tbl_intervention_inventory.int_type_id = tbl_intervention_type.int_type_id
        LEFT JOIN tbl_seed_type 
            ON tbl_intervention_inventory.seed_id = tbl_seed_type.seed_id
        INNER JOIN tbl_unit 
            ON tbl_intervention_inventory.unit_id = tbl_unit.unit_id
        WHERE tbl_intervention_inventory.station_id = ? 
            AND (tbl_intervention_type.intervention_name LIKE ? OR tbl_seed_type.seed_name LIKE ?)
            AND tbl_intervention_inventory.archived_at IS NULL  -- Exclude archived records
        LIMIT 10"; // Limits search results

$stmt = $conn->prepare($sql);
$searchTerm = "%" . $searchQuery . "%"; // Wildcard search
$stmt->bind_param("iss", $station_name, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['intervention_name']) . "</td>
                <td>" . htmlspecialchars($row['seedling_name']) . "</td>
                <td>" . htmlspecialchars($row['description']) . "</td>
                <td>" . htmlspecialchars($row['quantity']) . "</td>
                <td>" . htmlspecialchars($row['quantity_left']) . "</td>
                <td>" . htmlspecialchars($row['unit_name']) . "</td>
                <td>
                    <div class='d-flex justify-content-center gap-2'>
                        <a href='#' class='btn btn-success rounded-0' 
                            data-bs-toggle='modal' data-bs-target='#updateInterventionModal'
                            data-intervention-id='" . htmlspecialchars($row['intervention_id']) . "'>
                            <i class='fas fa-edit'></i> 
                        </a>
                        <button class='btn btn-danger rounded-0 archiveintervention-btn'
                            data-intervention-id='" . htmlspecialchars($row['intervention_id']) . "'>
                            <i class='fas fa-trash-alt'></i> 
                        </button>
                    </div>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7' class='text-center'>No records found</td></tr>";
}

$stmt->close();
$conn->close();
?>
