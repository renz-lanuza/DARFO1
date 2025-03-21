<style>
    .modal-header {
        background-color: #0D7C66;
        color: white;
        /* White text for contrast */
    }
</style>

<!-- Modal -->
<div class="modal fade" id="addInterventionModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Add Intervention</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="interventionForm">
                    <?php
                    include('../conn.php'); // Include the connection to the database


                    // Check if the user is logged in
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
                    $sql = "SELECT int_type_id, intervention_name FROM tbl_intervention_type WHERE station_id = ? ORDER BY int_type_id";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $stationId); // Assuming station_id is an integer
                    $stmt->execute();
                    $result = $stmt->get_result(); // Get the result set from the prepared statement

                    // Check if query executed successfully
                    if ($result === false) {
                        die("Error in SQL query: " . $conn->error);
                    }
                    ?>

                    <div class="mb-3">
                        <label for="intervention_name" class="form-label">Intervention Name</label>
                        <select class="form-control" id="intervention_name" name="interventionName11" required>
                            <option value="" disabled selected>Select an Intervention</option>
                            <?php
                            // Check if there are any rows in the result
                            if ($result->num_rows > 0) {
                                // Fetch each row and create an option element
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['int_type_id']}'>" . htmlspecialchars($row['intervention_name']) . "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No interventions found</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="seedling_type" class="form-label">Classification</label>
                        <select class="form-control" id="seedling_type" name="seedling_type" required>
                            <option value="" disabled selected>Select Classification</option>
                        </select>
                    </div>

                    <?php
                    // Close the database connection
                    $conn->close();
                    ?>


                    <div class="mb-3">
                        <label for="interventionDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="interventionDescription" name="interventionDescription" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="interventionQty" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="interventionQty" name="interventionQty" required>
                    </div>
                    <div class="mb-3">
                        <label for="unit" class="form-label">Indicator</label>
                        <select class="form-control" id="unit" name="unit" required>
                            <option value="" disabled selected>Select Indicator</option>
                            <?php
                            include('../conn.php'); // Include your database connection

                            $query = "SELECT unit_id, unit_name FROM tbl_unit ORDER BY unit_name ASC";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($row['unit_id']) . "'>" . htmlspecialchars($row['unit_name']) . "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No units available</option>";
                            }

                            $conn->close();
                            ?>
                        </select>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success" form="interventionForm">Submit</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<!-- Update Intervention Modal -->
<div class="modal fade" id="updateInterventionModal" tabindex="-1" aria-labelledby="updateInterventionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateInterventionLabel">Update Intervention</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateIntForm">
                    <input type="hidden" id="intervention_id" name="intervention_id">

                    <!-- Intervention Name Input (Display only, not included in form submission) -->
                    <div class="mb-3">
                        <label for="intervention_type" class="form-label">Intervention Name</label>
                        <input type="text" id="intervention_type" name="intervention_name" class="form-control" required disabled>
                    </div>

                    <!-- Seedling Type Input (Display only, not included in form submission) -->
                    <div class="mb-3">
                        <label for="seed_type" class="form-label">Seedling Type</label>
                        <input type="text" id="seed_type" name="seed_name" class="form-control" required disabled>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description" class="form-control" required></textarea>
                    </div>

                    <!-- Quantity -->
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" required>
                    </div>

                    <!-- Quantity Left -->
                    <div class="mb-3">
                        <label for="quantity_left" class="form-label">Quantity Left</label>
                        <input type="number" id="quantity_left" name="quantity_left" class="form-control" required>
                    </div>

                    <!-- Indicator -->
                    <div class="mb-3">
                        <label for="unit_name" class="form-label">Indicator</label>
                        <select class="form-control" id="unit_name" name="unit" required>
                            <option value="" disabled>Select Indicator</option>
                            <?php
                            include('../conn.php');
                            $query = "SELECT unit_id, unit_name FROM tbl_unit ORDER BY unit_name ASC";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($row['unit_id']) . "'>" . htmlspecialchars($row['unit_name']) . "</option>";
                                }
                            } else {
                                echo "<option value='' disabled>No units available</option>";
                            }
                            $conn->close();
                            ?>
                        </select>
                    </div>
                    <!-- Hidden Unit ID (For Form Submission) -->
                    <input type="hidden" id="unit_id" name="unit_id">

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="updateIntForm" class="btn btn-success">Update</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById("interventionQty").addEventListener("keydown", function (event) {
    if (event.key === "e" || event.key === "E" || event.key === "+" || event.key === "-") {
        event.preventDefault();
    }
});

document.querySelectorAll("#quantity, #quantity_left").forEach((input) => {
    input.addEventListener("keydown", function (event) {
        if (event.key === "e" || event.key === "E" || event.key === "+" || event.key === "-") {
            event.preventDefault();
        }
    });
});
</script>
