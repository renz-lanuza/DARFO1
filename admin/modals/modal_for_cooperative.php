<!-- Modal for Adding Cooperative -->
    <div class="modal fade" id="addCooperativeModal" tabindex="-1" aria-labelledby="addCooperativeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #0D7C66; color: white;">
                    <h5 class="modal-title" id="addCooperativeModalLabel">Add Cooperative</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCooperativeForm">
                        <div class="mb-3">
                            <label for="cooperative_name" class="form-label">Cooperative Name</label>
                                <input type="text" class="form-control" id="cooperative_name" name="cooperative_name" required>
                            <div class="invalid-feedback" id="cooperative_error"></div>
                        </div>
                        <div class="mb-3">
                            <label for="province" class="form-label">Province</label>
                            <select class="form-control" id="province" name="province" required>
                                <option value="" disabled selected>Select Province</option>
                                <!-- Populate dynamically -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="municipality" class="form-label">Municipality</label>
                            <select class="form-control" id="municipality" name="municipality" required>
                                <option value="" disabled selected>Select Municipality</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="barangay" class="form-label">Barangay</label>
                            <select class="form-control" id="barangay" name="barangay" required>
                                <option value="" disabled selected>Select Barangay</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success";>Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#cooperative_name").on("input", function () {
                var cooperativeName = $(this).val().trim();

                if (cooperativeName.length > 2) { // Avoid unnecessary requests for very short input
                    $.ajax({
                        url: "6cooperativeManagement/check_cooperative.php", // PHP script to check the database
                        type: "POST",
                        data: { cooperative_name: cooperativeName },
                        success: function (response) {
                            if (response === "exists") {
                                $("#cooperative_name").addClass("is-invalid");
                                $("#cooperative_error").text("Cooperative name is already taken.");
                                $(".btn-success").prop("disabled", true);
                            } else {
                                $("#cooperative_name").removeClass("is-invalid");
                                $("#cooperative_error").text("");
                                $(".btn-success").prop("disabled", false);
                            }
                        }
                    });
                } else {
                    $("#cooperative_name").removeClass("is-invalid");
                    $("#cooperative_error").text("");
                    $(".btn-success").prop("disabled", false);
                }
            });
        });
    </script>

<style>
    .modal-lg {
        max-width: 40%; /* Adjusted size for better visibility */
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


<!-- Bootstrap 5 Modal -->
<div class="modal fade" id="updateCooperativeModal" tabindex="-1" aria-labelledby="updateCooperativeModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateCooperativeModalLabel">Update Cooperative</h5>
                <button type="button" class="btn-close" id="closeUpdateModalBtn" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="updateCooperativeForm">
                    <input type="hidden" id="update_id">

                    <div class="mb-3">
                        <label for="update_cooperative_name" class="form-label">Cooperative Name</label>
                        <input type="text" class="form-control" id="update_cooperative_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="update_province" class="form-label">Province</label>
                        <select class="form-control" id="update_province" required>
                            <option value="">Select Province</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="update_municipality" class="form-label">Municipality</label>
                        <select class="form-control" id="update_municipality" required>
                            <option value="">Select Municipality</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="update_barangay" class="form-label">Barangay</label>
                        <select class="form-control" id="update_barangay" required>
                            <option value="">Select Barangay</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeUpdateModalBtn2">Close</button>
                <button type="button" class="btn btn-primary" id="updateCooperativeBtn">Update</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $("#update_cooperative_name").on("input", function () {
            var cooperativeName = $(this).val().trim();
            var coopId = $("#coop_id").val(); // Get coop_id from the hidden input

            if (cooperativeName.length > 2) { 
                $.ajax({
                    url: "6cooperativeManagement/upcheck_coopname.php",
                    type: "POST",
                    data: { cooperative_name: cooperativeName, coop_id: coopId },
                    success: function (response) {
                        if (response === "exists") {
                            $("#update_cooperative_name").addClass("is-invalid");
                            $("#updateCooperativeBtn").prop("disabled", true);
                        } else {
                            $("#update_cooperative_name").removeClass("is-invalid");
                            $("#updateCooperativeBtn").prop("disabled", false);
                        }
                    }
                });
            } else {
                $("#update_cooperative_name").removeClass("is-invalid");
                $("#updateCooperativeBtn").prop("disabled", false);
            }
        });
    });
</script>


<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let modalElement = document.getElementById("updateCooperativeModal");
    let modalInstance = new bootstrap.Modal(modalElement);

    // Close modal and remove backdrop properly
    function closeModalAndReload() {
        modalInstance.hide();
        setTimeout(() => {
            document.body.classList.remove("modal-open");
            let backdrop = document.querySelector(".modal-backdrop");
            if (backdrop) {
                backdrop.remove();
            }
            location.reload(); // Reload page after closing
        }, 300);
    }

    // Attach event listeners to all close buttons
    document.querySelectorAll("#closeUpdateModalBtn, #closeUpdateModalBtn2").forEach(button => {
        button.addEventListener("click", closeModalAndReload);
    });

    // Update button event
    document.getElementById("updateCooperativeBtn").addEventListener("click", function (event) {
        event.preventDefault(); // Stop form from reloading

        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to update this cooperative?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, update it!"
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append("update_id", document.getElementById("update_id").value);
                formData.append("cooperative_name", document.getElementById("update_cooperative_name").value);

                let provinceDropdown = document.getElementById("update_province");
                let municipalityDropdown = document.getElementById("update_municipality");
                let barangayDropdown = document.getElementById("update_barangay");

                formData.append("province", provinceDropdown.options[provinceDropdown.selectedIndex].text);
                formData.append("municipality", municipalityDropdown.options[municipalityDropdown.selectedIndex].text);
                formData.append("barangay", barangayDropdown.options[barangayDropdown.selectedIndex].text);

                fetch("6cooperativeManagement/update_cooperative.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Server Response:", data); // Debugging

                    if (data.success) {
                        Swal.fire({
                            title: "Updated!",
                            text: "Cooperative has been updated successfully.",
                            icon: "success"
                        }).then(closeModalAndReload); // Close modal and reload
                    } else {
                        Swal.fire("Error!", data.message, "error");
                    }
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    Swal.fire("Error!", "Something went wrong.", "error");
                });
            }
        });
    });
});
</script>

