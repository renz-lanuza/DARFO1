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
                    <div class='d-flex justify-content-center'>
                        <a href='#' class='btn btn-success update-btn' style='border-radius: 0; padding: 5px 10px;'
                            data-toggle='modal' data-target='#updateUnitModal'
                            data-unit-id='" . htmlspecialchars($row['unit_id']) . "'
                            data-unit-name='" . htmlspecialchars($row['unit_name']) . "'>
                            <i class='fa fa-edit'></i> 
                        </a>

                        <a href='#' class='btn btn-danger archive-unit-btn' style='border-radius: 0; padding: 5px 10px;'
                            data-unit-id='" . htmlspecialchars($row['unit_id']) . "'>
                            <i class='fas fa-trash-alt'></i> 
                        </a>
                    </div>
                </td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='2' class='text-center'>No records found</td></tr>";
}

$stmt->close();
$conn->close();
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
