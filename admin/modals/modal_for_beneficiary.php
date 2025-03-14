<!-- Add Beneficiary Modal -->
<div class="modal fade" id="addBeneficiaryModal" tabindex="-1" aria-labelledby="addBeneficiaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBeneficiaryModalLabel">Add Beneficiary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addBeneficiaryForm" method="POST" action="add_beneficiary.php">
                    <div class="row">
                        <!-- Beneficiary Type -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Beneficiary Type</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="individual" name="beneficiary_type" value="Individual" required>
                                <label class="form-check-label" for="individual">Individual</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="group" name="beneficiary_type" value="Group" required>
                                <label class="form-check-label" for="group">Group</label>
                            </div>
                        </div>

                        <!-- Individual Type (Hidden by Default) -->
                        <div class="col-12 mb-3" id="individualTypeRadio" style="display: none;">
                            <label class="form-label">Individual Type</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="farmer" name="individual_type" value="Farmer">
                                <label class="form-check-label" for="farmer">Farmer</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="fisher" name="individual_type" value="Fisher">
                                <label class="form-check-label" for="fisher">Fisher</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="aew" name="individual_type" value="AEW">
                                <label class="form-check-label" for="aew">AEW</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="others" name="individual_type" value="Others">
                                <label class="form-check-label" for="others">Others</label>
                            </div>
                            <!-- Input field for specifying "Others" -->
                            <div class="mt-2" id="othersInput" style="display: none;">
                                <label for="others_specify" class="form-label">Please Specify</label>
                                <input type="text" class="form-control" id="others_specify" name="others_specify">
                            </div>
                        </div>

                        <!-- Group Type (Hidden by Default) -->
                        <div class="col-12 mb-3" id="groupTypeRadio" style="display: none;">
                            <label class="form-label">Group Type</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="fca" name="group_type" value="FCA">
                                <label class="form-check-label" for="fca">FCA</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="cluster" name="group_type" value="Cluster">
                                <label class="form-check-label" for="cluster">Cluster</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="lgu" name="group_type" value="LGU">
                                <label class="form-check-label" for="lgu">LGU</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="school" name="group_type" value="School">
                                <label class="form-check-label" for="school">School</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="group_others" name="group_type" value="Others">
                                <label class="form-check-label" for="group_others">Others</label>
                            </div>
                            <!-- Input field for specifying "Others" -->
                            <div class="mt-2" id="groupOthersInput" style="display: none;">
                                <label for="group_others_specify" class="form-label">Please Specify</label>
                                <input type="text" class="form-control" id="group_others_specify" name="group_others_specify">
                            </div>
                        </div>

                        <!-- Cooperative Input (Hidden by Default) -->
                        <div class="col-12 mb-3" id="cooperativeInput" style="display: none;">
                            <label for="cooperative" class="form-label">Cooperative</label>
                            <select class="form-control" id="cooperative" name="cooperative">
                                <option value="" disabled selected>Select a Cooperative</option>
                                <?php
                                // Fetch cooperatives from the database
                                include('../conn.php');

                                if (!isset($_SESSION['uid'])) {
                                    die("User ID not found in session.");
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

                        <!-- Beneficiary Details -->
                        <div class="col-12 mb-3">
                            <label for="beneficiary_first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="beneficiary_first_name" name="beneficiary_first_name" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="beneficiary_middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="beneficiary_middle_name" name="beneficiary_middle_name">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="beneficiary_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="beneficiary_last_name" name="beneficiary_last_name" required>
                        </div>

                        <!-- Address Fields -->
                        <div class="col-12 mb-3">
                            <label for="province">Province</label>
                            <select id="province" class="form-control" name="province" required>
                                <option selected disabled>Select a province</option>
                                <!-- Populate dynamically using JavaScript or PHP -->
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="municipality">Municipality</label>
                            <select id="municipality" class="form-control" name="municipality" required>
                                <option selected disabled>Select a municipality</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="barangay">Barangay</label>
                            <select id="barangay" class="form-control" name="barangay" required>
                                <option selected disabled>Select a barangay</option>
                            </select>
                        </div>

                        <!-- Additional Fields -->
                        <div class="col-12 mb-3">
                            <label for="rsbsa_no" class="form-label">RSBSA No.</label>
                            <input type="text" class="form-control" id="rsbsa_no" name="rsbsa_no">
                            <small class="form-text text-muted" style="font-size: 1.1em;">
                                If you don't know your RSBSA No.,
                                <a href="https://finder-rsbsa.da.gov.ph/?fbclid=IwY2xjawI9yR5leHRuA2FlbQIxMAABHUH8-YVy-cRpNVJgrzYznFQpQhWH_XMvVASmOru156UDC97RjJKjxmYLAg_aem_Yg-2xPtYXEe4FvX8p4VcJg"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    style="font-size: 1.1em; text-decoration: underline; color: blue;">
                                    click here
                                </a>.
                            </small>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Sex</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="sex_male" name="sex" value="Male" required>
                                <label class="form-check-label" for="sex_male">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="sex_female" name="sex" value="Female" required>
                                <label class="form-check-label" for="sex_female">Female</label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="birthdate" class="form-label">Birthdate</label>
                            <input type="date" class="form-control" id="birthdate" name="birthdate" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" required minlength="11" maxlength="11" pattern="\d{11}" title="Please enter a valid 11-digit contact number">
                        </div>
                        <!-- Check if applicable -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Select if Applicable</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="arb" name="applicable[]" value="ARB">
                                <label class="form-check-label" for="arb">ARB</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="ips" name="applicable[]" value="IPs">
                                <label class="form-check-label" for="ips">IPs</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="pwd" name="applicable[]" value="PWD">
                                <label class="form-check-label" for="pwd">PWD</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="4ps" name="applicable[]" value="4Ps">
                                <label class="form-check-label" for="4ps">4Ps</label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Add Beneficiary</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Show/hide individual or group fields based on beneficiary type
        $('input[name="beneficiary_type"]').change(function() {
            if ($(this).val() === 'Individual') {
                $('#individualTypeRadio').show(); // Show individual type radio buttons
                $('#groupTypeRadio').hide(); // Hide group type radio buttons
                $('#cooperativeInput').hide(); // Hide cooperative input for individual
            } else if ($(this).val() === 'Group') {
                $('#individualTypeRadio').hide(); // Hide individual type radio buttons
                $('#groupTypeRadio').show(); // Show group type radio buttons
                $('#cooperativeInput').show(); // Show cooperative input for group
            }
        });

        // Show/hide "Others" input for individual type
        $('input[name="individual_type"]').change(function() {
            if ($(this).val() === 'Others') {
                $('#othersInput').show();
            } else {
                $('#othersInput').hide();
            }
        });

        // Show/hide "Others" input for group type
        $('input[name="group_type"]').change(function() {
            if ($(this).val() === 'Others') {
                $('#groupOthersInput').show();
            } else {
                $('#groupOthersInput').hide();
            }
        });
    });
</script>