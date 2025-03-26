<?php
include('../../conn.php');
session_start();

if (!isset($_SESSION['uid'])) {
    echo "<tr><td colspan='3' class='text-center text-danger'>User not logged in.</td></tr>";
    exit;
}

$uid = $_SESSION['uid'];

// Fetch station_id for the logged-in user
$sqlStation = "SELECT station_id FROM tbl_user WHERE uid = ?";
$stmtStation = $conn->prepare($sqlStation);
$stmtStation->bind_param("i", $uid);
$stmtStation->execute();
$stmtStation->bind_result($stationId);
$stmtStation->fetch();
$stmtStation->close();

if (empty($stationId)) {
    echo "<tr><td colspan='3' class='text-center text-danger'>No station assigned to this user.</td></tr>";
    exit;
}

// Secure and sanitize search input
$search = isset($_GET['search']) ? "%" . trim($_GET['search']) . "%" : "%";

// Fetch matching data for the logged-in user's station, excluding archived records
$sql = "SELECT st.seed_name, st.seed_id, it.intervention_name
        FROM tbl_seed_type st
        INNER JOIN tbl_intervention_type it ON st.int_type_id = it.int_type_id
        WHERE st.station_id = ? 
        AND (st.seed_name LIKE ? OR it.intervention_name LIKE ?)
        AND st.archived_at IS NULL  -- 🔹 Exclude archived records
        ORDER BY st.seed_id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $stationId, $search, $search);
$stmt->execute();
$result = $stmt->get_result();

// Display table rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['intervention_name']) . "</td>
                <td>" . htmlspecialchars($row['seed_name']) . "</td>
                <td>
                    <div class='btn-group' role='group'>
                        <button class='btn btn-success btn-sm edit-btn' style='border-radius: 0;'
                            data-id='" . htmlspecialchars($row['seed_id'], ENT_QUOTES, 'UTF-8') . "'
                            data-seed-name='" . htmlspecialchars($row['seed_name'], ENT_QUOTES, 'UTF-8') . "'
                            data-intervention-name='" . htmlspecialchars($row['intervention_name'], ENT_QUOTES, 'UTF-8') . "'
                            data-bs-toggle='modal' 
                            data-bs-target='#editSeedlingModal'>
                            <i class='fas fa-edit'></i> 
                        </button>

                        <button class='btn btn-danger btn-sm archive-btn' style='border-radius: 0;'
                            data-id='" . htmlspecialchars($row['seed_id'], ENT_QUOTES, 'UTF-8') . "'>
                            <i class='fas fa-trash-alt'></i> 
                        </button>
                    </div>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='3' class='text-center text-warning'>No matching seed types found.</td></tr>";
}

$stmt->close();
$conn->close();
?>
