<div class="modal fade" id="addSeedTypeModal" tabindex="-1" aria-labelledby="addSeedTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #0D7C66; color: white;">
                <h5 class="modal-title" id="addSeedTypeModalLabel">Add Seed Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addSeedTypeForm">
                    <?php
                    include('../conn.php'); // Include the connection to the database


                    if (!isset($_SESSION['uid'])) {
                        die("User  ID not found in session.");
                    }

                    $uid = $_SESSION['uid']; // Get the uid from the session

                    // Retrieve the station_id based on the logged-in user's uid
                    $stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
                    $stationQuery->bind_param("i", $uid);
                    $stationQuery->execute();
                    $stationQuery->bind_result($stationId);
                    $stationQuery->fetch();
                    $stationQuery->close();

                    // Check if station_id was found
                    if (empty($stationId)) {
                        die("No station found for the user.");
                    }

                    // SQL query to fetch the intervention names filtered by station_id
                    $sql = "SELECT * FROM tbl_intervention_type WHERE station_id = ? ORDER BY int_type_id";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $stationId); // Assuming station_id is an integer
                    $stmt->execute();
                    $result = $stmt->get_result(); // Get the result set from the prepared statement

                    // Check if query executed successfully
                    if ($result === false) {
                        die("Error in SQL query: " . $conn->error);
                    }

                    // Store the intervention names in an array for later use
                    $interventions = [];
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $interventions[] = $row; // Store each row in the array
                        }
                    } else {
                        echo "No intervention types found for this station.";
                    }

                    // Close the statement
                    $stmt->close();
                    ?>

                    <div class="mb-3">
                        <label for="intervention_name" class="form-label">Intervention Name</label>
                        <select class="form-control" id="intervention_name" name="interventionName11" required>
                            <option value="" disabled selected>Select an Intervention</option>
                            <?php
                            // Populate the dropdown with intervention names
                            if (!empty($interventions)) {
                                foreach ($interventions as $intervention) {
                                    echo "<option value='{$intervention['int_type_id']}'>" . htmlspecialchars($intervention['intervention_name']) . "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No interventions found</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <?php
                    // Close the database connection
                    $conn->close();
                    ?>

                    <div class="mb-3">
                        <label for="seed_type_name" class="form-label">Classification Name</label>
                        <input type="text" class="form-control" id="seed_type_name" name="seed_type_name" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-lg {
        max-width: 40%; /* Adjusted size for better visibility */
    }

    .form-control:invalid {
        border-color: #e74c3c; /* Red border for invalid input */
    }

    .modal-header {
        background-color: #0D7C66;
        color: white;
    }

    .modal-footer {
        padding-top: 1rem;
    }

    .form-text {
        font-size: 0.9rem;
        color: #6c757d;
    }
</style>

<!-- Edit Seedling Modal -->
<div class="modal fade" id="editSeedlingModal" tabindex="-1" aria-labelledby="editSeedlingLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSeedlingLabel">Update Classification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
           
            <div class="modal-body">
                <form id="updateSeedForm">
                    <input type="hidden" id="seed_id" name="seed_id">

                    <!-- Intervention Name (Fixed ID) -->
                    <div class="mb-3">
                        <label for="up_intervention_name" class="form-label">Intervention Name</label>
                        <input type="text" id="up_intervention_name" name="intervention_name" class="form-control" required disabled>
                    </div>

                    <!-- Seed Name -->
                    <div class="mb-3">
                        <label for="seed_name" class="form-label">Classification</label>
                        <input type="text" id="seed_name" name="seed_name" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="updateSeedForm" class="btn btn-success">Update</button>
            </div>
        </div>
    </div>
</div>

