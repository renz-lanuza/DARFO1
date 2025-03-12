<style>
    .modal-header {
        background-color: #0D7C66;
        color: white;
    }

    /* Change table header background */
    #interventionTable thead {
        background-color: #0D7C66;
        /* Custom Green */
        color: white;
    }

    /* Change table body background */
    #interventionTable tbody tr {
        background-color: #E8F6F3;
        /* Light Greenish */
    }

    #interventionTable tbody tr:hover {
        background-color: #D1ECE4;
        /* Slightly Darker Green on Hover */
    }

    /* Customize border */
    #interventionTable {
        border: 1px solid #0D7C66;
    }
</style>
<!-- Add Distribution Modal -->
<div class="modal fade" id="addDistributionModal" tabindex="-1" aria-labelledby="addDistributionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDistributionModalLabel">Add Distribution</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addDistributionForm" method="POST">
                    <div class="row">
                        <!-- Type of Distribution -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Type of Distribution</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="individual" name="type_of_distribution" value="Individual" required>
                                <label class="form-check-label" for="individual">Individual</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="group" name="type_of_distribution" value="Group" required>
                                <label class="form-check-label" for="group">Group</label>
                            </div>
                        </div>
                        <div class="col-12 mb-3" id="cooperativeDropdown" style="display: none;">
                            <label for="cooperative" class="form-label">Cooperative</label>
                            <select class="form-control" id="cooperative" name="cooperative_id">
                                <option value="" disabled selected>Select a Cooperative</option>
                                <?php
                                // Fetch cooperatives from the database
                                include('../conn.php');

                                if (!isset($_SESSION['uid'])) {
                                    die("User  ID not found in session.");
                                }
                                $uid = $_SESSION['uid'];
                                $stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
                                if ($stationQuery === false) {
                                    die("Prepare failed: " . $conn->error);
                                }
                                $stationQuery->bind_param("i", $uid);
                                $stationQuery->execute();
                                $stationQuery->bind_result($stationId);
                                $stationQuery->fetch();
                                $stationQuery->close();

                                if ($stationId === null) {
                                    die("Station ID not found for the user.");
                                }

                                $cooperativeQuery = "SELECT coop_id, cooperative_name FROM tbl_cooperative WHERE station_id = ?";
                                $stmt = $conn->prepare($cooperativeQuery);
                                if ($stmt === false) {
                                    die("Prepare failed: " . $conn->error);
                                }
                                $stmt->bind_param("i", $stationId);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result === false) {
                                    die("Query failed: " . $conn->error);
                                }

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . $row["coop_id"] . '">' . $row["cooperative_name"] . '</option>';
                                    }
                                } else {
                                    echo '<option value="" disabled>No cooperatives available</option>';
                                }

                                $stmt->close();
                                $conn->close();
                                ?>
                            </select>
                        </div>

                        <div class="col-12 mb-3 position-relative">
                            <label for="beneficiary_first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="beneficiary_first_name" name="beneficiary_first_name" required>
                        </div>
                        <div class="col-12 mb-3 position-relative">
                            <label for="beneficiary_middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="beneficiary_middle_name" name="beneficiary_middle_name">
                        </div>
                        <div class="col-12 mb-3 position-relative">
                            <label for="beneficiary_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="beneficiary_last_name" name="beneficiary_last_name" required>
                        </div>

                        <!-- Province -->
                        <div class="col-12 mb-3">
                            <label for="province">Province</label>
                            <select id="province" class="form-control" name="provinceCode" required>
                                <option selected disabled>Select a province</option>
                            </select>
                        </div>

                        <!-- Municipality -->
                        <div class="col-12 mb-3">
                            <label for="municipality">Municipality</label>
                            <select id="municipality" class="form-control" name="municipalityCode" required>
                                <option selected disabled>Select a municipality</option>
                            </select>
                        </div>

                        <!-- Barangay -->
                        <div class="col-12 mb-3">
                            <label for="barangay">Barangay</label>
                            <select id="barangay" class="form-control" name="barangayCode" required>
                                <option selected disabled>Select a barangay</option>
                            </select>
                        </div>
                        <!-- Date of Distribution -->
                        <div class="col-12 mb-3">
                            <label for="distribution_date" class="form-label">Date of Distribution</label>
                            <input type="date" class="form-control" id="distribution_date" name="distribution_date" required>
                        </div>


                    </div>

                    <!-- Table for Interventions -->
                    <div class="mb-3">
                        <label class="form-label">Intervention Details</label>
                        <table class="table table-bordered" id="interventionTable">
                            <thead>
                                <tr>
                                    <th>Intervention Name</th>
                                    <th>Seedling Type</th>
                                    <th>Quantity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <?php
                                        // Check if the user is logged in
                                        if (!isset($_SESSION['uid'])) {
                                            die("User   ID not found in session.");
                                        }

                                        $uid = $_SESSION['uid']; // Get the uid from the session

                                        // Connect to the database
                                        $conn = mysqli_connect("localhost", "root", "", "db_darfo1");

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

                                        // Fetch intervention names from the database filtered by station_id
                                        $sql = "SELECT int_type_id, intervention_name FROM tbl_intervention_type WHERE station_id = ? ORDER BY int_type_id";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param("i", $stationId);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        ?>

                                        <select class="form-control intervention_name_distri" name="intervention_name_distri[]" required>
                                            <option value="" disabled selected>Select Intervention</option>
                                            <?php
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<option value='{$row['int_type_id']}'>" . htmlspecialchars($row['intervention_name']) . "</option>";
                                                }
                                            } else {
                                                echo "<option value='' disabled>No interventions available for this station</option>";
                                            }
                                            ?>
                                        </select>

                                        <?php
                                        // Close the database connection
                                        $conn->close();
                                        ?>
                                    </td>
                                    <td>
                                        <select class="form-control seedling_type_distri" name="seedling_type_distri[]" required>
                                            <option value="" disabled selected>Select Seedling Type</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="quantity_distri[]" required>
                                        <small class="form-text text-muted quantity-left">Quantity Left: 0</small>
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-primary btn-sm" id="addRowButton">Add Row</button>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Show or hide the cooperative dropdown based on the selected radio button
        $('input[name="type_of_distribution"]').change(function() {
            if ($(this).val() === 'Group') {
                $('#cooperativeDropdown').show();
            } else {
                $('#cooperativeDropdown').hide();
            }
        });
    });
</script>

<style>
    /* Modal Header Styling */
    .modal-header {
        background-color: #0D7C66;
        color: white;
    }

    /* Table Styling */
    #updateinterventionTable thead {
        background-color: #0D7C66;
        color: white;
    }

    #updateinterventionTable tbody tr {
        background-color: #E8F6F3;
    }

    #updateinterventionTable tbody tr:hover {
        background-color: #D1ECE4;
    }
</style>

<!-- Update Distribution Modal -->
<div class="modal fade" id="updateDistributionModal" tabindex="-1" aria-labelledby="updateDistributionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateDistributionModalLabel">Update Distribution</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateDistributionForm" method="POST">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Type of Distribution</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="update_individual" name="type_of_distribution" value="Individual" required>
                                <label class="form-check-label" for="update_individual">Individual</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="update_group" name="type_of_distribution" value="Group" required>
                                <label class="form-check-label" for="update_group">Group</label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="update_beneficiary_name" class="form-label">Beneficiary Name</label>
                            <input type="text" class="form-control" id="update_beneficiary_name" name="update_beneficiary_name" required autocomplete="off">
                        </div>

                        <div class="col-12 mb-3">
                            <label for="update_province">Province</label>
                            <select id="update_province" class="form-control" name="update_province" required></select>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="update_municipality">Municipality</label>
                            <select id="update_municipality" class="form-control" name="update_municipality" required></select>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="update_barangay">Barangay</label>
                            <select id="update_barangay" class="form-control" name="update_barangay" required></select>
                        </div>
                    </div>

                    <!-- Single Row Table for Interventions -->
                    <div class="mb-3">
                        <label class="form-label">Intervention Details</label>
                        <table class="table table-bordered" id="updateinterventionTable">
                            <thead>
                                <tr>
                                    <th>Intervention Name</th>
                                    <th>Classification</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                    <?php
                                        // Check if the user is logged in
                                        if (!isset($_SESSION['uid'])) {
                                            die("User  ID not found in session.");
                                        }

                                        $uid = $_SESSION['uid']; // Get the uid from the session

                                        // Connect to the database
                                        $conn = mysqli_connect("localhost", "root", "", "db_darfo1");

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

                                        // Fetch intervention names from the database filtered by station_id
                                        $sql = "SELECT int_type_id, intervention_name FROM tbl_intervention_type WHERE station_id = ? ORDER BY int_type_id";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param("i", $stationId);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        ?>

                                        <select class="form-control intervention_name_distrib" name="intervention_name_distrib[]" required>
                                            <option value="" disabled selected>Select Intervention:</option>
                                            <?php
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<option value='{$row['int_type_id']}'>" . htmlspecialchars($row['intervention_name']) . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>


                                        <?php
                                        // Close the database connection
                                        $conn->close();
                                        ?>

                                    </td>
                                    <td>
                                        <select class="form-control seedling_type_distrib" id="seedling_type_distrib" name="seedling_type_distrib" required>
                                            <option value="" disabled selected>Select Classification:</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" name="update_quantity[]" required>
                                        <small class="form-text text-muted">Quantity Left: <span class="quantity-left">0</span></small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
