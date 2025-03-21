<?php
include('../../conn.php');
session_start();

if (!isset($_SESSION['uid'])) {
    echo "<tr><td colspan='3' class='text-center'>User not logged in.</td></tr>";
    exit;
}

$uid = $_SESSION['uid'];

// Fetch station_id for the logged-in user
$sqlStation = "SELECT station_id FROM tbl_user WHERE uid = ?";
$stmtStation = $conn->prepare($sqlStation);
$stmtStation->bind_param("i", $uid);
$stmtStation->execute();
$resultStation = $stmtStation->get_result();

if ($resultStation->num_rows > 0) {
    $rowStation = $resultStation->fetch_assoc();
    $stationId = $rowStation['station_id'];

    $search = isset($_GET['search']) ? "%" . trim($_GET['search']) . "%" : "%";

    // Fetch matching data
    $sql = "SELECT st.seed_name, st.seed_id, it.intervention_name
            FROM tbl_seed_type st
            INNER JOIN tbl_intervention_type it ON st.int_type_id = it.int_type_id
            WHERE st.station_id = ? AND (st.seed_name LIKE ? OR it.intervention_name LIKE ?)
            ORDER BY st.seed_id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $stationId, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['intervention_name']) . "</td>
                    <td>" . htmlspecialchars($row['seed_name']) . "</td>
                    <td>
                        <button class='btn btn-success btn-sm edit-btn' 
                            data-id='" . htmlspecialchars($row['seed_id']) . "'
                            data-seed-name='" . htmlspecialchars($row['seed_name']) . "'
                            data-intervention-name='" . htmlspecialchars($row['intervention_name']) . "'
                            data-bs-toggle='modal' 
                            data-bs-target='#editSeedlingModal'>
                            Update
                        </button>

                        <button class='btn btn-warning btn-sm archive-btn' 
                            data-id='" . htmlspecialchars($row['seed_id']) . "'>
                            Archive
                        </button>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3' class='text-center'>No matching seed types found.</td></tr>";
    }

    $stmt->close();
} else {
    echo "<tr><td colspan='3' class='text-center'>No station found for this user.</td></tr>";
}

$stmtStation->close();
$conn->close();
?>
