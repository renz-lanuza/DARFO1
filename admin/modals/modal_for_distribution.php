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
                        <input type="hidden" id="beneficiary_id" name="beneficiary_id">

                        <!-- Date of Distribution -->
                        <div class="col-12 mb-3">
                            <label for="distribution_date" class="form-label">Date of Distribution</label>
                            <input type="date" class="form-control" id="distribution_date" name="distribution_date" required>
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
</div>                
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
                        <!-- Date of Distribution -->
                        <div class="col-md-4 mb-3">
                            <label for="update_distribution_date" class="form-label">Date of Distribution</label>
                            <input type="date" class="form-control" id="update_distribution_date" name="update_distribution_date" required>
                        </div>
                    </div>

                    <!-- Table for Interventions -->
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
                                        <select class="form-control intervention_name_distrib" name="intervention_name_distrib[]" required>
                                            <option value="" disabled selected>Select Intervention</option>
                                            <?php
                                                $conn = new mysqli("localhost", "root", "", "db_darfo1");
                                                $uid = $_SESSION['uid'];
                                                $stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
                                                $stationQuery->bind_param("i", $uid);
                                                $stationQuery->execute();
                                                $stationQuery->bind_result($stationId);
                                                $stationQuery->fetch();
                                                $stationQuery->close();

                                                $sql = "SELECT int_type_id, intervention_name FROM tbl_intervention_type WHERE station_id = ? ORDER BY int_type_id";
                                                $stmt = $conn->prepare($sql);
                                                $stmt->bind_param("i", $stationId);
                                                $stmt->execute();
                                                $result = $stmt->get_result();
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<option value='{$row['int_type_id']}'>" . htmlspecialchars($row['intervention_name']) . "</option>";
                                                }
                                                $conn->close();
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control seedling_type_distrib" name="seedling_type_distrib" required>
                                            <option value="" disabled selected>Select Classification</option>
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

                    <!-- Modal Footer -->
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
document.addEventListener('DOMContentLoaded', function() {
    var updateDistributionModal = document.getElementById('updateDistributionModal');
    updateDistributionModal.addEventListener('show.bs.modal', function (event) {
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
document.getElementById('updateDistributionForm').addEventListener('submit', function (e) {
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
