<?php
// Check if beneficiary_id is provided
if (!isset($_GET['beneficiary_id'])) {
    die("Beneficiary ID is missing.");
}

$beneficiary_id = intval($_GET['beneficiary_id']); // Sanitize the input

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_darfo1";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch beneficiary details
$beneficiary_query = "SELECT fname, mname, lname FROM tbl_beneficiary WHERE beneficiary_id = ?";
$stmt = $conn->prepare($beneficiary_query);
$stmt->bind_param("i", $beneficiary_id);
$stmt->execute();
$beneficiary_result = $stmt->get_result();

// Check if query was successful
if ($beneficiary_result === false) {
    die("Query Error: " . $conn->error);
}

// Check if beneficiary exists
if ($beneficiary_result->num_rows === 0) {
    die("Beneficiary not found.");
}

$beneficiary = $beneficiary_result->fetch_assoc();
$beneficiary_name = htmlspecialchars(trim($beneficiary['fname'] . ' ' . $beneficiary['mname'] . ' ' . $beneficiary['lname']));

$stmt->close();

// Fetch interventions received by the beneficiary
$query = "
    SELECT 
        d.distribution_date, 
        CONCAT(b.fname, ' ', IFNULL(b.mname, ''), ' ', b.lname) AS beneficiary_name, 
        st.seed_name, 
        it.intervention_name, 
        b.beneficiary_type, 
        b.province_name, 
        b.municipality_name, 
        b.barangay_name, 
        d.quantity, 
        IF(b.coop_id = 0, 'N/A', c.cooperative_name) AS cooperative_name 
    FROM 
        tbl_distribution AS d
    INNER JOIN 
        tbl_beneficiary AS b ON d.beneficiary_id = b.beneficiary_id
    INNER JOIN 
        tbl_seed_type AS st ON d.seed_id = st.seed_id
    INNER JOIN 
        tbl_intervention_type AS it ON st.int_type_id = it.int_type_id
    LEFT JOIN 
        tbl_cooperative AS c ON b.coop_id = c.coop_id
    WHERE 
        d.beneficiary_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $beneficiary_id);
$stmt->execute();
$interventions_result = $stmt->get_result();

// Check if query was successful
if ($interventions_result === false) {
    die("Query Error: " . $conn->error);
}

$interventions_data = [];
while ($row = $interventions_result->fetch_assoc()) {
    $interventions_data[] = $row;
}

$stmt->close();
$conn->close();
?>

<!-- Display beneficiary name -->
<h5 style="color: black;">Interventions Received by: <strong><?= $beneficiary_name ?></strong></h5>

<!-- Display interventions in a styled table -->
<?php if (!empty($interventions_data)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered text-center align-middle">
            <thead class="thead-dark" style="background-color: #0D7C66; color: white;">
                <tr>
                    <th>#</th>
                    <th>Distribution Date</th>
                    <th>Seed Name</th>
                    <th>Intervention Name</th>
                    <th>Type of Beneficiary</th>
                    <th>Province</th>
                    <th>Municipality</th>
                    <th>Barangay</th>
                    <th>Quantity</th>
                    <th>Cooperative Name</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($interventions_data as $index => $intervention): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($intervention['distribution_date']) ?></td>
                        <td><?= htmlspecialchars($intervention['seed_name']) ?></td>
                        <td><?= htmlspecialchars($intervention['intervention_name']) ?></td>
                        <td><?= htmlspecialchars($intervention['beneficiary_type']) ?></td>
                        <td><?= htmlspecialchars($intervention['province_name']) ?></td>
                        <td><?= htmlspecialchars($intervention['municipality_name']) ?></td>
                        <td><?= htmlspecialchars($intervention['barangay_name']) ?></td>
                        <td><?= htmlspecialchars($intervention['quantity']) ?></td>
                        <td><?= htmlspecialchars($intervention['cooperative_name']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class="text-muted text-center">No interventions found for this beneficiary.</p>
<?php endif; ?>

<!-- Custom Table Styles -->
<style>
    .table {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }
    .thead-dark th {
        background-color: #0D7C66 !important;
        color: white;
    }
    tbody tr:hover {
        background-color: rgba(13, 124, 102, 0.1);
    }
</style>
