<?php
session_start(); // Start the session
include("../../conn.php");

$filter = isset($_GET['filter']) ? $_GET['filter'] : "all";
$station_id = intval($_SESSION['station_id']); // Get logged-in user's station

$query = "SELECT b.beneficiary_id,
                b.fname,
                b.mname,
                b.lname,
                b.province_name,
                b.municipality_name,
                b.barangay_name,
                b.beneficiary_category,
                COALESCE(c.cooperative_name, 'N/A') AS cooperative_name
          FROM tbl_beneficiary b
          LEFT JOIN tbl_cooperative c ON b.coop_id = c.coop_id
          WHERE b.station_id = ?";

if ($filter !== "all") {
    $query .= " AND b.beneficiary_type = ?";
}

$stmt = mysqli_prepare($conn, $query);
if ($filter === "all") {
    mysqli_stmt_bind_param($stmt, "i", $station_id);
} else {
    mysqli_stmt_bind_param($stmt, "is", $station_id, $filter);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $index = 1;
    while ($beneficiary = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>{$index}</td>";
        echo "<td>" . htmlspecialchars($beneficiary['fname'] . ' ' . $beneficiary['mname'] . ' ' . $beneficiary['lname']) . "</td>";
        echo "<td>" . htmlspecialchars($beneficiary['barangay_name']) . "</td>";
        echo "<td>" . htmlspecialchars($beneficiary['municipality_name']) . "</td>";
        echo "<td>" . htmlspecialchars($beneficiary['province_name']) . "</td>";
        echo "<td>" . ucfirst($beneficiary['beneficiary_category']) . "</td>";
        echo "<td>" . htmlspecialchars($beneficiary['cooperative_name']) . "</td>";
        echo "<td><button class='btn btn-primary btn-sm view-interventions-btn' data-beneficiary-id='{$beneficiary['beneficiary_id']}'><i class='fas fa-eye'></i> View</button></td>";
        echo "</tr>";
        $index++;
    }
} else {
    echo "<tr><td colspan='8' class='text-center'>No beneficiaries found.</td></tr>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
