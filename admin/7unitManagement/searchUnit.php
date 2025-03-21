<?php
include('../../conn.php');

session_start();
if (!isset($_SESSION['uid'])) {
    echo "<tr><td colspan='2' class='text-center'>User not logged in.</td></tr>";
    exit;
}

$user_id = $_SESSION['uid'];
$stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
$stationQuery->bind_param("i", $user_id);
$stationQuery->execute();
$stationQuery->bind_result($station_id);
$stationQuery->fetch();
$stationQuery->close();

if (empty($station_id)) {
    echo "<tr><td colspan='2' class='text-center'>No station found for the user.</td></tr>";
    exit;
}

$search = isset($_GET['search']) ? "%" . trim($_GET['search']) . "%" : "%";
$query = "SELECT unit_id, unit_name FROM tbl_unit 
          WHERE station_id = ? AND unit_name LIKE ? 
          ORDER BY unit_id DESC LIMIT 10";

$stmt = $conn->prepare($query);
$stmt->bind_param("is", $station_id, $search);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['unit_name']) . "</td>
                <td>
                    <a href='#' class='btn update-btn' style='background-color: #DCFFB7; color: black;'
                        data-toggle='modal' data-target='#updateUnitModal'
                        data-unit-id='" . htmlspecialchars($row['unit_id']) . "'
                        data-unit-name='" . htmlspecialchars($row['unit_name']) . "'>Update</a>

                    <a href='#' class='btn btn-warning archive-unit-btn'
                        data-unit-id='" . htmlspecialchars($row['unit_id']) . "'>Archive</a>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='2' class='text-center'>No records found</td></tr>";
}

$stmt->close();
$conn->close();
?>
