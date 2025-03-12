<?php
header("Content-Type: application/json");

// Database connection
include('../../conn.php');

// Function to sanitize input data
function sanitizeInput($data)
{
    $data = trim($data); // Remove whitespace
    $data = stripslashes($data); // Remove backslashes
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // Convert special characters to HTML entities
    return $data;
}

// Check if POST request is made
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $type_of_distribution = sanitizeInput($_POST["type_of_distribution"]);
    $beneficiary_first_name = sanitizeInput($_POST["beneficiary_first_name"]);
    $beneficiary_middle_name = !empty($_POST["beneficiary_middle_name"]) ? sanitizeInput($_POST["beneficiary_middle_name"]) : null; // Allow NULL
    $beneficiary_last_name = sanitizeInput($_POST["beneficiary_last_name"]);
    $provinceCode = sanitizeInput($_POST['provinceCode']);
    $provinceName = sanitizeInput($_POST['provinceName']);
    $municipalityCode = sanitizeInput($_POST['municipalityCode']);
    $municipalityName = sanitizeInput($_POST['municipalityName']);
    $barangayCode = sanitizeInput($_POST['barangayCode']);
    $barangayName = sanitizeInput($_POST['barangayName']);
    $cooperative_id = !empty($_POST['cooperative_id']) ? intval($_POST['cooperative_id']) : 0; // Default to 0 if not provided
    $distribution_date = date('Y-m-d', strtotime(sanitizeInput($_POST['distribution_date']))); // Format the date

    // Handle RSBSA No.
    $rsbsa_no = isset($_POST["rsbsa_no"]) ? sanitizeInput($_POST["rsbsa_no"]) : null;

    // Remove dashes from RSBSA No.
    if (!empty($rsbsa_no)) {
        $rsbsa_no = str_replace('-', '', $rsbsa_no); // Remove dashes

        // Validate RSBSA number length
        if (strlen($rsbsa_no) !== 15) {
            echo json_encode(["status" => "error", "message" => "RSBSA number must be exactly 15 digits long after removing dashes."]);
            exit;
        }
    }

    // Other fields
    $sex = isset($_POST["sex"]) ? sanitizeInput($_POST["sex"]) : null;
    $birthdate = isset($_POST['birthdate']) ? date('Y-m-d', strtotime(sanitizeInput($_POST['birthdate']))) : null; // Format the date

    // Handle applicable checkboxes
    $applicable = isset($_POST['applicable']) ? $_POST['applicable'] : []; // Ensure $applicable is always an array
    $applicable = array_map('sanitizeInput', $applicable); // Sanitize each value in the array
    $applicable_string = !empty($applicable) ? implode(',', $applicable) : ''; // Default to empty string

    // Determine beneficiary type
    $individual_type = isset($_POST["individual_type"]) ? sanitizeInput($_POST["individual_type"]) : null;
    $group_type = isset($_POST["group_type"]) ? sanitizeInput($_POST["group_type"]) : null;

    // Check if "Others" was selected for individual type
    if ($individual_type === 'Others') {
        $others_specify = isset($_POST["others_specify"]) ? sanitizeInput($_POST["others_specify"]) : null;
        $beneficiary_type = $others_specify; // Use the specified value
    } else {
        $beneficiary_type = $individual_type; // Use the selected individual type
    }

    // Check if "Others" was selected for group type
    if ($group_type === 'Others') {
        $group_others_specify = isset($_POST["group_others_specify"]) ? sanitizeInput($_POST["group_others_specify"]) : null;
        $beneficiary_type = $group_others_specify; // Use the specified value
    } elseif ($type_of_distribution === 'Group') {
        $beneficiary_type = $group_type; // Use the selected group type
    }

    // Get arrays from POST data
    $intervention_ids = $_POST['intervention_name_distri']; // Array of intervention IDs
    $quantities = $_POST['quantity_distri']; // Array of quantities
    $seed_ids = $_POST['seedling_type_distri']; // Array of seed IDs

    // Sanitize arrays
    $intervention_ids = array_map('intval', $intervention_ids); // Convert to integers
    $quantities = array_map('intval', $quantities); // Convert to integers
    $seed_ids = array_map('intval', $seed_ids); // Convert to integers

    // Check if the arrays are of the same length
    if (count($intervention_ids) !== count($quantities) || count($intervention_ids) !== count($seed_ids)) {
        echo json_encode(["status" => "error", "message" => "Mismatched array lengths for intervention IDs, quantities, and seed IDs."]);
        exit;
    }

    // Check if quantity is greater than zero for each entry
    foreach ($quantities as $quantity) {
        if ($quantity <= 0) {
            echo json_encode(["status" => "error", "message" => "All quantities must be greater than zero."]);
            exit;
        }
    }

    // Retrieve the user's station ID from the session
    session_start();
    if (!isset($_SESSION['uid'])) {
        echo json_encode(["status" => "error", "message" => "User not logged in."]);
        exit;
    }

    $user_id = $_SESSION['uid'];
    $stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
    $stationQuery->bind_param("i", $user_id);
    $stationQuery->execute();
    $stationQuery->bind_result($station_id);
    $stationQuery->fetch();
    $stationQuery->close();

    // Check if the station ID was found
    if ($station_id === null) {
        echo json_encode(["status" => "error", "message" => "Station not found for the user."]);
        exit;
    }

    // Start a transaction
    $conn->begin_transaction();
    try {
        // Insert province, municipality, and barangay if they don't exist
        $sql_insert_province = "INSERT IGNORE INTO provinces (province_code, province_name) VALUES (?, ?)";
        $stmt_insert_province = $conn->prepare($sql_insert_province);
        $stmt_insert_province->bind_param("is", $provinceCode, $provinceName);
        $stmt_insert_province->execute();
        $stmt_insert_province->close();

        $sql_insert_municipality = "INSERT IGNORE INTO municipalities (municipality_code, municipality_name, province_code) VALUES (?, ?, ?)";
        $stmt_insert_municipality = $conn->prepare($sql_insert_municipality);
        $stmt_insert_municipality->bind_param("isi", $municipalityCode, $municipalityName, $provinceCode);
        $stmt_insert_municipality->execute();
        $stmt_insert_municipality->close();

        $sql_insert_barangay = "INSERT IGNORE INTO barangays (barangay_code, barangay_name, municipality_code) VALUES (?, ?, ?)";
        $stmt_insert_barangay = $conn->prepare($sql_insert_barangay);
        $stmt_insert_barangay->bind_param("isi", $barangayCode, $barangayName, $municipalityCode);
        $stmt_insert_barangay->execute();
        $stmt_insert_barangay->close();

        // Check if beneficiary already exists
        if ($beneficiary_middle_name === null) {
            // Handle NULL middle name
            $sql_check_beneficiary = "SELECT beneficiary_id FROM tbl_beneficiary WHERE fname = ? AND mname IS NULL AND lname = ? AND province_name = ? AND municipality_name = ? AND barangay_name = ? AND rsbsa_no = ?";
            $stmt_check_beneficiary = $conn->prepare($sql_check_beneficiary);
            $stmt_check_beneficiary->bind_param("ssssss", $beneficiary_first_name, $beneficiary_last_name, $provinceName, $municipalityName, $barangayName, $rsbsa_no);
        } else {
            // Handle non-NULL middle name
            $sql_check_beneficiary = "SELECT beneficiary_id FROM tbl_beneficiary WHERE fname = ? AND mname = ? AND lname = ? AND province_name = ? AND municipality_name = ? AND barangay_name = ? AND rsbsa_no = ?";
            $stmt_check_beneficiary = $conn->prepare($sql_check_beneficiary);
            $stmt_check_beneficiary->bind_param("sssssss", $beneficiary_first_name, $beneficiary_middle_name, $beneficiary_last_name, $provinceName, $municipalityName, $barangayName, $rsbsa_no);
        }
        $stmt_check_beneficiary->execute();
        $stmt_check_beneficiary->store_result();
        $stmt_check_beneficiary->bind_result($existing_beneficiary_id);
        $stmt_check_beneficiary->fetch();

        if ($stmt_check_beneficiary->num_rows > 0) {
            // Beneficiary exists, use the existing ID
            $beneficiary_id = $existing_beneficiary_id;
        } else {
            // Insert new beneficiary
            $sql_insert_beneficiary = "INSERT INTO tbl_beneficiary (fname, mname, lname, province_name, municipality_name, barangay_name, station_id, coop_id, rsbsa_no, sex, birthdate, beneficiary_type, if_applicable) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert_beneficiary = $conn->prepare($sql_insert_beneficiary);
            $stmt_insert_beneficiary->bind_param("ssssssiisssss", $beneficiary_first_name, $beneficiary_middle_name, $beneficiary_last_name, $provinceName, $municipalityName, $barangayName, $station_id, $cooperative_id, $rsbsa_no, $sex, $birthdate, $beneficiary_type, $applicable_string);

            if (!$stmt_insert_beneficiary->execute()) {
                throw new Exception("Error inserting beneficiary: " . $stmt_insert_beneficiary->error);
            }
            $beneficiary_id = $stmt_insert_beneficiary->insert_id; // Get the auto-generated beneficiary ID
            $stmt_insert_beneficiary->close();
        }
        $stmt_check_beneficiary->close();

        // Insert distributions and update inventory
        for ($i = 0; $i < count($intervention_ids); $i++) {
            $intervention_id = intval($intervention_ids[$i]);
            $quantity = intval($quantities[$i]);
            $seed_id = intval($seed_ids[$i]);

            // Check inventory with intervention and seed names
            $sql_check_quantity = "
                SELECT 
                    i.quantity_left, 
                    it.intervention_name, 
                    st.seed_name 
                FROM tbl_intervention_inventory i
                INNER JOIN tbl_intervention_type it ON i.int_type_id = it.int_type_id
                INNER JOIN tbl_seed_type st ON i.seed_id = st.seed_id
                WHERE i.int_type_id = ? AND i.seed_id = ?
            ";
            $stmt_check = $conn->prepare($sql_check_quantity);
            $stmt_check->bind_param("ii", $intervention_id, $seed_id);
            $stmt_check->execute();
            $stmt_check->store_result();
            $stmt_check->bind_result($quantity_left, $intervention_name, $seed_name);
            $stmt_check->fetch();
            $stmt_check->close();

            if ($quantity_left >= $quantity) {
                // Insert into distribution table
                $sql_insert_distribution = "INSERT INTO tbl_distribution (beneficiary_id, type_of_distribution, intervention_id, quantity, seed_id, station_id, distribution_date) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_insert_distribution = $conn->prepare($sql_insert_distribution);
                $stmt_insert_distribution->bind_param("issiiis", $beneficiary_id, $type_of_distribution, $intervention_id, $quantity, $seed_id, $station_id, $distribution_date);
                $stmt_insert_distribution->execute();
                $stmt_insert_distribution->close();

                // Update inventory
                $new_quantity_left = $quantity_left - $quantity;
                $sql_update_inventory = "UPDATE tbl_intervention_inventory SET quantity_left = ? WHERE int_type_id = ? AND seed_id = ?";
                $stmt_update_inventory = $conn->prepare($sql_update_inventory);
                $stmt_update_inventory->bind_param("iii", $new_quantity_left, $intervention_id, $seed_id);
                $stmt_update_inventory->execute();
                $stmt_update_inventory->close();
            } else {
                throw new Exception("Insufficient inventory for intervention: $intervention_name and seed: $seed_name.");
            }
        }

        // Commit the transaction
        $conn->commit();
        echo json_encode(["status" => "success", "message" => "All distributions added and inventory updated successfully."]);
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "Transaction failed: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
$conn->close();
