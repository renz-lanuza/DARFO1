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
                            <label class="form-label">Beneficiary Type</label><span class="text-danger">*</span><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="individual" name="beneficiary_category" value="Individual" required>
                                <label class="form-check-label" for="individual">Individual</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="group" name="beneficiary_category" value="Group" required>
                                <label class="form-check-label" for="group">Group</label>
                            </div>
                        </div>

                        <!-- Individual Type (Hidden by Default) -->
                        <div class="col-12 mb-3" id="individualTypeRadio" style="display: none;">
                            <label class="form-label">Individual Type</label><span class="text-danger">*</span><br>
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
                                <label for="others_specify" class="form-label">Please Specify</label><span class="text-danger">*</span>
                                <input type="text" class="form-control" id="others_specify" name="others_specify">
                            </div>
                        </div>

                        <!-- Group Type (Hidden by Default) -->
                        <div class="col-12 mb-3" id="groupTypeRadio" style="display: none;">
                            <label class="form-label">Group Type</label><span class="text-danger">*</span><br>
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
                                <label for="group_others_specify" class="form-label">Please Specify</label><span class="text-danger">*</span>
                                <input type="text" class="form-control" id="group_others_specify" name="group_others_specify">
                            </div>
                        </div>

                        <!-- Cooperative Input (Hidden by Default) -->
                        <div class="col-12 mb-3" id="cooperativeInput" style="display: none;">
                            <label for="cooperative" class="form-label">Cooperative</label><span class="text-danger">*</span>
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
                            <label for="beneficiary_first_name" class="form-label">First Name</label><span class="text-danger">*</span>
                            <input type="text" class="form-control" id="beneficiary_first_name" name="beneficiary_first_name" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="beneficiary_middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="beneficiary_middle_name" name="beneficiary_middle_name">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="beneficiary_last_name" class="form-label">Last Name</label><span class="text-danger">*</span>
                            <input type="text" class="form-control" id="beneficiary_last_name" name="beneficiary_last_name" required>
                        </div>

                        <!-- Address Fields -->
                        <div class="col-12 mb-3">
                            <label for="province">Province</label><span class="text-danger">*</span>
                            <select id="province" class="form-control" name="province" required>
                                <option selected disabled>Select a province</option>
                                <!-- Populate dynamically using JavaScript or PHP -->
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="municipality">Municipality</label><span class="text-danger">*</span>
                            <select id="municipality" class="form-control" name="municipality" required>
                                <option selected disabled>Select a municipality</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="barangay">Barangay</label><span class="text-danger">*</span>
                            <select id="barangay" class="form-control" name="barangay" required>
                                <option selected disabled>Select a barangay</option>
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="streetPurok">Street/Purok</label>
                            <input type="text" id="streetPurok" class="form-control" name="streetPurok">
                        </div>

                        
                        <div class="col-12 mb-3">
                            <label for="rsbsa-no">RSBSA No.</label>
                            <input type="text" class="form-control" name="rsbsa_no" placeholder="(e.g. 01-33-10-001-000000)"
                            
                                id="rsbsa-no" oninput="formatRSBSA(this)" required maxlength="19">
                                <small class="form-text"></small> <!-- Add this for feedback -->
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

                        <script>
                            function formatRSBSA(input) {
                                // Remove all non-digit characters
                                let value = input.value.replace(/\D+/g, '');

                                // Format the value as 01-33-10-001-000000
                                let dashedValue = '';
                                if (value.length > 0) {
                                    dashedValue += value.substring(0, 2);
                                }
                                if (value.length > 2) {
                                    dashedValue += '-' + value.substring(2, 4);
                                }
                                if (value.length > 4) {
                                    dashedValue += '-' + value.substring(4, 6);
                                }
                                if (value.length > 6) {
                                    dashedValue += '-' + value.substring(6, 9);
                                }
                                if (value.length > 9) {
                                    dashedValue += '-' + value.substring(9, 15);
                                }

                                // Set the formatted value back to the input
                                input.value = dashedValue;

                            }
                        </script>
                        <div class="col-12 mb-3">
                            <label class="form-label">Sex</label><span class="text-danger">*</span><br>
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
                            <label for="birthdate" class="form-label">Birthdate</label><span class="text-danger">*</span>
                            <input type="date" class="form-control" id="birthdate" name="birthdate" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="number" class="form-control" id="contact_number" name="contact_number" 
                                required minlength="11" maxlength="11" pattern="\d{11}" title="Please enter a valid 11-digit contact number">
                            <small class="form-text"></small> <!-- Add this for feedback -->
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
                        <button type="submit" id="add-beneficiary-btn" class="btn btn-success">Add Beneficiary</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Show/hide individual or group fields based on beneficiary type
        $('input[name="beneficiary_category"]').change(function() {
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

<script>
document.addEventListener("DOMContentLoaded", function () {
    const rsbsaInput = document.getElementById("rsbsa-no");
    const contactInput = document.getElementById("contact_number");
    const submitButton = document.getElementById("add-beneficiary-btn"); // Button ID

    let rsbsaValid = true; 
    let contactValid = true; 

    function validateInput(inputElement, fieldType) {
        let value = inputElement.value.trim();
        
        // Remove dashes from RSBSA No. before validation
        if (fieldType === "rsbsa_no") {
            value = value.replace(/-/g, "");
        }

        if (value === "") return; // Stop if the input is empty

        fetch("8beneficiaryManagement/validate_inputs.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `${fieldType}=${encodeURIComponent(value)}`
        })
        .then(response => response.json())
        .then(data => {
            const feedback = inputElement.nextElementSibling;
            if (!feedback || !feedback.classList.contains("form-text")) return;

            if (data.error) {
                console.error("Validation Error:", data.error);
                feedback.textContent = "An error occurred. Please try again.";
                feedback.style.color = "red";
                disableSubmitButton(true);
                return;
            }

            if (data.exists) {
                inputElement.classList.add("is-invalid");
                inputElement.classList.remove("is-valid");
                feedback.textContent = data.message;
                feedback.style.color = "red";

                if (fieldType === "rsbsa_no") rsbsaValid = false;
                if (fieldType === "contact_no") contactValid = false;
            } else {
                inputElement.classList.remove("is-invalid");
                inputElement.classList.add("is-valid");
                feedback.textContent = "";

                if (fieldType === "rsbsa_no") rsbsaValid = true;
                if (fieldType === "contact_no") contactValid = true;
            }

            // Check if both fields are valid before enabling the button
            disableSubmitButton(!(rsbsaValid && contactValid));
        })
        .catch(error => {
            console.error("Fetch Error:", error);
        });
    }

    function disableSubmitButton(state) {
        submitButton.disabled = state; // Disable button if validation fails
    }

    rsbsaInput.addEventListener("input", () => validateInput(rsbsaInput, "rsbsa_no"));
    contactInput.addEventListener("input", () => validateInput(contactInput, "contact_no"));
});
</script>

<!-- Update Beneficiary Modal -->
<div class="modal fade" id="updateBeneficiaryModal" tabindex="-1" aria-labelledby="updateBeneficiaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateBeneficiaryModalLabel">Update Beneficiary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateBeneficiaryForm" method="POST" action="update_beneficiary.php">
                    <!-- Hidden input for beneficiary ID -->
                    <input type="hidden" id="beneficiary_id" name="beneficiary_id">

                    <!-- Rest of the form fields (same as the Add Beneficiary form) -->
                    <div class="row">
                        <!-- Beneficiary Type -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Beneficiary Type</label><span class="text-danger">*</span><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="update_individual" name="beneficiary_category" value="Individual" required>
                                <label class="form-check-label" for="update_individual">Individual</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="update_group" name="beneficiary_category" value="Group" required>
                                <label class="form-check-label" for="update_group">Group</label>
                            </div>
                        </div>

                    <!-- Individual Type -->
                    <div class="col-12 mb-3" id="updateIndividualTypeRadio" style="display: none;">
                        <label class="form-label">Individual Type</label><span class="text-danger">*</span><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="update_farmer" name="individual_type" value="Farmer">
                            <label class="form-check-label" for="update_farmer">Farmer</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="update_fisher" name="individual_type" value="Fisher">
                            <label class="form-check-label" for="update_fisher">Fisher</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="update_aew" name="individual_type" value="AEW">
                            <label class="form-check-label" for="update_aew">AEW</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="update_others" name="individual_type" value="Others">
                            <label class="form-check-label" for="update_others">Others</label>
                        </div>
                        <!-- Input field for specifying "Others" -->
                        <div class="mt-2" id="updateOthersInput" style="display: none;">
                            <label for="update_others_specify" class="form-label">Please Specify</label><span class="text-danger">*</span>
                            <input type="text" class="form-control" id="update_others_specify" name="others_specify">
                        </div>
                    </div>

                    <!-- Group Type -->
                    <div class="col-12 mb-3" id="updateGroupTypeRadio" style="display: none;">
                        <label class="form-label">Group Type</label><span class="text-danger">*</span><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="update_fca" name="group_type" value="FCA">
                            <label class="form-check-label" for="update_fca">FCA</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="update_cluster" name="group_type" value="Cluster">
                            <label class="form-check-label" for="update_cluster">Cluster</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="update_lgu" name="group_type" value="LGU">
                            <label class="form-check-label" for="update_lgu">LGU</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="update_school" name="group_type" value="School">
                            <label class="form-check-label" for="update_school">School</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="update_group_others" name="group_type" value="Others">
                            <label class="form-check-label" for="update_group_others">Others</label>
                        </div>
                        <!-- Input field for specifying "Others" -->
                        <div class="mt-2" id="updateGroupOthersInput" style="display: none;">
                            <label for="update_group_others_specify" class="form-label">Please Specify</label><span class="text-danger">*</span>
                            <input type="text" class="form-control" id="update_group_others_specify" name="group_others_specify">
                        </div>
                    </div>
                        <!-- Cooperative Input (Hidden by Default) -->
                        <div class="col-12 mb-3" id="updateCooperativeInput" style="display: none;">
                            <label for="update_cooperative" class="form-label">Cooperative</label><span class="text-danger">*</span>
                            <select class="form-control" id="update_cooperative" name="cooperative">
                                <option value="" disabled selected>Select a Cooperative</option>
                                <!-- Populate dynamically using PHP or JavaScript -->
                            </select>
                        </div>

                        <!-- Beneficiary Details -->
                        <div class="col-12 mb-3">
                            <label for="update_beneficiary_first_name" class="form-label">First Name</label><span class="text-danger">*</span>
                            <input type="text" class="form-control" id="update_beneficiary_first_name" name="beneficiary_first_name" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="update_beneficiary_middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="update_beneficiary_middle_name" name="beneficiary_middle_name">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="update_beneficiary_last_name" class="form-label">Last Name</label><span class="text-danger">*</span>
                            <input type="text" class="form-control" id="update_beneficiary_last_name" name="beneficiary_last_name" required>
                        </div>

                        <!-- Address Fields -->
                        <div class="col-12 mb-3">
                            <label for="update_province_name">Province</label><span class="text-danger">*</span>
                            <select id="update_province_name" class="form-control" name="update_province_name" required>
                                <option selected disabled>Select a province</option>
                                <!-- Populate dynamically using JavaScript or PHP -->
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="update_municipality_name">Municipality</label><span class="text-danger">*</span>
                            <select id="update_municipality_name" class="form-control" name="update_municipality_name" required>
                                <option selected disabled>Select a municipality</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="update_barangay_name">Barangay</label><span class="text-danger">*</span>
                            <select id="update_barangay_name" class="form-control" name="update_barangay_name" required>
                                <option selected disabled>Select a barangay</option>
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="update_streetPurok">Street/Purok</label>
                            <input type="text" id="update_streetPurok" class="form-control" name="streetPurok">
                        </div>

                        <div class="col-12 mb-3">
                            <label for="rsbsa-no">RSBSA No.</label>
                            <input type="text" class="form-control" name="up_rsbsa_no" placeholder="(e.g. 01-33-10-001-000000)"
                            
                                id="update_rsbsa-no" oninput="formatRSBSA(this)" required maxlength="19">
                                <small class="form-text"></small> <!-- Add this for feedback -->
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

                        <script>
                            function formatRSBSA(input) {
                                // Remove all non-digit characters
                                let value = input.value.replace(/\D+/g, '');

                                // Format the value as 01-33-10-001-000000
                                let dashedValue = '';
                                if (value.length > 0) {
                                    dashedValue += value.substring(0, 2);
                                }
                                if (value.length > 2) {
                                    dashedValue += '-' + value.substring(2, 4);
                                }
                                if (value.length > 4) {
                                    dashedValue += '-' + value.substring(4, 6);
                                }
                                if (value.length > 6) {
                                    dashedValue += '-' + value.substring(6, 9);
                                }
                                if (value.length > 9) {
                                    dashedValue += '-' + value.substring(9, 15);
                                }

                                // Set the formatted value back to the input
                                input.value = dashedValue;

                            }
                        </script>
                        <!-- Sex -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Sex</label><span class="text-danger">*</span><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="update_sex_male" name="up_sex" value="Male" required>
                                <label class="form-check-label" for="update_sex_male">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="update_sex_female" name="up_sex" value="Female" required>
                                <label class="form-check-label" for="update_sex_female">Female</label>
                            </div>
                        </div>


                        <!-- Birthdate -->
                        <div class="col-12 mb-3">
                            <label for="update_birthdate" class="form-label">Birthdate</label><span class="text-danger">*</span>
                            <input type="date" class="form-control" id="update_birthdate" name="birthdate" required>
                        </div>

                        <!-- Contact Number -->
                        <div class="col-12 mb-3">
                            <label for="update_contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="update_contact_number" name="contact_number" 
                                required minlength="11" maxlength="11" pattern="\d{11}" title="Please enter a valid 11-digit contact number">
                            <small class="form-text"></small>
                        </div>

                        <!-- Check if applicable -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Select if Applicable</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="update_arb" name="applicable[]" value="ARB">
                                <label class="form-check-label" for="update_arb">ARB</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="update_ips" name="applicable[]" value="IPs">
                                <label class="form-check-label" for="update_ips">IPs</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="update_pwd" name="applicable[]" value="PWD">
                                <label class="form-check-label" for="update_pwd">PWD</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="update_4ps" name="applicable[]" value="4Ps">
                                <label class="form-check-label" for="update_4ps">4Ps</label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="update-beneficiary-btn" class="btn btn-success">Update Beneficiary</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {
    // Get elements
    const individualRadio = document.getElementById("update_individual");
    const groupRadio = document.getElementById("update_group");
    const individualTypeDiv = document.getElementById("updateIndividualTypeRadio");
    const groupTypeDiv = document.getElementById("updateGroupTypeRadio");
    const cooperativeDiv = document.getElementById("updateCooperativeInput");

    // Individual Type "Others" elements
    const individualOthersRadio = document.getElementById("update_others");
    const individualTypeRadios = document.querySelectorAll("input[name='individual_type']");
    const individualOthersInput = document.getElementById("updateOthersInput");

    // Group Type "Others" elements
    const groupOthersRadio = document.getElementById("update_group_others");
    const groupTypeRadios = document.querySelectorAll("input[name='group_type']");
    const groupOthersInput = document.getElementById("updateGroupOthersInput");

    // Function to show/hide fields based on Individual or Group selection
    function toggleBeneficiaryType() {
        if (individualRadio.checked) {
            individualTypeDiv.style.display = "block";  // Show Individual Type options
            groupTypeDiv.style.display = "none";
            cooperativeDiv.style.display = "none";
            groupOthersInput.style.display = "none";  // Hide Group "Others" input if switching
            individualOthersInput.style.display = "none"; // Hide Individual "Others" input if switching
        } else if (groupRadio.checked) {
            individualTypeDiv.style.display = "none";
            groupTypeDiv.style.display = "block";  // Show Group Type options
            cooperativeDiv.style.display = "block";  // Show Cooperative dropdown
            individualOthersInput.style.display = "none"; // Hide Individual "Others" input if switching
        } else {
            individualTypeDiv.style.display = "none";
            groupTypeDiv.style.display = "none";
            cooperativeDiv.style.display = "none";
            individualOthersInput.style.display = "none"; // Ensure Individual "Others" input is hidden
            groupOthersInput.style.display = "none"; // Ensure Group "Others" input is hidden
        }
    }

    // Function to show/hide "Please Specify" input when "Others" is selected in Individual Type
    function toggleIndividualOthersInput() {
        if (individualOthersRadio.checked) {
            individualOthersInput.style.display = "block";  // Show "Please Specify" input for Individual
        } else {
            individualOthersInput.style.display = "none";  // Hide it otherwise
        }
    }

    // Function to show/hide "Please Specify" input when "Others" is selected in Group Type
    function toggleGroupOthersInput() {
        if (groupOthersRadio.checked) {
            groupOthersInput.style.display = "block";  // Show "Please Specify" input for Group
        } else {
            groupOthersInput.style.display = "none";  // Hide it otherwise
        }
    }

    // Add event listeners
    individualRadio.addEventListener("change", toggleBeneficiaryType);
    groupRadio.addEventListener("change", toggleBeneficiaryType);
    
    // Add event listeners for individual type radio buttons
    individualTypeRadios.forEach(radio => {
        radio.addEventListener("change", toggleIndividualOthersInput);
    });

    // Add event listeners for group type radio buttons
    groupTypeRadios.forEach(radio => {
        radio.addEventListener("change", toggleGroupOthersInput);
    });

    // Initialize visibility based on preselected values
    toggleBeneficiaryType();
    toggleIndividualOthersInput();
    toggleGroupOthersInput();
});



</script>

