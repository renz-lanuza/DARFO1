<?php
include('../../conn.php');
session_start();

// Ensure user is logged in
if (!isset($_SESSION['uid'])) {
    echo "<tr><td colspan='5' class='text-center text-danger'>User not logged in.</td></tr>";
    exit;
}

$user_id = $_SESSION['uid'];

// Retrieve the user's station_id
$stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
$stationQuery->bind_param("i", $user_id);
$stationQuery->execute();
$stationQuery->bind_result($station_id);
$stationQuery->fetch();
$stationQuery->close();

// Check if station_id was found
if (empty($station_id)) {
    echo "<tr><td colspan='5' class='text-center text-danger'>No station assigned to this user.</td></tr>";
    exit;
}

// Get search query
$searchQuery = isset($_GET['search']) ? "%" . trim($_GET['search']) . "%" : "%";

// Pagination settings
$entries_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $entries_per_page;

// SQL query to fetch cooperatives based on search
$sql = "SELECT coop_id, cooperative_name, province_name, municipality_name, barangay_name 
        FROM tbl_cooperative 
        WHERE station_id = ? 
        AND archived_at IS NULL  -- Exclude archived records
        AND (cooperative_name LIKE ? OR province_name LIKE ? OR municipality_name LIKE ? OR barangay_name LIKE ?)
        ORDER BY cooperative_name ASC
        LIMIT ?, ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("issssii", $station_id, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $offset, $entries_per_page);
$stmt->execute();
$result = $stmt->get_result();

// Check if records exist
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['cooperative_name']) . "</td>
                <td>" . htmlspecialchars($row['province_name']) . "</td>
                <td>" . htmlspecialchars($row['municipality_name']) . "</td>
                <td>" . htmlspecialchars($row['barangay_name']) . "</td>
                <td>
                    <div class='btn-group' role='group'>
                        <button class='btn btn-success btn-sm border-0 update-btn' 
                            data-id='" . htmlspecialchars($row['coop_id'], ENT_QUOTES, 'UTF-8') . "'
                            data-name='" . htmlspecialchars($row['cooperative_name'], ENT_QUOTES, 'UTF-8') . "'
                            data-province='" . htmlspecialchars($row['province_name'], ENT_QUOTES, 'UTF-8') . "'
                            data-municipality='" . htmlspecialchars($row['municipality_name'], ENT_QUOTES, 'UTF-8') . "'
                            data-barangay='" . htmlspecialchars($row['barangay_name'], ENT_QUOTES, 'UTF-8') . "'
                            data-bs-toggle='modal' data-bs-target='#updateCooperativeModal'>
                            <i class='fas fa-edit'></i> 
                        </button>

                        <button class='btn btn-danger btn-sm border-0 archivecoop-btn' 
                            data-id='" . htmlspecialchars($row['coop_id'], ENT_QUOTES, 'UTF-8') . "'>
                            <i class='fas fa-trash-alt'></i> 
                        </button>
                    </div>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center text-warning'>No cooperatives found.</td></tr>";
}

$stmt->close();
$conn->close();
?>
