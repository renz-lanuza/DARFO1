<?php
header("Content-Type: application/json");

// Include your database connection file
include('../../conn.php');

// Get the beneficiary ID from the request
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid beneficiary ID."]);
    exit;
}

// Function to format RSBSA number
function formatRsbsaNo($rsbsa_no)
{
    // Check if the RSBSA number is valid and has the correct length
    if (strlen($rsbsa_no) === 15) {
        return substr($rsbsa_no, 0, 2) . '-' .
            substr($rsbsa_no, 2, 2) . '-' .
            substr($rsbsa_no, 4, 2) . '-' .
            substr($rsbsa_no, 6, 3) . '-' .
            substr($rsbsa_no, 9, 6);
    }
    return $rsbsa_no; // Return as is if not valid
}

// Fetch beneficiary details from the database, including cooperative name
$query = "
    SELECT 
        b.fname, 
        b.mname, 
        b.lname, 
        b.province_name, 
        b.municipality_name, 
        b.barangay_name, 
        b.rsbsa_no, 
        b.contact_no, 
        b.sex, 
        b.birthdate, 
        b.beneficiary_type, 
        b.if_applicable, 
        b.coop_id, 
        b.beneficiary_category,
        c.cooperative_name 
    FROM 
        tbl_beneficiary b
    LEFT JOIN 
        tbl_cooperative c ON b.coop_id = c.coop_id 
    WHERE 
        b.beneficiary_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Format the RSBSA number
    $row['rsbsa_no'] = formatRsbsaNo($row['rsbsa_no']);

    // Check if coop_id is zero or not set, and assign "N/A" if true
    $row['cooperative_name'] = isset($row['coop_id']) && $row['coop_id'] > 0 ? $row['cooperative_name'] : 'N/A';

    echo json_encode(["status" => "success", "data" => $row]);
} else {
    echo json_encode(["status" => "error", "message" => "Beneficiary not found."]);
}

$stmt->close();
$conn->close();
