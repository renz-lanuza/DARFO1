<?php
header("Content-Type: application/json");

// Database connection
include('../../conn.php');

// Check if POST request is made
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $type_of_distribution = htmlspecialchars($_POST["type_of_distribution"]);
    $beneficiary_first_name = htmlspecialchars($_POST["beneficiary_first_name"]);
    $beneficiary_middle_name = !empty($_POST["beneficiary_middle_name"]) ? htmlspecialchars($_POST["beneficiary_middle_name"]) : null; // Allow NULL
    $beneficiary_last_name = htmlspecialchars($_POST["beneficiary_last_name"]);
    $provinceCode = $_POST['provinceCode'];
    $provinceName = $_POST['provinceName'];
    $municipalityCode = $_POST['municipalityCode'];
    $municipalityName = $_POST['municipalityName'];
    $barangayCode = $_POST['barangayCode'];
    $barangayName = $_POST['barangayName'];
    $cooperative_id = !empty($_POST['cooperative_id']) ? intval($_POST['cooperative_id']) : 0; // Default to 0 if not provided
    $distribution_date = date('Y-m-d', strtotime($_POST['distribution_date'])); // Format the date

    // Get arrays from POST data
    $intervention_ids = $_POST['intervention_name_distri']; // Array of intervention IDs
    $quantities = $_POST['quantity_distri']; // Array of quantities
    $seed_ids = $_POST['seedling_type_distri']; // Array of seed IDs

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
        $stmt_check_beneficiary->bind_result($existing_beneficiary_id);
        $stmt_check_beneficiary->fetch();

        if ($stmt_check_beneficiary->num_rows > 0) {
            // Beneficiary exists, use the existing ID
            $beneficiary_id = $existing_beneficiary_id;
        } else {
            // Insert new beneficiary
            $sql_insert_beneficiary = "INSERT INTO tbl_beneficiary (fname, mname, lname, province_name, municipality_name, barangay_name, station_id, coop_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert_beneficiary = $conn->prepare($sql_insert_beneficiary);
            $stmt_insert_beneficiary->bind_param("ssssssii", $beneficiary_first_name, $beneficiary_middle_name, $beneficiary_last_name, $provinceName, $municipalityName, $barangayName, $station_id, $cooperative_id);

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

            // Check inventory
            $sql_check_quantity = "SELECT quantity_left FROM tbl_intervention_inventory WHERE int_type_id = ? AND seed_id = ?";
            $stmt_check = $conn->prepare($sql_check_quantity);
            $stmt_check->bind_param("ii", $intervention_id, $seed_id);
            $stmt_check->execute();
            $stmt_check->store_result();
            $stmt_check->bind_result($quantity_left);
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
                throw new Exception("Insufficient inventory for intervention ID: $intervention_id and seed ID: $seed_id.");
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
