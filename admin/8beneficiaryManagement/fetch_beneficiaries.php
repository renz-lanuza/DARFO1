<?php
include('../../conn.php');

$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Base query
$query = "SELECT beneficiary_id, fname, mname, lname, rsbsa_no, province_name, municipality_name, barangay_name, birthdate, beneficiary_category 
          FROM tbl_beneficiary";

// If a specific category is selected, add WHERE condition
if ($category !== 'all') {
    $query .= " WHERE beneficiary_category = ?";
}

// Append ORDER BY
$query .= " ORDER BY beneficiary_id DESC";

// Prepare statement
$stmt = $conn->prepare($query);

if (!$stmt) {
    die(json_encode(['error' => 'SQL preparation failed: ' . $conn->error]));
}

// Bind parameter only if filtering by category
if ($category !== 'all') {
    $stmt->bind_param("s", $category);
}

$stmt->execute();
$result = $stmt->get_result();

function formatRsbsaNo($rsbsa_no) {
    if (strlen($rsbsa_no) === 15) {
        return substr($rsbsa_no, 0, 2) . '-' .
            substr($rsbsa_no, 2, 2) . '-' .
            substr($rsbsa_no, 4, 2) . '-' .
            substr($rsbsa_no, 6, 3) . '-' .
            substr($rsbsa_no, 9, 6);
    }
    return $rsbsa_no;
}

$data = [];

while ($row = $result->fetch_assoc()) {
    $fullName = $row['fname'] . ' ' . (!empty($row['mname']) ? $row['mname'] . ' ' : '') . $row['lname'];
    $formattedBirthdate = date('F j, Y', strtotime($row['birthdate']));
    $rsbsa_no = !empty($row['rsbsa_no']) ? formatRsbsaNo($row['rsbsa_no']) : 'N/A';

    $data[] = [
        'beneficiary_id' => $row['beneficiary_id'],
        'fullName' => $fullName,
        'rsbsa_no' => $rsbsa_no,
        'province_name' => $row['province_name'],
        'municipality_name' => $row['municipality_name'],
        'barangay_name' => $row['barangay_name'],
        'birthdate' => $formattedBirthdate
    ];
}

// Return JSON response
echo json_encode($data);

$stmt->close();
$conn->close();
?>
