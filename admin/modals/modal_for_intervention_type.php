<!-- Add Intervention Type Modal -->
<div class="modal fade" id="addInterventionTypeModal" tabindex="-1" aria-labelledby="addInterventionTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Large modal for better visibility -->
        <div class="modal-content">
            <div class="modal-header" style="background-color: #0D7C66; color: white;">
                <h5 class="modal-title" id="addInterventionTypeModalLabel">Add Intervention Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addInterventionForm">
                    <div class="mb-3">
                        <label for="interventionName" class="form-label">Intervention Name</label>
                            <input type="text" class="form-control" id="interventionName" name="interventionName" placeholder="Enter Intervention Name" required>
                            <!-- <div id="nameHelp" class="form-text">Provide a unique name for the intervention.</div> -->
                        <span id="nameFeedback" class="text-danger small" style="display: none;">Intervention name already exists.</span> <!-- Validation Message -->
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $("#interventionName").on("input", function () {
            var interventionName = $(this).val().trim();

            if (interventionName.length > 2) { // Validate only after 3+ characters
                $.ajax({
                    url: "4InterventionTypeManagement/check_interventiontype.php",
                    type: "POST",
                    data: { interventionName: interventionName },
                    success: function (response) {
                        if (response === "exists") {
                            $("#interventionName").addClass("is-invalid");
                            $("#nameFeedback").text("Intervention name already exists.").show();
                            $(".btn-success").prop("disabled", true);
                        } else {
                            $("#interventionName").removeClass("is-invalid");
                            $("#nameFeedback").hide();
                            $(".btn-success").prop("disabled", false);
                        }
                    }
                });
            } else {
                $("#interventionName").removeClass("is-invalid");
                $("#nameFeedback").hide();
                $(".btn-success").prop("disabled", false);
            }
        });
    });
</script>


<!-- Update Intervention Type Modal -->
<div class="modal fade" id="updateInterventionTypeModal" tabindex="-1" aria-labelledby="updateInterventionTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Large modal for better visibility -->
        <div class="modal-content">
            <div class="modal-header" style="background-color: #0D7C66; color: white;">
                <h5 class="modal-title" id="updateInterventionTypeModalLabel">Update Intervention Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateInterventionForm">
                    <!-- Hidden field to hold the intervention type id -->
                    <input type="hidden" id="updateIntTypeId" name="int_type_id">

                    <div class="mb-3">
                        <label for="updateInterventionName" class="form-label">Intervention Name</label>
                            <input type="text" class="form-control" id="updateInterventionName" name="intervention_name" placeholder="Enter Intervention Name" required>
                        <span id="updateNameFeedback" class="text-danger small" style="display: none;">Intervention name already exists.</span>
                    </div>

                    <!-- Validation feedback on input -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span id="updateNameFeedback" class="text-danger" style="display: none;">Please provide a valid intervention name.</span>
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

<script>
    $(document).ready(function () {
        $("#updateInterventionName").on("input", function () {
            var interventionName = $(this).val().trim();
            var intTypeId = $("#updateIntTypeId").val(); // Get the current intervention ID

            if (interventionName.length > 2) { // Validate only after 3+ characters
                $.ajax({
                    url: "4InterventionTypeManagement/check_update_intervention.php",
                    type: "POST",
                    data: { interventionName: interventionName, int_type_id: intTypeId },
                    success: function (response) {
                        if (response === "exists") {
                            $("#updateInterventionName").addClass("is-invalid");
                            $("#updateNameFeedback").text("Intervention name already exists.").show();
                            $(".btn-success").prop("disabled", true);
                        } else {
                            $("#updateInterventionName").removeClass("is-invalid");
                            $("#updateNameFeedback").hide();
                            $(".btn-success").prop("disabled", false);
                        }
                    }
                });
            } else {
                $("#updateInterventionName").removeClass("is-invalid");
                $("#updateNameFeedback").hide();
                $(".btn-success").prop("disabled", false);
            }
        });
    });
</script>

<style>
    .modal-lg {
        max-width: 50%;
        /* Adjusted size for better visibility */
    }

    .form-control:invalid {
        border-color: #e74c3c;
        /* Red border for invalid input */
    }

    .modal-header {
        background-color: #0D7C66;
        color: white;
    }

    .modal-footer {
        padding-top: 1rem;
    }
</style>
