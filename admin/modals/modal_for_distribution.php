<style>
    .modal-header {
        background-color: #0D7C66;
        color: white;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .modal-content {
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    }

    /* Table Styles */
    #interventionTable {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #0D7C66;
    }

    #interventionTable thead {
        background-color: #0D7C66;
        color: white;
    }

    #interventionTable tbody tr {
        background-color: #E8F6F3;
        transition: 0.3s;
    }

    #interventionTable tbody tr:hover {
        background-color: #D1ECE4;
    }

    /* Button Styles */
    .btn-custom {
        border-radius: 5px;
        font-weight: 600;
    }

    .btn-primary {
        background-color: #0D7C66;
        border-color: #0A5C4B;
    }

    .btn-primary:hover {
        background-color: #0A5C4B;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #218838;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    /* Modal Footer */
    .modal-footer {
        justify-content: space-between;
        border-top: none;
    }
</style>

<!-- Add Distribution Modal -->
<div class="modal fade" id="addDistributionModal" tabindex="-1" aria-labelledby="addDistributionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDistributionModalLabel">
                    Add Distribution
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addDistributionForm" method="POST">
                    <input type="hidden" id="beneficiary_id" name="beneficiary_id">

                    <!-- Date of Distribution -->
                    <div class="mb-3">
                        <label for="distribution_date" class="form-label"> Date of Distribution</label>
                        <input type="date" class="form-control" id="distribution_date" name="distribution_date" required>
                    </div>

                    <!-- Intervention Details Table -->
                    <div class="mb-3">
                        <label class="form-label"> Intervention Details</label>
                        <table class="table table-bordered text-center" id="interventionTable">
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
                                        <select class="form-control intervention_name_distri" name="intervention_name_distri[]" required>
                                            <option value="" disabled selected>Select Intervention</option>
                                            <!-- PHP: Populate options dynamically -->
                                            <?php
                                            include("../conn.php");
                                            $station_id = $_SESSION['station_id']; // Get the user's station_id
                                            $sql = "SELECT int_type_id, intervention_name FROM tbl_intervention_type WHERE station_id = ? ORDER BY intervention_name";
                                            $stmt = $conn->prepare($sql);
                                            $stmt->bind_param("i", $station_id);
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option value='{$row['int_type_id']}'>" . htmlspecialchars($row['intervention_name']) . "</option>";
                                            }
                                            ?>
                                        </select>
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
                                    <!-- <td>
                                        <button type="button" class="btn btn-danger btn-sm remove-row">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td> -->
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-primary btn-sm" id="addRowButton">
                            <i class="fas fa-plus-circle"></i> Add Row
                        </button>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-custom" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-success btn-custom">
                            Add Distribution
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get today's date in YYYY-MM-DD format
        let today = new Date().toISOString().split('T')[0];

        // Set the default value of the date input field
        document.getElementById("distribution_date").value = today;
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
                    <input type="hidden" name="distribution_id" id="distribution_id">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="update_distribution_date" class="form-label">Date of Distribution</label>
                            <input type="date" class="form-control" id="update_distribution_date" name="update_distribution_date" required>
                        </div>
                    </div>

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
                                        <select class="form-control intervention_name_distrib" name="intervention_name_distrib" required>
                                            <option value="" disabled selected>Select Intervention</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control seedling_type_distrib" name="seedling_type_distrib" required>
                                            <option value="" disabled selected>Select Classification</option>
                                            <!-- Options for seedling types will be populated here -->
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

<script>
    // This script will handle the modal close event
    document.addEventListener('DOMContentLoaded', function() {
        var updateDistributionModal = document.getElementById('updateDistributionModal');

        updateDistributionModal.addEventListener('hidden.bs.modal', function() {
            // Reload the page when modal is closed
            location.reload();
        });

        // Optional: Prevent reload if form was submitted successfully
        document.getElementById('updateDistributionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Handle your form submission via AJAX here

            // After successful submission, you can close the modal manually
            // and it will still reload the page
            var modal = bootstrap.Modal.getInstance(updateDistributionModal);
            modal.hide();
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var updateDistributionModal = document.getElementById('updateDistributionModal');
        updateDistributionModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var distributionId = button.getAttribute('data-distribution-id');
            var quantity = button.getAttribute('data-quantity');
            var interventionName = button.getAttribute('data-intervention-name');
            var seedName = button.getAttribute('data-seed-name');
            var distributionDate = button.getAttribute('data-distribution-date');

            // Update the modal's content.
            var modalTitle = updateDistributionModal.querySelector('.modal-title');
            var distributionIdInput = updateDistributionModal.querySelector('#distribution_id');
            var updateQuantityInput = updateDistributionModal.querySelector('input[name="update_quantity[]"]');
            var updateDistributionDateInput = updateDistributionModal.querySelector('#update_distribution_date');

            modalTitle.textContent = 'Update Distribution ' + distributionId;
            distributionIdInput.value = distributionId;
            updateQuantityInput.value = quantity;
            updateDistributionDateInput.value = distributionDate;
        });
    });
</script>

<!-- function for update distribution -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('updateDistributionForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // SweetAlert confirmation
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to update this distribution.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData(this);

                fetch('3DistributionManagement/updateDistribution.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                            }).then(() => {
                                $('#updateDistributionModal').modal('hide');
                                // Optionally, refresh the page or update the table
                                location.reload(); // Reload the page to reflect changes
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message,
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while updating the distribution.',
                        });
                    });
            }
        });
    });
</script>
