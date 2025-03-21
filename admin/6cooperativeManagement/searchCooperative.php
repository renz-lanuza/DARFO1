<?php
include('../../conn.php');
session_start();

// Ensure user is logged in
if (!isset($_SESSION['uid'])) {
    echo "<tr><td colspan='5' class='text-center'>User not logged in.</td></tr>";
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
    echo "<tr><td colspan='5' class='text-center'>No station found for the user.</td></tr>";
    exit;
}

// Get search query and sanitize input
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : "";

// Set pagination values
$entries_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $entries_per_page;

// SQL query to fetch cooperatives based on search
$sql = "SELECT coop_id, cooperative_name, province_name, municipality_name, barangay_name 
        FROM tbl_cooperative 
        WHERE station_id = ? 
        AND archived_at IS NULL  -- Exclude archived records
        AND (cooperative_name LIKE ? OR province_name LIKE ? OR municipality_name LIKE ? OR barangay_name LIKE ?)
        LIMIT ?, ?";

$stmt = $conn->prepare($sql);
$searchTerm = "%" . $searchQuery . "%";
$stmt->bind_param("issssii", $station_id, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $offset, $entries_per_page);
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
                    <button class='btn btn-success btn-sm update-btn' 
                        data-id='" . htmlspecialchars($row['coop_id'], ENT_QUOTES, 'UTF-8') . "'
                        data-name='" . htmlspecialchars($row['cooperative_name'], ENT_QUOTES, 'UTF-8') . "'
                        data-province='" . htmlspecialchars($row['province_name'], ENT_QUOTES, 'UTF-8') . "'
                        data-municipality='" . htmlspecialchars($row['municipality_name'], ENT_QUOTES, 'UTF-8') . "'
                        data-barangay='" . htmlspecialchars($row['barangay_name'], ENT_QUOTES, 'UTF-8') . "'
                        data-bs-toggle='modal' data-bs-target='#updateCooperativeModal'>
                        Update
                    </button>

                    <button class='btn btn-warning btn-sm archivecoop-btn' 
                        data-id='" . htmlspecialchars($row['coop_id'], ENT_QUOTES, 'UTF-8') . "'>
                        Archive
                    </button>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center'>No cooperatives found.</td></tr>";
}

$stmt->close();
$conn->close();
?>
