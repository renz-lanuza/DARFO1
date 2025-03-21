<!-- Add Unit Modal -->
<div class="modal fade" id="addUnitModal" tabindex="-1" aria-labelledby="addUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #0D7C66; color: white;">
                <h5 class="modal-title" id="addUnitModalLabel">Add Unit</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addUnitForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="unit_name">Unit Name</label>
                        <input type="text" class="form-control" id="unit_name" name="unit_name" placeholder="Enter unit name" required>
                        <div class="invalid-feedback" id="unitError"></div> <!-- Error Message -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        let existingUnits = []; // Store existing unit names

        // Fetch existing unit names on page load
        $.ajax({
            url: '7unitManagement/validateUnit.php', // PHP script to fetch unit names
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status === "success") {
                    existingUnits = response.data.map(unit => unit.toLowerCase()); // Convert to lowercase for case-insensitive matching
                }
            },
            error: function () {
                console.error("Error fetching unit names.");
            }
        });

        // Validate unit name input
        $('#unit_name').on('input', function () {
            let unitName = $(this).val().trim().toLowerCase();
            let submitButton = $('button[type="submit"]'); // Adjust if needed

            if (existingUnits.includes(unitName)) {
                $('#unit_name').addClass('is-invalid');
                $('#unitError').text("This unit name already exists.");
                submitButton.prop('disabled', true);
            } else {
                $('#unit_name').removeClass('is-invalid');
                $('#unitError').text("");
                submitButton.prop('disabled', false);
            }
        });
    });
</script>




<!-- Update Unit Modal -->
<div class="modal fade" id="updateUnitModal" tabindex="-1" aria-labelledby="updateUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #0D7C66; color: white;">
                <h5 class="modal-title" id="updateUnitModalLabel">Update Unit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateUnitForm">
                    <input type="hidden" id="unit_id" name="unit_id">
                    <div class="form-group">
                        <label for="up_unit_name">Unit Name</label>
                        <input type="text" class="form-control" id="up_unit_name" name="unit_name" required>
                        <div class="invalid-feedback" id="updateUnitError"></div> <!-- Error Message -->
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success" id="updateUnitBtn">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let submitBtn = $("#updateUnitBtn"); // Select the button by ID

    $("#up_unit_name").on("input", function () {
        let unitName = $(this).val().trim();
        let unitId = $("#unit_id").val(); // Get the current unit ID for update
        let errorDiv = $("#updateUnitError");

        if (unitName === "") {
            errorDiv.text(""); // Clear error if input is empty
            $("#up_unit_name").removeClass("is-invalid");
            submitBtn.prop("disabled", true);
            return;
        }

        $.ajax({
            url: "7unitManagement/validateUpdateUnit.php",
            type: "POST",
            data: { unit_name: unitName, unit_id: unitId },
            dataType: "json",
            success: function (response) {
                if (response.exists) {
                    errorDiv.text("Unit name already exists!").show();
                    $("#up_unit_name").addClass("is-invalid");
                    submitBtn.prop("disabled", true); // Disable button if name exists
                } else {
                    errorDiv.text("").hide();
                    $("#up_unit_name").removeClass("is-invalid");
                    submitBtn.prop("disabled", false); // Enable button if name is unique
                }
            },
            error: function () {
                console.error("Error checking unit name.");
                submitBtn.prop("disabled", true); // Disable button on error
            }
        });
    });

    // Disable button initially
    submitBtn.prop("disabled", true);
});
</script>
