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
    $distribution_date = date('Y-m-d', strtotime(sanitizeInput($_POST['distribution_date']))); // Format the date
    $beneficiary_id = intval($_POST['beneficiary_id']); // Get beneficiary ID from the form

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
        // Group quantities by intervention ID and seed ID
        $groupedData = [];
        for ($i = 0; $i < count($intervention_ids); $i++) {
            $intervention_id = $intervention_ids[$i];
            $seed_id = $seed_ids[$i];
            $quantity = $quantities[$i];

            // Create a unique key for the combination of intervention ID and seed ID
            $key = "{$intervention_id}-{$seed_id}";

            // If the key already exists, add the quantity to the existing value
            if (isset($groupedData[$key])) {
                $groupedData[$key]['quantity'] += $quantity;
            } else {
                // Otherwise, create a new entry
                $groupedData[$key] = [
                    'intervention_id' => $intervention_id,
                    'seed_id' => $seed_id,
                    'quantity' => $quantity
                ];
            }
        }

        // Insert distributions and update inventory
        foreach ($groupedData as $data) {
            $intervention_id = $data['intervention_id'];
            $seed_id = $data['seed_id'];
            $quantity = $data['quantity'];

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
                $sql_insert_distribution = "INSERT INTO tbl_distribution (beneficiary_id, intervention_id, quantity, seed_id, station_id, distribution_date) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_insert_distribution = $conn->prepare($sql_insert_distribution);
                $stmt_insert_distribution->bind_param("iiiiis", $beneficiary_id, $intervention_id, $quantity, $seed_id, $station_id, $distribution_date);
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
