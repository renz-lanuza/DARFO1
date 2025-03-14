<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $distribution_id = $_POST['distribution_id'];
    $new_intervention_id = $_POST['intervention_name_distrib'][0];
    $new_seed_id = $_POST['seedling_type_distrib'];
    $new_quantity = $_POST['update_quantity'][0];
    $new_distribution_date = $_POST['update_distribution_date'];

    $conn = new mysqli("localhost", "root", "", "db_darfo1");

    if ($conn->connect_error) {
        die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Fetch existing distribution data
        $stmt = $conn->prepare("SELECT intervention_id, seed_id, quantity FROM tbl_distribution WHERE distribution_id = ?");
        $stmt->bind_param("i", $distribution_id);
        $stmt->execute();
        $stmt->bind_result($old_intervention_id, $old_seed_id, $old_quantity);
        if (!$stmt->fetch()) {
            throw new Exception("Distribution record not found.");
        }
        $stmt->close();

        // Restore old inventory before making changes
        $stmt = $conn->prepare("UPDATE tbl_intervention_inventory SET quantity_left = quantity_left + ? WHERE int_type_id = ? AND seed_id = ?");
        $stmt->bind_param("iii", $old_quantity, $old_intervention_id, $old_seed_id);
        $stmt->execute();
        $stmt->close();

        // Fetch new inventory quantity
        $stmt = $conn->prepare("SELECT quantity_left FROM tbl_intervention_inventory WHERE int_type_id = ? AND seed_id = ?");
        $stmt->bind_param("ii", $new_intervention_id, $new_seed_id);
        $stmt->execute();
        $stmt->bind_result($new_quantity_left);
        if (!$stmt->fetch()) {
            throw new Exception("New inventory record not found for intervention_id: $new_intervention_id, seed_id: $new_seed_id.");
        }
        $stmt->close();

        // Check if there is enough stock in the new inventory
        if ($new_quantity_left < $new_quantity) {
            throw new Exception("Not enough stock in the new inventory for intervention_id: $new_intervention_id, seed_id: $new_seed_id. Available: $new_quantity_left, Required: $new_quantity.");
        }

        // Deduct from the new inventory
        $stmt = $conn->prepare("UPDATE tbl_intervention_inventory SET quantity_left = quantity_left - ? WHERE int_type_id = ? AND seed_id = ?");
        $stmt->bind_param("iii", $new_quantity, $new_intervention_id, $new_seed_id);
        $stmt->execute();
        $stmt->close();

        // Update the distribution record
        $stmt = $conn->prepare("UPDATE tbl_distribution SET intervention_id = ?, seed_id = ?, quantity = ?, distribution_date = ? WHERE distribution_id = ?");
        $stmt->bind_param("iiisi", $new_intervention_id, $new_seed_id, $new_quantity, $new_distribution_date, $distribution_id);
        $stmt->execute();
        if ($stmt->affected_rows === 0) {
            throw new Exception("Failed to update distribution record.");
        }
        $stmt->close();

        // Commit transaction
        $conn->commit();

        echo json_encode(["status" => "success", "message" => "Distribution updated successfully."]);
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "Failed to update distribution: " . $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
