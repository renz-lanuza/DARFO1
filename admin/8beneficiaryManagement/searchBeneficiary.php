<?php
include('../../conn.php');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    // Remove multiple spaces
    $search = preg_replace('/\s+/', ' ', $search);

    // Adjust query to match full names with or without spaces
    $query = "SELECT beneficiary_id, fname, mname, lname, rsbsa_no, province_name, municipality_name, barangay_name, birthdate 
              FROM tbl_beneficiary 
              WHERE CONCAT(fname, ' ', lname) LIKE ? 
              OR CONCAT(fname, ' ', mname, ' ', lname) LIKE ? 
              OR rsbsa_no LIKE ? 
              OR fname LIKE ? 
              OR lname LIKE ?";

    $stmt = $conn->prepare($query);
    $searchTerm = "%{$search}%";
    $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $output = '';

    if ($result->num_rows > 0) :
        while ($row = $result->fetch_assoc()) :
            $fullName = $row['fname'] . ' ' . (!empty($row['mname']) ? $row['mname'] . ' ' : '') . $row['lname'];
            $formattedBirthdate = date('F j, Y', strtotime($row['birthdate']));
            $rsbsa_no = !empty($row['rsbsa_no']) ? $row['rsbsa_no'] : 'N/A';

            $output .= '<tr>
                <td>' . htmlspecialchars($fullName) . '</td>
                <td>' . htmlspecialchars($rsbsa_no) . '</td>
                <td>' . htmlspecialchars($row['province_name']) . '</td>
                <td>' . htmlspecialchars($row['municipality_name']) . '</td>
                <td>' . htmlspecialchars($row['barangay_name']) . '</td>
                <td>' . htmlspecialchars($formattedBirthdate) . '</td>
                <td>
                    <button class="btn btn-primary" onclick="openUpdateBeneficiaryModal(' . $row['beneficiary_id'] . ')">Update</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteBeneficiary(' . $row['beneficiary_id'] . ')">Delete</button>
                    <button class="btn btn-info btn-sm view-beneficiary" data-id="' . $row['beneficiary_id'] . '">View</button>
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addDistributionModal" 
                        data-beneficiary-id="' . $row['beneficiary_id'] . '">
                        <i class="bx bx-plus"></i>
                        <span>Add Intervention</span>
                    </button>
                </td>
            </tr>';
        endwhile;
    else :
        $output = '<tr><td colspan="7">No beneficiaries found.</td></tr>';
    endif;

    echo $output;
}

$conn->close();
?>
