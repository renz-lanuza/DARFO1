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

// Get station ID of logged-in user
$stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
$stationQuery->bind_param("i", $user_id);
$stationQuery->execute();
$stationQuery->bind_result($station_id);
$stationQuery->fetch();
$stationQuery->close();

// Get month and year from request (default to current month)
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Fetch distinct provinces for the selected month
$provinceQuery = "SELECT DISTINCT b.province_name 
                FROM tbl_distribution AS d
                INNER JOIN tbl_beneficiary AS b ON d.beneficiary_id = b.beneficiary_id
                WHERE d.station_id = ? 
                AND MONTH(d.distribution_date) = ? 
                AND YEAR(d.distribution_date) = ?
                AND (d.archived_at IS NULL OR d.archived_at = '')
                ORDER BY b.province_name ASC";

$stmt = $conn->prepare($provinceQuery);
$stmt->bind_param("iii", $station_id, $month, $year);
$stmt->execute();
$provinceResult = $stmt->get_result();
// Get selected date range (default to full month if not provided)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Create PDF with Template
$pdf = new FPDI();
$pdf->SetTitle('Monthly Distribution Report');

// Set Template
$templatePath = 'template.pdf';  
$pdf->setSourceFile($templatePath);
$tplIdx = $pdf->importPage(1);

$dataExists = false; // Flag to check if any data is present

while ($provinceRow = $provinceResult->fetch_assoc()) {
    $province = $provinceRow['province_name'];

    // Fetch and list data for this province and selected month
    $query = "SELECT 
                d.distribution_date,
                CONCAT(b.fname, ' ', IFNULL(b.mname, ''), ' ', b.lname) AS beneficiary_name,
                b.municipality_name,
                b.barangay_name,
                b.coop_id,
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
            AND d.distribution_date BETWEEN ? AND ?
            AND (d.archived_at IS NULL OR d.archived_at = '')
            ORDER BY b.municipality_name ASC, d.distribution_date DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $station_id, $province, $start_date, $end_date);

    $stmt->execute();
    $result = $stmt->get_result();

    $currentMunicipality = "";
    $coopData = [];
    $noCoopData = [];

    while ($row = $result->fetch_assoc()) {
        $dataExists = true; // Mark that data is found
        if ($row['coop_id'] > 0) {
            $coopData[] = $row;
        } else {
            $noCoopData[] = $row;
        }
    }

    if (!$dataExists) continue; // Skip if no data for this province

    // **ADD A PAGE ONLY IF DATA EXISTS**
    $pdf->AddPage();
    $pdf->useTemplate($tplIdx, 0, 0, 210);
    $pdf->SetY(40);
    // Report Header
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, "Distribution Report (" . date("F j, Y", strtotime($start_date)) . " to " . date("F j, Y", strtotime($end_date)) . ")", 0, 1, 'C');
    $pdf->Ln(5);

    // Province Title
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, "Province: $province", 0, 1, 'L');
    $pdf->SetFont('Arial', '', 9);

    foreach ([['With Cooperative', $coopData, true], ['No Cooperative', $noCoopData, false]] as [$title, $data, $hasCoop]) {
        if (!empty($data)) {
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 5, "Municipality: {$data[0]['municipality_name']} - $title", 0, 1, 'L');
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
                // Get the height of the row based on the Seed Name column
                $seed_name_height = $pdf->GetStringWidth($row['seed_name']) > 18 ? 16 : 8; 
                $row_height = max(8, $seed_name_height); // Set minimum row height
                
                $pdf->Cell(23, $row_height, date("M j, Y", strtotime($row['distribution_date'])), 1, 0);
                $pdf->Cell(50, $row_height, $row['beneficiary_name'], 1, 0);
                $pdf->Cell(25, $row_height, $row['barangay_name'], 1, 0);
            
                if ($hasCoop) {
                    $pdf->Cell(25, $row_height, $row['cooperative_name'], 1, 0);
                }
            
                $pdf->Cell(30, $row_height, $row['intervention_name'], 1, 0);
            
                // Handle Seed Name Wrapping
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(18, 8, $row['seed_name'], 1);
                $pdf->SetXY($x + 18, $y); // Reset X position
            
                $pdf->Cell(19, $row_height, $row['quantity_with_unit'], 1, 1); // Last cell in row
            }            
        }
    }
    $pdf->Ln(10);  // Space between provinces
}

// If no data was found, add a message instead of a blank report
if (!$dataExists) {
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, "No data available for $year - " . date("F", mktime(0, 0, 0, $month, 1)), 0, 1, 'C');
}

$pdf->SetTitle('Monthly Distribution Report');
$pdf->Output();
?>
