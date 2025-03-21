<?php
session_start(); // Ensure session is started before using session variables
include('../../conn.php');

if (!isset($_SESSION['uid'])) {
    echo "<tr><td colspan='2' class='text-center text-danger'>User not logged in.</td></tr>";
    return;
}

$user_id = $_SESSION['uid'];

// Retrieve station ID
$stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
$stationQuery->bind_param("i", $user_id);
$stationQuery->execute();
$stationQuery->bind_result($station_id);
$stationQuery->fetch();
$stationQuery->close();

if (empty($station_id)) {
    echo "<tr><td colspan='2' class='text-center'>No station found for the user.</td></tr>";
    return; // Use return to continue script execution properly
}

// Get search query
$searchQuery = isset($_GET['q']) ? "%" . $_GET['q'] . "%" : "%";

// Fetch matching records
$sql = "SELECT int_type_id, intervention_name FROM tbl_intervention_type 
        WHERE station_id = ? 
        AND intervention_name LIKE ? 
        AND archived_at IS NULL
        ORDER BY int_type_id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $station_id, $searchQuery);
$stmt->execute();
$result = $stmt->get_result();

// Ensure table structure remains intact even if no results are found
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['intervention_name']) . "</td>
                <td>
                    <a href='#' class='btn' style='background-color: #DCFFB7; color: black;' 
                        data-toggle='modal' data-target='#updateInterventionTypeModal'
                        data-user-id='" . $row['int_type_id'] . "'>Update</a>
                    
                    <a href='#' class='btn btn-warning archive-int-type-btn'
                        data-int-type-id='" . htmlspecialchars($row['int_type_id']) . "'>Archive</a>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='2' class='text-center'>No records found</td></tr>";
}

// Close statements
$stmt->close();
$conn->close();
?>
