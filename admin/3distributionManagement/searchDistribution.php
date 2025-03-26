<?php
include('../../conn.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['uid'])) {
    echo "<tr><td colspan='11' class='text-center'>User not logged in.</td></tr>";
    exit;
}

$user_id = $_SESSION['uid'];
$stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
$stationQuery->bind_param("i", $user_id);
$stationQuery->execute();
$stationQuery->bind_result($station_id);
$stationQuery->fetch();
$stationQuery->close();

// Get search query
$searchQuery = isset($_GET['q']) ? "%" . $_GET['q'] . "%" : "%";

// Fetch filtered data
$query = "
    SELECT 
        d.distribution_date, 
        CONCAT(b.fname, ' ', IFNULL(b.mname, ''), ' ', b.lname) AS beneficiary_name, 
        st.seed_name, 
        it.intervention_name, 
        b.province_name, 
        b.municipality_name, 
        b.barangay_name, 
        d.distribution_id,
        CONCAT(d.quantity, ' ', u.unit_name) AS quantity_with_unit,  
        IF(b.coop_id = 0, 'N/A', c.cooperative_name) AS cooperative_name
    FROM 
        tbl_distribution AS d
    INNER JOIN 
        tbl_beneficiary AS b ON d.beneficiary_id = b.beneficiary_id
    INNER JOIN 
        tbl_seed_type AS st ON d.seed_id = st.seed_id
    INNER JOIN 
        tbl_intervention_type AS it ON st.int_type_id = it.int_type_id
    INNER JOIN 
        tbl_intervention_inventory AS ii ON it.int_type_id = ii.int_type_id AND st.seed_id = ii.seed_id
    INNER JOIN 
        tbl_unit AS u ON ii.unit_id = u.unit_id
    LEFT JOIN 
        tbl_cooperative AS c ON b.coop_id = c.coop_id
    WHERE 
        d.station_id = ? 
        AND (b.fname LIKE ? OR b.lname LIKE ? OR b.province_name LIKE ? OR b.municipality_name LIKE ? 
             OR b.barangay_name LIKE ? OR c.cooperative_name LIKE ? OR st.seed_name LIKE ? OR it.intervention_name LIKE ?)
        AND d.archived_at IS NULL
    ORDER BY 
        d.distribution_date DESC;
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}
$stmt->bind_param("issssssss", $station_id, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery, $searchQuery);
$stmt->execute();
$result = $stmt->get_result();

// Output search results
if ($result->num_rows > 0) {
    while ($data = $result->fetch_assoc()) {
        $beneficiary_name = $data['beneficiary_name'];
        $seed_name = $data['seed_name'];
        $province = $data['province_name'];
        $municipality = $data['municipality_name'];
        $barangay = $data['barangay_name'];
        $intervention_name = $data['intervention_name'];
        $quantity_with_unit = $data['quantity_with_unit'];
        $cooperative_name = $data['cooperative_name'];
        $date = date("F j, Y", strtotime($data['distribution_date']));
        
        echo "<tr>
            <td>" . htmlspecialchars($date) . "</td>
            <td>" . htmlspecialchars($beneficiary_name) . "</td>
            <td>" . htmlspecialchars($province) . "</td>
            <td>" . htmlspecialchars($municipality) . "</td>
            <td>" . htmlspecialchars($barangay) . "</td>
            <td>" . htmlspecialchars($cooperative_name) . "</td>
            <td>" . htmlspecialchars($intervention_name) . "</td>
            <td>" . htmlspecialchars($seed_name) . "</td>
            <td>" . htmlspecialchars($quantity_with_unit) . "</td>
            <td>
                <div class='d-flex justify-content-center gap-2'>
                    <button type='button' class='btn btn-success rounded-0' data-bs-toggle='modal' data-bs-target='#updateDistributionModal'
                        data-distribution-id='" . htmlspecialchars($data['distribution_id']) . "'
                        data-quantity='" . htmlspecialchars($data['quantity'] ?? '') . "'
                        data-intervention-name='" . htmlspecialchars($intervention_name ?? '') . "'
                        data-seed-name='" . htmlspecialchars($seed_name ?? '') . "'
                        data-distribution-date='" . htmlspecialchars($data['distribution_date'] ?? '') . "'
                        data-quantity-left='" . (isset($data['quantity_left']) ? htmlspecialchars($data['quantity_left']) : '0') . "'>
                        <i class='fas fa-edit'></i> 
                    </button>

                    <button class='btn btn-danger rounded-0 archivedistribution-btn' 
                        data-distribution-id='" . htmlspecialchars($data['distribution_id']) . "'>
                        <i class='fas fa-trash-alt'></i> 
                    </button>
                </div>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='11' class='text-center'>No records found</td></tr>";
}
?>
