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
    $beneficiary_first_name = sanitizeInput($_POST["beneficiary_first_name"]);
    $beneficiary_middle_name = !empty($_POST["beneficiary_middle_name"]) ? sanitizeInput($_POST["beneficiary_middle_name"]) : null; // Allow NULL
    $beneficiary_last_name = sanitizeInput($_POST["beneficiary_last_name"]);
    $provinceCode = sanitizeInput($_POST['provinceCode']);
    $provinceName = sanitizeInput($_POST['provinceName']);
    $municipalityCode = sanitizeInput($_POST['municipalityCode']);
    $municipalityName = sanitizeInput($_POST['municipalityName']);
    $barangayCode = sanitizeInput($_POST['barangayCode']);
    $barangayName = sanitizeInput($_POST['barangayName']);
    $contact_number = sanitizeInput($_POST['contact_number']);
    $cooperative_id = !empty($_POST['cooperative_id']) ? intval($_POST['cooperative_id']) : 0; // Default to 0 if not provided

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

    // Get Street/Purok input
    $streetPurok = isset($_POST['streetPurok']) ? sanitizeInput($_POST['streetPurok']) : null; // Allow NULL

    // Determine beneficiary type (Individual or Group)
    $beneficiary_category = sanitizeInput($_POST["beneficiary_category"]); // Individual or Group

    // Handle Individual Type
    if ($beneficiary_category === "Individual") {
        $individual_type = isset($_POST["individual_type"]) ? sanitizeInput($_POST["individual_type"]) : null;

        // Check if "Others" was selected for individual type
        if ($individual_type === 'Others') {
            $others_specify = isset($_POST["others_specify"]) ? sanitizeInput($_POST["others_specify"]) : null;
            if (!empty($others_specify)) {
                $beneficiary_type = $others_specify; // Use the specified value
            } else {
                echo json_encode(["status" => "error", "message" => "Please specify the individual type."]);
                exit;
            }
        } else {
            $beneficiary_type = $individual_type; // Use the selected individual type
        }
    }

    // Handle Group Type
    if ($beneficiary_category === "Group") {
        $group_type = isset($_POST["group_type"]) ? sanitizeInput($_POST["group_type"]) : null;

        // Check if "Others" was selected for group type
        if ($group_type === 'Others') {
            $group_others_specify = isset($_POST["group_others_specify"]) ? sanitizeInput($_POST["group_others_specify"]) : null;
            if (!empty($group_others_specify)) {
                $beneficiary_type = $group_others_specify; // Use the specified value
            } else {
                echo json_encode(["status" => "error", "message" => "Please specify the group type."]);
                exit;
            }
        } else {
            $beneficiary_type = $group_type; // Use the selected group type
        }
    }

    // Check if beneficiary_type is still null
    if (is_null($beneficiary_type)) {
        echo json_encode(["status" => "error", "message" => "Beneficiary type must be specified."]);
        exit;
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

        // Check if beneficiary already exists (excluding rsbsa_no)
        if ($beneficiary_middle_name === null) {
            // Handle NULL middle name
            $sql_check_beneficiary = "SELECT beneficiary_id FROM tbl_beneficiary WHERE fname = ? AND mname IS NULL AND lname = ? AND province_name = ? AND municipality_name = ? AND barangay_name = ?";
            $stmt_check_beneficiary = $conn->prepare($sql_check_beneficiary);
            $stmt_check_beneficiary->bind_param("sssss", $beneficiary_first_name, $beneficiary_last_name, $provinceName, $municipalityName, $barangayName);
        } else {
            // Handle non-NULL middle name
            $sql_check_beneficiary = "SELECT beneficiary_id FROM tbl_beneficiary WHERE fname = ? AND mname = ? AND lname = ? AND province_name = ? AND municipality_name = ? AND barangay_name = ?";
            $stmt_check_beneficiary = $conn->prepare($sql_check_beneficiary);
            $stmt_check_beneficiary->bind_param("ssssss", $beneficiary_first_name, $beneficiary_middle_name, $beneficiary_last_name, $provinceName, $municipalityName, $barangayName);
        }
        $stmt_check_beneficiary->execute();
        $stmt_check_beneficiary->store_result();

        // If beneficiary already exists, return an error
        if ($stmt_check_beneficiary->num_rows > 0) {
            echo json_encode(["status" => "error", "message" => "Beneficiary already exists."]);
            exit;
        }
        $stmt_check_beneficiary->close();

        // Insert new beneficiary
        $sql_insert_beneficiary = "INSERT INTO tbl_beneficiary (
            fname, mname, lname, province_name, municipality_name, barangay_name, 
            StreetPurok, station_id, coop_id, rsbsa_no, sex, birthdate, 
            beneficiary_type, if_applicable, contact_no, beneficiary_category
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert_beneficiary = $conn->prepare($sql_insert_beneficiary);
        if (!$stmt_insert_beneficiary) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt_insert_beneficiary->bind_param(
            "sssssssiisssssss",
            $beneficiary_first_name,
            $beneficiary_middle_name,
            $beneficiary_last_name,
            $provinceName,
            $municipalityName,
            $barangayName,
            $streetPurok,
            $station_id,
            $cooperative_id,
            $rsbsa_no,
            $sex,
            $birthdate,
            $beneficiary_type,
            $applicable_string,
            $contact_number,
            $beneficiary_category
        );
        if (!$stmt_insert_beneficiary->execute()) {
            throw new Exception("Error inserting beneficiary: " . $stmt_insert_beneficiary->error);
        }
        $stmt_insert_beneficiary->close();

        // Commit the transaction
        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Beneficiary added successfully."]);
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "Transaction failed: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
$conn->close();
