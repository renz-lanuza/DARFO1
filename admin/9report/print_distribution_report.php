<?php
    require('fpdf186/fpdf.php');
    require('fpdi/src/autoload.php');  
    include('../../conn.php');
    session_start();

    use setasign\Fpdi\Fpdi;

    // Check if user is logged in
    if (!isset($_SESSION['uid'])) {
        die("User not logged in.");
    }

    $user_id = $_SESSION['uid'];
    $stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
    $stationQuery->bind_param("i", $user_id);
    $stationQuery->execute();
    $stationQuery->bind_result($station_id);
    $stationQuery->fetch();
    $stationQuery->close();

    // Fetch distinct provinces
    $provinceQuery = "SELECT DISTINCT b.province_name 
                    FROM tbl_distribution AS d
                    INNER JOIN tbl_beneficiary AS b ON d.beneficiary_id = b.beneficiary_id
                    WHERE d.station_id = ? 
                    AND (d.archived_at IS NULL OR d.archived_at = '')
                    ORDER BY b.province_name ASC";

    $stmt = $conn->prepare($provinceQuery);
    $stmt->bind_param("i", $station_id);
    $stmt->execute();
    $provinceResult = $stmt->get_result();

    // Create PDF with Template
    $pdf = new FPDI();
    $pdf->SetTitle('Distribution Report');

    // Set Template
    $templatePath = 'template.pdf';  
    $pdf->setSourceFile($templatePath);
    $tplIdx = $pdf->importPage(1);

    while ($provinceRow = $provinceResult->fetch_assoc()) {
        $province = $provinceRow['province_name'];

        // Start a new page for each province
        $pdf->AddPage();
        $pdf->useTemplate($tplIdx, 0, 0, 210);
        $pdf->SetY(40);
        // Main Report Header
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, "Distribution Report", 0, 1, 'C');
        $pdf->Ln(5);
        // Province Title
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 0, "Province: $province", 0, 1, 'L');
        $pdf->SetFont('Arial', '', 9);

        // Fetch and list data for this province
        $query = "SELECT 
                    d.distribution_date,
                    CONCAT(b.fname, ' ', IFNULL(b.mname, ''), ' ', b.lname) AS beneficiary_name,
                    b.municipality_name,
                    b.barangay_name,
                    b.coop_id,  -- Ensure this is included
                    IF(b.coop_id = 0, 'N/A', c.cooperative_name) AS cooperative_name,
                    it.intervention_name,
                    st.seed_name,
                    CONCAT(d.quantity, ' ', u.unit_name) AS quantity_with_unit
                FROM tbl_distribution AS d
                INNER JOIN tbl_beneficiary AS b ON d.beneficiary_id = b.beneficiary_id
                INNER JOIN tbl_seed_type AS st ON d.seed_id = st.seed_id
                INNER JOIN tbl_intervention_type AS it ON d.intervention_id = it.int_type_id
                LEFT JOIN tbl_cooperative AS c ON b.coop_id = c.coop_id
                LEFT JOIN tbl_intervention_inventory AS inv ON d.seed_id = inv.seed_id
                LEFT JOIN tbl_unit AS u ON inv.unit_id = u.unit_id
                WHERE d.station_id = ?
                AND b.province_name = ?
                AND (d.archived_at IS NULL OR d.archived_at = '')
                ORDER BY b.municipality_name ASC, d.distribution_date DESC";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $station_id, $province);
        $stmt->execute();
        $result = $stmt->get_result();

        $currentMunicipality = "";
        $coopData = [];
        $noCoopData = [];

        while ($row = $result->fetch_assoc()) {
            if ($row['coop_id'] > 0) {
                $coopData[] = $row;
            } else {
                $noCoopData[] = $row;
            }
        }

        foreach ([['With Cooperative', $coopData, true], ['No Cooperative', $noCoopData, false]] as [$title, $data, $hasCoop]) {
            if (!empty($data)) {
                $pdf->Ln(5);
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(0, 5, "Municipality: {$data[0]['municipality_name']} - $title", 0, 1, 'L');
                // $pdf->Cell(0, 8, "$title", 0, 1, 'L');
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetFillColor(200, 200, 200);
                
                $pdf->Cell(23, 10, 'Date', 1, 0, 'C', true);
                $pdf->Cell(50, 10, 'Beneficiary', 1, 0, 'C', true);
                $pdf->Cell(25, 10, 'Barangay', 1, 0, 'C', true);
                if ($hasCoop) {
                    $pdf->Cell(25, 10, 'Group Name', 1, 0, 'C', true);
                }
                $pdf->Cell(30, 10, 'Intervention', 1, 0, 'C', true);
                $pdf->Cell(18, 10, 'Seed', 1, 0, 'C', true);
                $pdf->Cell(19, 10, 'Quantity', 1, 1, 'C', true);
                
                $pdf->SetFont('Arial', '', 9);
                foreach ($data as $row) {
                    $pdf->Cell(23, 8, date("M j, Y", strtotime($row['distribution_date'])), 1);
                    $pdf->Cell(50, 8, $row['beneficiary_name'], 1);
                    $pdf->Cell(25, 8, $row['barangay_name'], 1);
                    if ($hasCoop) {
                        $pdf->Cell(25, 8, $row['cooperative_name'], 1);
                    }
                    $pdf->Cell(30, 8, $row['intervention_name'], 1);
                    $pdf->Cell(18, 8, $row['seed_name'], 1);
                    $pdf->Cell(19, 8, $row['quantity_with_unit'], 1, 1);
                }
            }
        }
        $pdf->Ln(10);  // Space between provinces
    }

    $pdf->SetTitle('Distribution Report');
    // $pdf->Image('../../img/da.png', 10, 5, 20); // Adjust path if needed
    $pdf->Output();
?>
