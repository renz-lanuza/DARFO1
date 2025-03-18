<?php
// Check if municipality name is provided
if (!isset($_GET['municipality'])) {
    die("Municipality name is missing.");
}

$municipality = trim($_GET['municipality']); // Sanitize input

// Database connection
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if needed
$dbname = "db_darfo1"; // Your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch beneficiaries and count their total interventions
$query = "
    SELECT 
        CONCAT(b.fname, ' ', IFNULL(b.mname, ''), ' ', b.lname) AS beneficiary_name, 
        b.beneficiary_type, 
        IF(b.coop_id = 0, 'N/A', c.cooperative_name) AS cooperative_name,
        COUNT(d.distribution_id) AS total_interventions
    FROM 
        tbl_distribution AS d
    INNER JOIN 
        tbl_beneficiary AS b ON d.beneficiary_id = b.beneficiary_id
    LEFT JOIN 
        tbl_cooperative AS c ON b.coop_id = c.coop_id
    WHERE 
        b.municipality_name = ?
    GROUP BY 
        b.beneficiary_id
    ORDER BY 
        total_interventions DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $municipality);
$stmt->execute();
$beneficiary_result = $stmt->get_result();

// Check if query was successful
if ($beneficiary_result === false) {
    die("Query Error: " . $conn->error);
}

// Fetch results
$beneficiaries = [];
while ($row = $beneficiary_result->fetch_assoc()) {
    $beneficiaries[] = $row;
}

$stmt->close();
$conn->close();
?>

<!-- Display municipality name -->
<h5 style="color: black;">Total Interventions per Beneficiary in: <strong><?= htmlspecialchars($municipality) ?></strong></h5>

<!-- Display beneficiaries in a table -->
<?php if (!empty($beneficiaries)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered text-center align-middle">
            <thead class="thead-dark" style="background-color: #0D7C66; color: white;">
                <tr>
                    <th>#</th>
                    <th>Beneficiary Name</th>
                    <th>Type of Beneficiary</th>
                    <th>Total Interventions</th>
                    <th>Cooperative Name</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($beneficiaries as $index => $beneficiary): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($beneficiary['beneficiary_name']) ?></td>
                        <td><?= htmlspecialchars($beneficiary['beneficiary_type']) ?></td>
                        <td><?= htmlspecialchars($beneficiary['total_interventions']) ?></td>
                        <td><?= htmlspecialchars($beneficiary['cooperative_name']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class="text-muted text-center">No beneficiaries found for this municipality.</p>
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
