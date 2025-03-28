<!-- Bootstrap core JavaScript-->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Core plugin JavaScript-->
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="../js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="../vendor/chart.js/Chart.min.js"></script>

<!-- Page level custom scripts -->
<script src="../js/demo/chart-area-demo.js"></script>
<script src="../js/demo/chart-pie-demo.js"></script>

<!-- sweetalert2 -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>
<!-- Include SweetAlert CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Bootstrap 5 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- inserting user swal -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Add Users -->
<script>
    $(document).ready(function() {
        $(document).ready(function() {
            // Handle form submission
            $("#addUserForm").submit(function(event) {
                event.preventDefault(); // Prevent default form submission

                // Show a confirmation dialog using SweetAlert2
                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to add this user?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#28a745",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, add it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Serialize the form data
                        let formData = $(this).serialize();
                        console.log("Sending Data:", formData); // Debugging

                        // Send the form data using AJAX
                        $.ajax({
                            type: "POST",
                            url: "1userManagement/addUser.php", // URL to the PHP script
                            data: formData, // Form data to be sent
                            dataType: "json", // Expecting a JSON response
                            success: function(response) {
                                console.log("Server Response:", response); // Debugging

                                // Handle the success response
                                if (response.success) {
                                    Swal.fire({
                                        title: "Success!",
                                        text: response.message,
                                        icon: "success",
                                        confirmButtonColor: "#28a745",
                                    }).then(() => {
                                        // Close the modal
                                        $("#addUserModal").modal("hide"); // Close the modal with the correct ID
                                        // Reset the form
                                        $("#addUserForm")[0].reset();
                                        // Optionally, reload the page
                                        setTimeout(() => {
                                            window.location.href = window.location.href;
                                        }, 1000); // 1-second delay
                                    });
                                } else {
                                    // Handle the error response
                                    Swal.fire({
                                        title: "Error!",
                                        text: response.message,
                                        icon: "error",
                                        confirmButtonColor: "#d33",
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                // Handle AJAX errors
                                console.error("AJAX Error:", xhr.responseText);
                                Swal.fire({
                                    title: "Error!",
                                    text: "An error occurred while processing your request.",
                                    icon: "error",
                                    confirmButtonColor: "#d33",
                                });
                            }
                        });
                    }
                });
            });
        });
    });
</script>

<!-- update and fetch users -->
<script>
    $(document).ready(function() {
        // Open modal and fetch user data
        $(document).on("click", "[data-target='#updateUserModal']", function() {
            let userId = $(this).data("user-id");
            fetchUserData(userId);
        });

        // Close modal properly
        $(".close-modal").click(function() {
            $("#updateUserModal").modal("hide");
        });

        $("#updateUserModal").on("hidden.bs.modal", function() {
            $("body").removeClass("modal-open");
            $(".modal-backdrop").remove();
        });

        // Update user data on form submission with confirmation
        $("#updateUserBtn").click(function() {
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to update this user?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, update it!",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = new FormData(document.getElementById("updateUserForm"));

                    fetch("1userManagement/updateUser.php", {
                            method: "POST",
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: "Updated!",
                                    text: "User updated successfully.",
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire("Error", data.message, "error");
                            }
                        })
                        .catch(error => Swal.fire("Error", "An error occurred while updating.", "error"));
                }
            });
        });
    });

    // Fetch user data for editing
    function fetchUserData(userId) {
        $.ajax({
            url: "1userManagement/fetchUser.php",
            type: "POST",
            data: {
                userId: userId
            },
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    $("#userId").val(response.data.uid);
                    $("#uname").val(response.data.username);
                    $("#ulevel").val(response.data.ulevel);
                    $("#fname").val(response.data.fname);
                    $("#mname").val(response.data.mname);
                    $("#lname").val(response.data.lname);
                    $("#userstation").val(response.data.station_id);
                    $("#updateUserModal").modal("show");
                } else {
                    Swal.fire("Error", "User not found.", "error");
                }
            },
            error: function() {
                Swal.fire("Error", "An error occurred while fetching data.", "error");
            }
        });
    }
</script>

<!-- Update Status -->
<script>
    function toggleStatus(username, action) {
        Swal.fire({
            title: "Are you sure?",
            text: "You are about to " + action + " the user: " + username,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, proceed!",
            cancelButtonText: "No, cancel!",
        }).then((result) => {
            if (result.isConfirmed) {
                // Create an XMLHttpRequest object
                var xhr = new XMLHttpRequest();

                // Define the URL to send the request to
                var url = '1userManagement/updateStatus.php'; // Change this to your actual URL

                // Prepare the data to be sent
                var data = 'username=' + encodeURIComponent(username) + '&action=' + encodeURIComponent(action);

                // Configure the request
                xhr.open('POST', url, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                // Set up a function to handle the response
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Handle the response from the server
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Show success message
                            Swal.fire("Success!", "Status updated successfully to " + action, "success")
                                .then(() => {
                                    location.reload(); // Reload the page to see the changes
                                });
                        } else {
                            Swal.fire("Error!", "Error updating status: " + response.message, "error");
                        }
                    }
                };

                // Send the request with the data
                xhr.send(data);
            } else {
                Swal.fire("Cancelled", "The action has been cancelled", "error");
            }
        });
    }
</script>


<!-- swal for adding intervention -->

<script>
    $(document).ready(function() {
        $("#interventionForm").submit(function(event) {
            event.preventDefault(); // Prevent default form submission

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to add this intervention?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, submit it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = $(this).serialize();
                    console.log("Sending Data:", formData); // Debugging

                    $.ajax({
                        type: "POST",
                        url: "2InterventionManagement/addIntervention.php",
                        data: formData,
                        dataType: "json",
                        success: function(response) {
                            console.log("Server Response:", response); // Debugging

                            if (response.status === "success") {
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonColor: "#28a745",
                                }).then(() => {
                                    $("#addInterventionModal").modal("hide"); // Close modal
                                    $("#interventionForm")[0].reset(); // Reset form
                                    location.reload(); // Reload the page after success
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    icon: "error",
                                    confirmButtonColor: "#d33",
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", xhr.responseText);
                            Swal.fire({
                                title: "Error!",
                                text: "An error occurred while processing your request.",
                                icon: "error",
                                confirmButtonColor: "#d33",
                            });
                        }
                    });
                }
            });
        });
    });
</script>


<!-- swal for EDIT INTERVENTION -->
<script>
    $(document).ready(function() {
        // Populate Modal with Data
        $(".edit-btn").on("click", function() {
            let id = $(this).data("id");
            let name = $(this).data("name");
            let description = $(this).data("description");
            let quantity = $(this).data("quantity");

            $("#edit_intervention_id").val(id);
            $("#edit_intervention_name").val(name);
            $("#edit_description").val(description);
            $("#edit_quantity").val(quantity);
        });

        // Handle Form Submission
        $("#editInterventionForm").submit(function(event) {
            event.preventDefault();

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to update this intervention?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, update it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = $(this).serialize();

                    $.ajax({
                        type: "POST",
                        url: "2InterventionManagement/editIntervention.php",
                        data: formData,
                        dataType: "json",
                        success: function(response) {
                            if (response.status === "success") {
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonColor: "#28a745",
                                }).then(() => {
                                    $("#editInterventionModal").modal("hide");
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1000);
                                });
                            } else {
                                Swal.fire("Error!", response.message, "error");
                            }
                        },
                        error: function(xhr) {
                            console.error("AJAX Error:", xhr.responseText);
                            Swal.fire("Error!", "An error occurred while updating.", "error");
                        }
                    });
                }
            });
        });
    });
</script>

<!-- swal for log out -->
<script>
    $(document).ready(function() {
        // Handle form submission
        $("#addUserForm").submit(function(event) {
            event.preventDefault(); // Prevent default form submission

            // Show a confirmation dialog using SweetAlert2
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to add this user?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, add it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Serialize the form data
                    let formData = $(this).serialize();
                    console.log("Sending Data:", formData); // Debugging

                    // Send the form data using AJAX
                    $.ajax({
                        type: "POST",
                        url: "1userManagement/addUser.php", // URL to the PHP script
                        data: formData, // Form data to be sent
                        dataType: "json", // Expecting a JSON response
                        success: function(response) {
                            console.log("Server Response:", response); // Debugging

                            // Handle the success response
                            if (response.success) {
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonColor: "#28a745",
                                }).then(() => {
                                    // Close the modal
                                    $("#addUserModal").modal("hide"); // Close the modal with the correct ID
                                    // Reset the form
                                    $("#addUserForm")[0].reset();
                                    // Optionally, reload the page
                                    setTimeout(() => {
                                        window.location.href = window.location.href;
                                    }, 1000); // 1-second delay
                                });
                            } else {
                                // Handle the error response
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    icon: "error",
                                    confirmButtonColor: "#d33",
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            // Handle AJAX errors
                            console.error("AJAX Error:", xhr.responseText);
                            Swal.fire({
                                title: "Error!",
                                text: "An error occurred while processing your request.",
                                icon: "error",
                                confirmButtonColor: "#d33",
                            });
                        }
                    });
                }
            });
        });
    });
</script>

<!-- Update Status -->
<script>
    function toggleStatus(username, action) {
        Swal.fire({
            title: "Are you sure?",
            text: "You are about to " + action + " the user: " + username,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, proceed!",
            cancelButtonText: "No, cancel!",
        }).then((result) => {
            if (result.isConfirmed) {
                // Create an XMLHttpRequest object
                var xhr = new XMLHttpRequest();

                // Define the URL to send the request to
                var url = '1userManagement/updateStatus.php'; // Change this to your actual URL

                // Prepare the data to be sent
                var data = 'username=' + encodeURIComponent(username) + '&action=' + encodeURIComponent(action);

                // Configure the request
                xhr.open('POST', url, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                // Set up a function to handle the response
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Handle the response from the server
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Show success message
                            Swal.fire("Success!", "Status updated successfully to " + action, "success")
                                .then(() => {
                                    location.reload(); // Reload the page to see the changes
                                });
                        } else {
                            Swal.fire("Error!", "Error updating status: " + response.message, "error");
                        }
                    }
                };

                // Send the request with the data
                xhr.send(data);
            } else {
                Swal.fire("Cancelled", "The action has been cancelled", "error");
            }
        });
    }
</script>



<!-- JS for Updating Users -->
<script>
    $(document).ready(function() {
        $('#saveChanges').on('click', function() {
            Swal.fire({
                title: "Are you sure?",
                text: "You are about to update this user's details.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, update it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = $('#updateUserForm').serialize(); // Serialize form data

                    $.ajax({
                        url: '1userManagement/updateUser.php',
                        method: 'POST',
                        data: formData,
                        dataType: 'json', // Expect JSON response
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: "Updated!",
                                    text: response.success,
                                    icon: "success",
                                    timer: 1000, // Auto close after 2 seconds
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload(); // Auto reload page after success
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.error,
                                    icon: "error"
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                title: "Error!",
                                text: "An error occurred: " + error,
                                icon: "error"
                            });
                        }
                    });
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Function to handle intervention change
        $(document).on('change', '.intervention_name_distri', function() {
            var int_type_id = $(this).val(); // Get the selected intervention ID
            var $row = $(this).closest('tr'); // Get the current row

            if (int_type_id) {
                $.ajax({
                    url: '3distributionManagement/fetch_seedlings_distri.php',
                    type: 'GET',
                    data: {
                        int_type_id: int_type_id
                    },
                    success: function(response) {
                        var seedlings = JSON.parse(response);

                        // Clear and update the seedling dropdown in the same row
                        var $seedlingDropdown = $row.find('.seedling_type_distri');
                        $seedlingDropdown.empty();
                        $seedlingDropdown.append('<option value="" disabled selected>Select Seedling Type</option>');

                        if (seedlings.length > 0) {
                            seedlings.forEach(function(seedling) {
                                $seedlingDropdown.append('<option value="' + seedling.seed_id + '">' + seedling.seed_name + '</option>');
                            });
                        } else {
                            $seedlingDropdown.append('<option value="" disabled>No seedlings found</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching seedlings:', error);
                    }
                });
            }
        });
        // Add new row
        $('#addRowButton').click(function() {
            var newRow = `
         <tr>
            <td>
                  <select class="form-control intervention_name_distri" name="intervention_name_distri[]" required>
                     <option value="" disabled selected>Select Intervention</option>
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

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['int_type_id']}'>{$row['intervention_name']}</option>";
                            }
                        } else {
                            echo "<option value='' disabled>No interventions available for this station</option>";
                        }

                        $stmt->close();
                        $conn->close();
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
            <td>
                  <button type="button" class="btn btn-danger btn-sm removeRow">Remove</button>
            </td>
         </tr>`;

            $('#interventionTable tbody').append(newRow);
        });

        // Remove row
        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
        });
    });
</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Add Intervention Type -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- swal for adding intervention TYPE -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("addInterventionForm");

        if (form) {
            form.addEventListener("submit", function(event) {
                event.preventDefault(); // Prevent default form submission

                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to add this intervention type?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, add it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData(form); // Capture form data

                        // Make the fetch request to your PHP script
                        fetch("4InterventionTypeManagement/addInterventionType.php", {
                                method: "POST",
                                body: formData
                            })
                            .then(response => {
                                // Check if response is okay
                                if (!response.ok) {
                                    throw new Error("Server error");
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: "Added!",
                                        text: "Intervention type has been added.",
                                        icon: "success"
                                    }).then(() => {
                                        location.reload(); // Reload page to update the table
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: data.message || "An unknown error occurred.",
                                        icon: "error"
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: "Error!",
                                    text: error.message || "Something went wrong.",
                                    icon: "error"
                                });
                                console.error("Error:", error);
                            });
                    }
                });
            });
        } else {
            console.error("Form with ID 'addInterventionForm' not found.");
        }
    });
</script>


<!-- swal for adding seed type -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("addSeedTypeForm");

        if (form) {
            form.addEventListener("submit", function(event) {
                event.preventDefault(); // Prevent default form submission

                // Get the selected value from the dropdown
                const interventionName = document.getElementById("intervention_name").value; // this is the int_type_id
                const seedTypeName = document.getElementById("seed_type_name").value;

                // Check if the fields are filled
                if (!interventionName || !seedTypeName) {
                    Swal.fire({
                        title: "Error!",
                        text: "Please select an intervention and provide a seed type name.",
                        icon: "error"
                    });
                    return; // Prevent form submission
                }

                // SweetAlert confirmation
                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to add this seed type?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, add it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        let formData = new FormData(form); // Capture form data

                        // Make the fetch request to your PHP script
                        fetch("5seedTypeManagement/addSeedType.php", { // Ensure this path is correct
                                method: "POST",
                                body: formData
                            })
                            .then(response => {
                                // Check if response is okay
                                if (!response.ok) {
                                    throw new Error("Server error");
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: "Added!",
                                        text: "Classification has been added.",
                                        icon: "success"
                                    }).then(() => {
                                        location.reload(); // Reload page to update the table
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Error!",
                                        text: data.message || "An unknown error occurred.",
                                        icon: "error"
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: "Error!",
                                    text: error.message || "Something went wrong.",
                                    icon: "error"
                                });
                                console.error("Error:", error);
                            });
                    }
                });
            });
        } else {
            console.error("Form with ID 'addSeedTypeForm' not found.");
        }
    });
</script>

<!-- script for fetchin the seed type -->
<script>
    $(document).ready(function() {
        // When an intervention name is selected
        $('#intervention_name').change(function() {
            var int_type_id = $(this).val(); // Get the selected int_type_id

            // Check if a valid intervention was selected
            if (int_type_id) {
                // AJAX request to fetch seedling types based on the selected intervention
                $.ajax({
                    url: '5seedTypeManagement/fetch_seedlings.php', // File to process the request (you need to create this file)
                    type: 'GET',
                    data: {
                        int_type_id: int_type_id
                    }, // Pass the int_type_id
                    success: function(response) {
                        // Parse the response as JSON
                        var seedlings = JSON.parse(response);

                        // Clear existing options in seedling dropdown
                        $('#seedling_type').empty();
                        $('#seedling_type').append('<option value="" disabled selected>Select Seedling Type</option>');

                        // Populate the seedling dropdown with new options
                        if (seedlings.length > 0) {
                            seedlings.forEach(function(seedling) {
                                $('#seedling_type').append('<option value="' + seedling.seed_id + '">' + seedling.seed_name + '</option>');
                            });
                        } else {
                            $('#seedling_type').append('<option value="" disabled>No seedlings found</option>');
                        }
                    }
                });
            } else {
                // If no intervention is selected, clear the seedling type dropdown
                $('#seedling_type').empty();
                $('#seedling_type').append('<option value="" disabled selected>Select Seedling Type</option>');
            }
        });
    });
</script>

<!-- api request to get the data inthe gitlab -->
<script>
    $(document).ready(function() {
        // Fetch provinces
        $.getJSON('3distributionManagement/fetch_psgc.php?type=provinces', function(data) {
            data.forEach(function(province) {
                $('#province').append(
                    `<option value="${province.code}" data-name="${province.name}">${province.name}</option>`
                );
            });
        });

        // Fetch municipalities when a province is selected
        $('#province').change(function() {
            const provinceCode = $(this).val();
            const provinceName = $(this).find(":selected").data('name'); // Get selected province name
            $('#municipality').empty().append('<option selected disabled>Loading...</option>');
            $('#barangay').empty().prop('disabled', true);

            $.getJSON(`3distributionManagement/fetch_psgc.php?type=municipalities&code=${provinceCode}`, function(data) {
                $('#municipality').empty().append('<option selected disabled>Select a municipality</option>');
                data.forEach(function(municipality) {
                    $('#municipality').append(
                        `<option value="${municipality.code}" data-name="${municipality.name}">${municipality.name}</option>`
                    );
                });
                $('#municipality').prop('disabled', false);
            });
        });

        // Fetch barangays when a municipality is selected
        $('#municipality').change(function() {
            const municipalityCode = $(this).val();
            const municipalityName = $(this).find(":selected").data('name'); // Get selected municipality name
            $('#barangay').empty().append('<option selected disabled>Loading...</option>');

            $.getJSON(`3distributionManagement/fetch_psgc.php?type=barangays&code=${municipalityCode}`, function(data) {
                $('#barangay').empty().append('<option selected disabled>Select a barangay</option>');
                data.forEach(function(barangay) {
                    $('#barangay').append(
                        `<option value="${barangay.code}" data-name="${barangay.name}">${barangay.name}</option>`
                    );
                });
                $('#barangay').prop('disabled', false);
            });
        });
    });
</script>


<!-- script for geting the quantity left -->
<script>
    $(document).ready(function() {
        // When seedling type is selected, fetch quantity left
        $('#seedling_type_distri').change(function() {
            var interventionId = $('#intervention_name_distri').val(); // Get selected intervention ID
            var seedlingId = $(this).val(); // Get selected seedling type ID

            // Fetch quantity left based on selected intervention and seedling
            if (interventionId && seedlingId) {
                $.ajax({
                    url: '3distributionManagement/get_quantity_left.php', // PHP file to fetch quantity left
                    type: 'POST',
                    data: {
                        int_type_id: interventionId,
                        seed_id: seedlingId
                    },
                    dataType: 'json', // Expect JSON response
                    success: function(response) {
                        console.log(response); // Debugging: Log the full response
                        if (response.success) { // Check if success is true
                            $('#quantityLeft').text(response.quantity_left); // Display the quantity left in the span
                        } else {
                            $('#quantityLeft').text("0"); // Default value if no records found
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error: " + error); // Log any AJAX errors
                    }
                });
            } else {
                $('#quantityLeft').text(""); // Clear the quantity left if either dropdown is empty
            }
        });
    });
</script>
<!-- script for geting the quantity left -->
<script>
    $(document).ready(function() {
        // When seedling type is selected, fetch quantity left
        $(document).on('change', 'select[name="seedling_type_distri[]"]', function() {
            var $row = $(this).closest('tr'); // Find the closest table row
            var interventionId = $row.find('select[name="intervention_name_distri[]"]').val(); // Get selected intervention ID
            var seedlingId = $(this).val(); // Get selected seedling type ID

            // Fetch quantity left based on selected intervention and seedling
            if (interventionId && seedlingId) {
                $.ajax({
                    url: '3distributionManagement/get_quantity_left.php', // PHP file to fetch quantity left
                    type: 'POST',
                    data: {
                        int_type_id: interventionId,
                        seed_id: seedlingId
                    },
                    dataType: 'json', // Expect JSON response
                    success: function(response) {
                        console.log(response); // Debugging: Log the full response
                        if (response.success) { // Check if success is true
                            $row.find('.quantity-left').text("Quantity Left: " + response.quantity_left); // Update the quantity-left text
                        } else {
                            $row.find('.quantity-left').text("Quantity Left: 0"); // Default value if no records found
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error: " + error); // Log any AJAX errors
                    }
                });
            } else {
                $row.find('.quantity-left').text("Quantity Left: 0"); // Clear the quantity left if either dropdown is empty
            }
        });
    });
</script>

<!-- swal for adding distribution -->
<script>
    $(document).ready(function() {
        // Handle Add Distribution button click
        $(document).on('click', '#btnAddDistribution', function() {
            // Debug: Check if the button is being clicked
            console.log("Add Distribution button clicked!");

            // Get the beneficiary ID from the data attribute
            const beneficiaryId = $(this).data('beneficiary-id');

            // Debug: Check if the beneficiary ID is being retrieved
            console.log("Beneficiary ID from data attribute:", beneficiaryId);

            // Set the beneficiary ID in a hidden input field in the modal
            $('#beneficiary_id').val(beneficiaryId);

            // Debug: Check if the value is being set in the hidden input
            console.log("Value set in hidden input:", $('#beneficiary_id').val());
        });
        // Handle form submission
        $("#addDistributionForm").submit(function(e) {
            e.preventDefault(); // Prevent default form submission

            // Collect the form data
            var interventionNames = [];
            var seedlingTypes = [];
            var quantities = [];

            // Collect multiple inputs for intervention names, seedling types, and quantities
            $('select[name="intervention_name_distri[]"]').each(function() {
                interventionNames.push($(this).val());
            });
            $('select[name="seedling_type_distri[]"]').each(function() {
                seedlingTypes.push($(this).val());
            });
            $('input[name="quantity_distri[]"]').each(function() {
                quantities.push($(this).val());
            });

            // Prepare form data for submission
            var formData = {
                distribution_date: $('#distribution_date').val(), // Fetch distribution date
                beneficiary_id: $('#beneficiary_id').val(), // Fetch beneficiary ID from the hidden input
                intervention_name_distri: interventionNames, // Array of intervention IDs
                quantity_distri: quantities, // Array of quantities
                seedling_type_distri: seedlingTypes // Array of seed IDs
            };

            // SweetAlert confirmation dialog
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to add this distribution?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, submit it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Perform AJAX request
                    $.ajax({
                        url: '3distributionManagement/addDistribution.php', // Update with the correct path
                        type: 'POST',
                        data: formData, // Pass the form data to the PHP file
                        dataType: 'json', // Expect a JSON response
                        success: function(response) {
                            if (response.status === "success") {
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonColor: "#28a745",
                                }).then(() => {
                                    $("#addDistributionModal").modal("hide"); // Close modal
                                    $("#addDistributionForm")[0].reset(); // Reset form
                                    location.reload(); // Reload the page after success
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    icon: "error",
                                    confirmButtonColor: "#d33",
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", xhr.responseText);
                            Swal.fire({
                                title: "Error!",
                                text: "An error occurred while processing your request: " + xhr.responseText,
                                icon: "error",
                                confirmButtonColor: "#d33",
                            });
                        }
                    });
                }
            });
        });
    });
</script>


<!-- fetch the seedlings in the distribution management -->
<!-- script for fetching the seed type -->
<!-- JS for fetch update intervention -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const updateModal = document.getElementById("updateInterventionModal");

        updateModal.addEventListener("show.bs.modal", function(event) {
            let button = event.relatedTarget;
            let interventionId = button.getAttribute("data-intervention-id");

            fetch("2InterventionManagement/fetch_intervention.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `intervention_id=${interventionId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }

                    document.getElementById("intervention_id").value = data.intervention_id;
                    document.getElementById("intervention_type").value = data.intervention_name;
                    document.getElementById("seed_type").value = data.seed_name;
                    document.getElementById("description").value = data.description;
                    document.getElementById("quantity").value = data.quantity;
                    document.getElementById("quantity_left").value = data.quantity_left;

                    // Set the hidden unit_id value
                    document.getElementById("unit_id").value = data.unit_id;

                    // Select the correct unit_name in the dropdown
                    let unitDropdown = document.getElementById("unit_name");
                    for (let option of unitDropdown.options) {
                        if (option.value == data.unit_id) {
                            option.selected = true;
                            break;
                        }
                    }
                })
                .catch(error => console.error("Error fetching intervention:", error));
        });

        // Form submission with confirmation
        document.getElementById("updateIntForm").addEventListener("submit", function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to update this intervention?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    let formData = new FormData(this);

                    fetch("2InterventionManagement/update_intervention.php", {
                            method: "POST",
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Updated!', 'The intervention has been updated successfully.', 'success')
                                    .then(() => {
                                        $('#updateInterventionModal').modal('hide');
                                        location.reload();
                                    });
                            } else {
                                Swal.fire('Error!', data.message || 'There was a problem updating the intervention.', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'There was an error with the request.', 'error');
                            console.error("Error updating intervention:", error);
                        });
                }
            });
        });
    });
</script>


<script>
    $(document).ready(function() {
        $('#logout-button').on('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: "Are you sure?",
                text: "You will be logged out.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, log out",
                cancelButtonText: "No, stay logged in"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '../logout.php', // Adjust if needed
                        success: function(response) {
                            let res = JSON.parse(response);
                            if (res.status === "success") {
                                Swal.fire({
                                    title: "Logged out successfully!",
                                    text: "Redirecting...",
                                    icon: "success",
                                    showConfirmButton: false,
                                    timer: 1000
                                }).then(() => {
                                    window.location.href = '../index.php';
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: "Error",
                                text: "Logout failed! Try again.",
                                icon: "error",
                                showConfirmButton: false,
                                timer: 1000
                            });
                        }
                    });
                }
            });
        });
    });
</script>



<script>
    $(document).ready(function() {
        $("#addCooperativeForm").submit(function(e) {
            e.preventDefault(); // Prevent default form submission

            // Collect form data
            var formData = {
                cooperative_name: $('#cooperative_name').val(), // Cooperative name
                province_code: $('#province').val(), // Province code
                province_name: $('#province option:selected').text(), // Province name
                municipality_code: $('#municipality').val(), // Municipality code
                municipality_name: $('#municipality option:selected').text(), // Municipality name
                barangay_code: $('#barangay').val(), // Barangay code
                barangay_name: $('#barangay option:selected').text() // Barangay name
            };

            // SweetAlert confirmation dialog
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to add this cooperative?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, submit it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Perform AJAX request
                    $.ajax({
                        url: '6cooperativeManagement/addCoop.php', // Update with the correct path
                        type: 'POST',
                        data: formData, // Send form data to PHP
                        dataType: 'json', // Expect a JSON response
                        success: function(response) {
                            if (response.status === "success") {
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonColor: "#28a745",
                                }).then(() => {
                                    $("#addCooperativeModal").modal("hide"); // Close modal
                                    $("#addCooperativeForm")[0].reset(); // Reset form
                                    location.reload(); // Reload the page after success
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    icon: "error",
                                    confirmButtonColor: "#d33",
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", xhr.responseText);
                            Swal.fire({
                                title: "Error!",
                                text: "An error occurred while processing your request.",
                                icon: "error",
                                confirmButtonColor: "#d33",
                            });
                        }
                    });
                }
            });
        });
    });
</script>

<!-- script for fetching int type update  -->

<script>
   $(document).ready(function () {
    // Dynamically handle "Edit" button clicks
    $(document).on("click", ".update-intervention", function () {
        var intTypeId = $(this).data("int-type-id");

        console.log("Fetching data for Intervention ID:", intTypeId); // Debugging

        $.ajax({
            url: "4InterventionTypeManagement/fetch_intervention_type.php",
            type: "POST",
            data: { int_type_id: intTypeId },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    console.log("Data received:", response);

                    // Populate modal fields
                    $("#updateIntTypeId").val(response.data.int_type_id);
                    $("#updateInterventionName").val(response.data.intervention_name);

                    // Show modal
                    $("#updateInterventionTypeModal").modal("show");
                } else {
                    Swal.fire("Error!", "Error fetching data.", "error");
                }
            },
            error: function () {
                Swal.fire("Failed!", "Failed to retrieve data.", "error");
            }
        });
    });

    // Handle update form submission
    $("#updateInterventionForm").submit(function (e) {
        e.preventDefault();

        Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to update this intervention?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#0D7C66",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, update it!"
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = $(this).serialize();
                $.ajax({
                    url: "4InterventionTypeManagement/update_intervention_type.php",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Updated!",
                                text: "Intervention updated successfully.",
                                icon: "success",
                                confirmButtonColor: "#0D7C66"
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire("Error!", "Error updating intervention: " + response.message, "error");
                        }
                    },
                    error: function () {
                        Swal.fire("Failed!", "Failed to update intervention.", "error");
                    }
                });
            }
        });
    });
});

</script>

<!-- fetch and dynamic int name and classification from distribution -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const updateModal = document.getElementById("updateDistributionModal");

        updateModal.addEventListener("show.bs.modal", function(event) {
            const button = event.relatedTarget; // Button that triggered the modal

            // Get elements inside the modal
            let interventionDropdown = updateModal.querySelector("select[name='intervention_name_distrib[]']");
            let classificationDropdown = updateModal.querySelector("select[name='seedling_type_distrib']");
            let quantityField = updateModal.querySelector(".quantity-left");

            document.querySelector("input[name='update_quantity[]']").value = button.getAttribute("data-quantity");

            // Fetch and update quantity left
            let quantityLeft = button.getAttribute("data-quantity-left") || "0";
            quantityField.textContent = quantityLeft;

            // Set intervention dropdown
            let selectedInterventionId = button.getAttribute("data-intervention-id");
            let selectedInterventionName = button.getAttribute("data-intervention-name");

            interventionDropdown.innerHTML = `<option selected value="${selectedInterventionId}">${selectedInterventionName}</option>`;

            // Set classification dropdown
            classificationDropdown.innerHTML = `<option selected value="${button.getAttribute("data-seed-id")}">${button.getAttribute("data-seed-name")}</option>`;

            // Fetch interventions to update the dropdown
            fetch("3distributionManagement/get_interventions.php")
                .then(response => response.json())
                .then(data => {
                    interventionDropdown.innerHTML = `<option value="" disabled>Select Intervention:</option>`;

                    data.forEach(intervention => {
                        let selected = (intervention.int_type_id === selectedInterventionId) ? "selected" : "";
                        interventionDropdown.innerHTML += `<option value="${intervention.int_type_id}" ${selected}>${intervention.intervention_name}</option>`;
                    });
                })
                .catch(error => console.error("Error fetching interventions:", error));
        });
    });

    $(document).ready(function() {
        // Handle intervention selection change
        $(document).on('change', '.intervention_name_distrib', function() {
            var int_type_id = $(this).val(); // Get selected intervention ID
            var $row = $(this).closest('tr'); // Get the current row

            if (int_type_id) {
                $.ajax({
                    url: '3distributionManagement/fetch_intervention_data.php',
                    type: 'GET',
                    data: {
                        int_type_id: int_type_id
                    },
                    success: function(response) {
                        try {
                            var data = JSON.parse(response);
                            var $classificationDropdown = $row.find('.seedling_type_distrib');
                            var $quantityField = $row.find('.quantity-left');

                            // Step 1: Clear and populate classification dropdown
                            $classificationDropdown.empty();
                            $classificationDropdown.append('<option value="" disabled selected>Select Classification</option>');

                            if (data.length > 0) {
                                data.forEach(function(item) {
                                    $classificationDropdown.append(
                                        `<option value="${item.seed_id}" data-quantity="${item.quantity}">${item.seed_name}</option>`
                                    );
                                });
                            } else {
                                $classificationDropdown.append('<option value="" disabled>No classifications found</option>');
                            }

                            // Reset quantity display
                            $quantityField.text('0');
                        } catch (error) {
                            console.error("JSON Parse Error:", error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }
        });

        // Fetch quantity_left dynamically when classification is selected
        $(document).on('change', '.seedling_type_distrib', function() {
            var selectedOption = $(this).find(':selected');
            var seed_id = selectedOption.val();
            var $row = $(this).closest('tr');
            var int_type_id = $row.find('.intervention_name_distrib').val();

            if (seed_id) {
                $.ajax({
                    url: '3distributionManagement/fetch_quantity_left.php',
                    type: 'GET',
                    data: {
                        int_type_id: int_type_id,
                        seed_id: seed_id
                    },
                    success: function(response) {
                        var quantityLeft = response ? response : '0';
                        $row.find('.quantity-left').text(quantityLeft);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching quantity left:', error);
                    }
                });
            }
        });
    });
</script>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<!-- for viewing the interventions received -->
<script>
    $(document).ready(function() {
        // Event listener for the "View Interventions" button
        $('.view-interventions-btn').on('click', function() {
            var beneficiaryId = $(this).data('beneficiary-idview');
            console.log("Beneficiary ID:", beneficiaryId);

            // Show the modal
            $('#viewInterventionsModal').modal('show');

            // Fetch interventions for the selected beneficiary
            $.ajax({
                url: '2interventionManagement/fetch_intervention_dashboard.php',
                type: 'GET',
                data: {
                    beneficiary_id: beneficiaryId
                },
                success: function(response) {
                    $('#modalContent').html(response);
                },
                error: function(xhr, status, error) {
                    $('#modalContent').html('<p class="text-danger">Error loading interventions.</p>');
                }
            });
        });

        // Manually close the modal (for debugging)
        $('#viewInterventionsModal .close, #viewInterventionsModal .btn-secondary').on('click', function() {
            $('#viewInterventionsModal').modal('hide');
        });
    });
</script>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- archive user  -->
<script>
    function confirmArchive(userId) {
        Swal.fire({
            title: "Are you sure?",
            text: "This user will be archived!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, archive it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                archiveUser(userId);
            }
        });
    }

    function archiveUser(userId) {
        $.ajax({
            url: "1userManagement/archiveUser.php",
            type: "POST",
            data: {
                userId: userId
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    Swal.fire("Archived!", "User has been archived.", "success").then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire("Error", response.message, "error");
                }
            },
            error: function() {
                Swal.fire("Error", "An error occurred while archiving.", "error");
            }
        });
    }
</script>

<!-- archive intervention management -->
<!-- Include SweetAlert Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".archiveintervention-btn").forEach(button => {
            button.addEventListener("click", function() {
                let interventionId = this.getAttribute("data-intervention-id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "This intervention will be archived.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, archive it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`2InterventionManagement/archiveIntervention.php?intervention_id=${interventionId}`, {
                                method: "GET"
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(
                                        "Archived!",
                                        "The intervention has been archived.",
                                        "success"
                                    ).then(() => {
                                        location.reload(); // Refresh page to update table
                                    });
                                } else {
                                    Swal.fire("Error!", "Failed to archive the intervention.", "error");
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                Swal.fire("Error!", "Something went wrong.", "error");
                            });
                    }
                });
            });
        });
    });
</script>

<!-- archive intervention type management -->
<!-- Include SweetAlert Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".archive-int-type-btn").forEach(button => {
            button.addEventListener("click", function() {
                let intTypeId = this.getAttribute("data-int-type-id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "This intervention type will be archived!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, archive it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch('4InterventionTypeManagement/archiveInterventionType.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: 'int_type_id=' + intTypeId
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: "Archived!",
                                        text: "Intervention has been archived successfully.",
                                        icon: "success",
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload(); // Refresh the table
                                    });
                                } else {
                                    Swal.fire("Error!", "Failed to archive intervention.", "error");
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                Swal.fire("Error!", "An error occurred while processing your request.", "error");
                            });
                    }
                });
            });
        });
    });
</script>

<!-- archive classification  -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
      document.addEventListener("DOMContentLoaded", function () {
    const seedTableBody = document.getElementById("seedTableBody");

    // 🔹 Event Delegation for Archive Button
    seedTableBody.addEventListener("click", function (event) {
        const archiveBtn = event.target.closest(".archive-btn"); // Find the closest archive button
        if (!archiveBtn) return;

        let seedId = archiveBtn.getAttribute("data-id");

        Swal.fire({
            title: "Are you sure?",
            text: "This classification will be archived.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, archive it!"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`5SeedTypeManagement/archiveClassification.php?seed_id=${seedId}`, { method: "GET" })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                "Archived!",
                                "The seed type has been archived successfully.",
                                "success"
                            ).then(() => {
                                location.reload(); // Refresh the page after archiving
                            });
                        } else {
                            Swal.fire("Error!", "Failed to archive the seed type.", "error");
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Swal.fire("Error!", "Something went wrong.", "error");
                    });
            }
        });
    });
});
</script>

<!-- archive intervention management -->
<!-- Include SweetAlert Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".archivedistribution-btn").forEach(button => {
            button.addEventListener("click", function() {
                let distributionId = this.getAttribute("data-distribution-id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "This distribution will be archived.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, archive it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`3distributionManagement/archiveDistribution.php?distribution_id=${distributionId}`, {
                                method: "GET"
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(
                                        "Archived!",
                                        "The distribution has been archived.",
                                        "success"
                                    ).then(() => {
                                        location.reload(); // Refresh page to update table
                                    });
                                } else {
                                    Swal.fire("Error!", "Failed to archive the distribution.", "error");
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                Swal.fire("Error!", "Something went wrong.", "error");
                            });
                    }
                });
            });
        });
    });
</script>

<!-- archive cooperative management -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".archivecoop-btn").forEach(button => {
            button.addEventListener("click", function() {
                let coopId = this.getAttribute("data-id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "This cooperative will be archived.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, archive it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`6cooperativeManagement/archiveCooperative.php?coop_id=${coopId}`, {
                                method: "GET"
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(
                                        "Archived!",
                                        "The cooperative has been archived successfully.",
                                        "success"
                                    ).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        "Error!",
                                        "Failed to archive the cooperative.",
                                        "error"
                                    );
                                }
                            })
                            .catch(error => {
                                console.error("Error:", error);
                                Swal.fire("Error!", "Something went wrong.", "error");
                            });
                    }
                });
            });
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let provinceSelect = document.getElementById("update_province");
        let municipalitySelect = document.getElementById("update_municipality");
        let barangaySelect = document.getElementById("update_barangay");

        // Fetch provinces only when the user clicks the dropdown
        provinceSelect.addEventListener("focus", function() {
            if (provinceSelect.options.length === 1) { // Prevents refetching
                fetchProvinces(provinceSelect);
            }
        });

        // Fetch municipalities only when a province is selected
        provinceSelect.addEventListener("change", function() {
            let provinceCode = this.value;
            resetDropdown(municipalitySelect, "Select Municipality");
            resetDropdown(barangaySelect, "Select Barangay");
            fetchMunicipalities(provinceCode, municipalitySelect);
        });

        // Fetch barangays only when a municipality is selected
        municipalitySelect.addEventListener("change", function() {
            let municipalityCode = this.value;
            resetDropdown(barangaySelect, "Select Barangay");
            fetchBarangays(municipalityCode, barangaySelect);
        });

    });

    // Helper function to reset dropdowns
    function resetDropdown(dropdown, placeholder) {
        dropdown.innerHTML = `<option value="" disabled selected>${placeholder}</option>`;
    }

    // Fetch provinces
    function fetchProvinces(dropdown) {
        fetch("6cooperativeManagement/fetch_location.php?type=provinces")
            .then(response => response.json())
            .then(data => {
                data.forEach(province => {
                    let option = document.createElement("option");
                    option.value = province.code;
                    option.textContent = province.name;
                    dropdown.appendChild(option);
                });
            })
            .catch(error => console.error("Error fetching provinces:", error));
    }

    // Fetch municipalities
    function fetchMunicipalities(provinceCode, dropdown) {
        fetch(`6cooperativeManagement/fetch_location.php?type=municipalities&code=${provinceCode}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(municipality => {
                    let option = document.createElement("option");
                    option.value = municipality.code;
                    option.textContent = municipality.name;
                    dropdown.appendChild(option);
                });
            })
            .catch(error => console.error("Error fetching municipalities:", error));
    }

    // Fetch barangays
    function fetchBarangays(municipalityCode, dropdown) {
        fetch(`6cooperativeManagement/fetch_location.php?type=barangays&code=${municipalityCode}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(barangay => {
                    let option = document.createElement("option");
                    option.value = barangay.code;
                    option.textContent = barangay.name;
                    dropdown.appendChild(option);
                });
            })
            .catch(error => console.error("Error fetching barangays:", error));
    }
</script>

<!-- for filtering type of distri if group or individual -->
<script>
    $(document).ready(function() {
        // Filter by type of distribution (Individual/Group)
        $('#filterType').on('change', function() {
            const selectedType = $(this).val(); // Get the selected filter value

            // Update the table header based on the selected filter
            const nameHeader = $('#nameHeader');
            if (selectedType === 'Group') {
                nameHeader.text('Representative Name'); // Change header for groups
            } else {
                nameHeader.text('Name'); // Default header for individuals or all
            }

            // Filter the table rows
            $('#beneficiariesTable tbody tr').each(function() {
                const rowType = $(this).data('type'); // Get the row's type of distribution
                if (selectedType === 'all' || rowType === selectedType) {
                    $(this).show(); // Show the row if it matches the filter
                } else {
                    $(this).hide(); // Hide the row if it doesn't match the filter
                }
            });
        });

        // Search functionality
        $('#searchInput').on('input', function() {
            const searchText = $(this).val().toLowerCase(); // Get the search text
            $('#beneficiariesTable tbody tr').each(function() {
                const rowText = $(this).text().toLowerCase(); // Get the row's text
                if (rowText.includes(searchText)) {
                    $(this).show(); // Show the row if it matches the search
                } else {
                    $(this).hide(); // Hide the row if it doesn't match the search
                }
            });
        });

        // Clear search
        $('#clearSearch').on('click', function() {
            $('#searchInput').val(''); // Clear the search input
            $('#beneficiariesTable tbody tr').show(); // Show all rows
        });
    });
</script>

<!-- fetch locations for update coop mngmnt -->
<script>
    // Helper function to reset dropdowns
    function resetDropdown(dropdown, placeholder) {
        dropdown.innerHTML = `<option value="" disabled selected>${placeholder}</option>`;
    }

    // Fetch provinces
    function fetchProvinces(dropdown) {
        fetch("6cooperativeManagement/fetch_location.php?type=provinces")
            .then(response => response.json())
            .then(data => {
                data.forEach(province => {
                    let option = document.createElement("option");
                    option.value = province.code;
                    option.textContent = province.name;
                    dropdown.appendChild(option);
                });
            })
            .catch(error => console.error("Error fetching provinces:", error));
    }

    // Fetch municipalities
    function fetchMunicipalities(provinceCode, dropdown) {
        fetch(`6cooperativeManagement/fetch_location.php?type=municipalities&code=${provinceCode}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(municipality => {
                    let option = document.createElement("option");
                    option.value = municipality.code;
                    option.textContent = municipality.name;
                    dropdown.appendChild(option);
                });
            })
            .catch(error => console.error("Error fetching municipalities:", error));
    }

    // Fetch barangays
    function fetchBarangays(municipalityCode, dropdown) {
        fetch(`6cooperativeManagement/fetch_location.php?type=barangays&code=${municipalityCode}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(barangay => {
                    let option = document.createElement("option");
                    option.value = barangay.code;
                    option.textContent = barangay.name;
                    dropdown.appendChild(option);
                });
            })
            .catch(error => console.error("Error fetching barangays:", error));
    }
</script>

<!-- fetch distribution date -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var updateModal = document.getElementById("updateDistributionModal");
        updateModal.addEventListener("show.bs.modal", function(event) {
            var button = event.relatedTarget;
            var distributionDate = button.getAttribute("data-distribution-date");

            var dateInput = updateModal.querySelector("#update_distribution_date");
            if (distributionDate) {
                dateInput.value = distributionDate;
            }
        });
    });
</script>

<!-- search users -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let searchInput = document.getElementById("search_id");
        let searchButton = document.getElementById("searchBtn");

        // Autofocus the search input on page load
        searchInput.focus();

        function fetchSearchResults() {
            let searchQuery = searchInput.value.trim();
            let xhr = new XMLHttpRequest();
            xhr.open("GET", "1userManagement/searchUser.php?search=" + encodeURIComponent(searchQuery), true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById("dataTable").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Trigger search on button click
        searchButton.addEventListener("click", function() {
            fetchSearchResults();
        });

        // Trigger search on Enter key press
        searchInput.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault(); // Prevent form submission
                fetchSearchResults();
            }
        });
    });
</script>

<!-- search cooperative -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("search_coop");
        const searchButton = document.getElementById("search_button");
        const dataTable = document.getElementById("dataTable6");

        // Auto-focus the search input on page load
        searchInput.focus();

        function fetchCooperatives() {
            const searchValue = searchInput.value.trim();
            fetch(`6cooperativeManagement/searchCooperative.php?search=${encodeURIComponent(searchValue)}`)
                .then(response => response.text())
                .then(data => {
                    dataTable.innerHTML = data;
                })
                .catch(error => console.error("Error:", error));
        }

        // Trigger search on "Enter" key press
        searchInput.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault(); // Prevent form submission
                fetchCooperatives();
            }
        });

        // Trigger search on button click
        searchButton.addEventListener("click", function() {
            fetchCooperatives();
            searchInput.focus(); // Keep focus on input after search
        });
    });
</script>

<!-- search for beneficiary -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("search_id");
        const searchButton = document.getElementById("searchButton");
        const beneficiaryTable = document.querySelector("#beneficiaryTable tbody");
        const searchForm = document.getElementById("searchForm");

        // Auto-focus the search input on page load
        searchInput.focus();

        function cleanSearchInput(input) {
            return input.replace(/\s+/g, " ").trim(); // Convert multiple spaces to single space
        }

        function fetchBeneficiaries() {
            let searchValue = cleanSearchInput(searchInput.value);

            if (searchValue === "") {
                location.reload(); // Reload to show the full list
                return;
            }

            console.log("Searching for:", searchValue);

            fetch("8beneficiaryManagement/searchBeneficiary.php?search=" + encodeURIComponent(searchValue))
                .then(response => response.text())
                .then(data => {
                    beneficiaryTable.innerHTML = data.trim() ? data : "<tr><td colspan='10' class='text-center'>No results found.</td></tr>";
                    searchInput.focus();
                })
                .catch(error => console.error("Error:", error));
        }


        // Prevent form from reloading
        searchForm.addEventListener("submit", function(event) {
            event.preventDefault();
            fetchBeneficiaries();
        });

        // Trigger search on "Enter" key press
        searchInput.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                fetchBeneficiaries();
            }
        });

        // Trigger search on button click
        searchButton.addEventListener("click", function() {
            fetchBeneficiaries();
        });
    });
</script>

<!-- search for distribution -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("search_id");
        const searchButton = document.getElementById("searchButton");
        const dataTable = document.getElementById("dataTable3");

        // Autofocus on the search input
        searchInput.focus();

        // Function to perform search
        function performSearch() {
            const searchQuery = searchInput.value.trim();

            if (searchQuery.length > 0) {
                fetch("3distributionManagement/searchDistribution.php?q=" + encodeURIComponent(searchQuery))
                    .then(response => response.text())
                    .then(data => {
                        dataTable.innerHTML = data.trim() ? data : "<tr><td colspan='10' class='text-center'>No results found.</td></tr>";
                    })
                    .catch(error => console.error("Error fetching search results:", error));
            } else {
                // Reload the page to restore default data
                location.reload();
            }
        }

        // Search on button click
        searchButton.addEventListener("click", function() {
            performSearch();
        });

        // Search on pressing Enter
        searchInput.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                performSearch();
            }
        });
    });
</script>


<script>
    $(document).ready(function() {
        $("#addBeneficiaryForm").submit(function(e) {
            e.preventDefault(); // Prevent default form submission

            // Collect the form data
            var applicable = []; // Array to store selected applicable checkboxes

            // Collect selected applicable checkboxes
            $('input[name="applicable[]"]:checked').each(function() {
                applicable.push($(this).val());
            });

            // Prepare form data for submission
            var formData = {
                beneficiary_first_name: $('#beneficiary_first_name').val(), // Fetch beneficiary first name
                beneficiary_middle_name: $('#beneficiary_middle_name').val(), // Fetch beneficiary middle name
                beneficiary_last_name: $('#beneficiary_last_name').val(), // Fetch beneficiary last name
                provinceCode: $('#province').val(), // Fetch province code
                provinceName: $('#province option:selected').text(), // Fetch province name
                municipalityCode: $('#municipality').val(), // Fetch municipality code
                municipalityName: $('#municipality option:selected').text(), // Fetch municipality name
                barangayCode: $('#barangay').val(), // Fetch barangay code
                barangayName: $('#barangay option:selected').text(), // Fetch barangay name
                cooperative_id: $('#cooperative').val() || 0, // Fetch cooperative ID, default to 0 if not selected
                rsbsa_no: $('#rsbsa-no').val(), // Fetch RSBSA No.
                sex: $('input[name="sex"]:checked').val(), // Fetch selected sex
                birthdate: $('#birthdate').val(), // Fetch birthdate
                individual_type: $('input[name="individual_type"]:checked').val(), // Fetch selected individual type
                group_type: $('input[name="group_type"]:checked').val(), // Fetch selected group type
                applicable: applicable, // Array of selected applicable checkboxes
                contact_number: $('#contact_number').val(), // Fetch contact number
                beneficiary_category: $('input[name="beneficiary_category"]:checked').val(), // Fetch selected beneficiary category
                streetPurok: $('#streetPurok').val() // Fetch Street/Purok input
            };

            // Check if "Others" was selected for individual type
            if ($('input[name="individual_type"]:checked').val() === 'Others') {
                formData.others_specify = $('#others_specify').val(); // Add the specified "Others" input
            }

            // Check if "Others" was selected for group type
            if ($('input[name="group_type"]:checked').val() === 'Others') {
                formData.group_others_specify = $('#group_others_specify').val(); // Add the specified "Others" input
            }

            // SweetAlert confirmation dialog
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to add this beneficiary?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, submit it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Perform AJAX request
                    $.ajax({
                        url: '8beneficiaryManagement/addBeneficiary.php', // Update with the correct path to your PHP file
                        type: 'POST',
                        data: formData, // Pass the form data to the PHP file
                        dataType: 'json', // Expect a JSON response
                        success: function(response) {
                            if (response.status === "success") {
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonColor: "#28a745",
                                }).then(() => {
                                    $("#addBeneficiaryModal").modal("hide"); // Close modal
                                    $("#addBeneficiaryForm")[0].reset(); // Reset form
                                    location.reload(); // Reload the page after success
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    icon: "error",
                                    confirmButtonColor: "#d33",
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", xhr.responseText);
                            Swal.fire({
                                title: "Error!",
                                text: "An error occurred while processing your request: " + xhr.responseText,
                                icon: "error",
                                confirmButtonColor: "#d33",
                            });
                        }
                    });
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        $("#addUnitForm").submit(function(event) {
            event.preventDefault(); // Prevent default form submission

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to add this unit?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, add it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Serialize form data
                    let formData = $(this).serialize();
                    console.log("Sending Data:", formData); // Debugging

                    // AJAX request to add the unit
                    $.ajax({
                        type: "POST",
                        url: "7unitManagement/add_unit.php", // Change URL to match your unit adding script
                        data: formData,
                        dataType: "json",
                        success: function(response) {
                            console.log("Server Response:", response); // Debugging

                            if (response.success) {
                                Swal.fire({
                                    title: "Success!",
                                    text: response.message,
                                    icon: "success",
                                    confirmButtonColor: "#28a745",
                                }).then(() => {
                                    // Close the modal
                                    $("#addUnitModal").modal("hide");
                                    // Reset the form
                                    $("#addUnitForm")[0].reset();
                                    // Optionally, reload the page
                                    setTimeout(() => {
                                        window.location.href = window.location.href;
                                    }, 1000);
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    icon: "error",
                                    confirmButtonColor: "#d33",
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX Error:", xhr.responseText);
                            Swal.fire({
                                title: "Error!",
                                text: "An error occurred while processing your request.",
                                icon: "error",
                                confirmButtonColor: "#d33",
                            });
                        }
                    });
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Show/hide individual or group fields based on beneficiary type
        $('input[name="beneficiary_type"]').change(function() {
            if ($(this).val() === 'Individual') {
                $('#individualTypeRadio').show();
                $('#groupTypeRadio').show();
                $('#cooperativeInput').hide(); // Hide cooperative input for individual
            } else if ($(this).val() === 'Group') {
                $('#individualTypeRadio').hide();
                $('#groupTypeRadio').show();
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

<!-- for update and fetch classification mngmngt -->
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // When the "Update" button is clicked
        $('.edit-btn').on('click', function() {
            // Get the data attributes from the button
            var seedId = $(this).data('id');
            var seedName = $(this).data('seed-name');
            var interventionName = $(this).data('intervention-name');

            // Populate the modal fields with the data
            $('#seed_id').val(seedId);
            $('#seed_name').val(seedName);
            $('#up_intervention_name').val(interventionName); // Fix: Ensure correct ID is used
        });


        // Handle form submission
        $('#updateSeedForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            // Show a confirmation dialog using SweetAlert2
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to update this seed?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If the user confirms, proceed with the AJAX request
                    var formData = $(this).serialize();

                    // Send the data via AJAX
                    $.ajax({
                        url: '5SeedtypeManagement/updateClassification.php', // PHP script to handle the update
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            // Show a success message using SweetAlert2
                            Swal.fire({
                                title: 'Success!',
                                text: 'Seed updated successfully!',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                $('#editSeedlingModal').modal('hide'); // Hide the modal
                                location.reload(); // Reload the page or update the table
                            });
                        },
                        error: function(xhr, status, error) {
                            // Show an error message using SweetAlert2
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while updating the seed.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });
    });
</script>

<!-- for update unit management -->
<script>
    $(document).ready(function() {
        // When the "Update" button is clicked
        $(document).on("click", ".btn[data-target='#updateUnitModal']", function() {
            // Get the unit details from the button's data attributes
            var unitId = $(this).data('unit-id');
            var unitName = $(this).data('unit-name'); // Get unit name directly from the button

            // Populate the modal fields
            $('#unit_id').val(unitId);
            $('#up_unit_name').val(unitName); // Fix: Ensure correct ID is used in modal

            // Optional: If you still want to fetch from PHP
            $.ajax({
                url: '7unitManagement/fetchUnit.php',
                type: 'GET',
                data: {
                    unit_id: unitId
                },
                dataType: 'json',
                success: function(data) {
                    $('#unit_id').val(data.unit_id);
                    $('#up_unit_name').val(data.unit_name); // Update modal with fetched data
                },
                error: function() {
                    console.error("Error fetching data.");
                }
            });
        });

        // Handle update form submission with confirmation
        $('#updateUnitForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            Swal.fire({
                title: "Are you sure?",
                text: "You are about to update this unit.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, update it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '7unitManagement/updateunit.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(data) {
                            if (data.success) {
                                Swal.fire("Updated!", "The unit has been updated.", "success")
                                    .then(() => location.reload()); // Reload page after success
                            } else {
                                Swal.fire("Error!", data.message, "error");
                            }
                        },
                        error: function() {
                            console.error("Error updating.");
                        }
                    });
                }
            });
        });
    });
</script>

<!-- for filter button in beneficiary management -->
<script>
   function fetchBeneficiaries(category) {
    fetch(`8BeneficiaryManagement/fetch_beneficiaries.php?category=${category}`)
        .then(response => response.json())
        .then(data => {
            let tableBody = document.querySelector("#beneficiaryTable tbody");
            tableBody.innerHTML = "";

            if (!data || data.length === 0) {
                tableBody.innerHTML = "<tr><td colspan='7'>No beneficiaries found.</td></tr>";
                return;
            }

            console.log("Fetched data:", data);

            data.forEach(row => {
                console.log("Beneficiary ID:", row.beneficiary_id);
                let newRow = document.createElement("tr");
                newRow.innerHTML = `
                    <td>${row.fullName}</td>
                    <td>${row.rsbsa_no}</td>
                    <td>${row.province_name}</td>
                    <td>${row.municipality_name}</td>
                    <td>${row.barangay_name}</td>
                    <td>${row.birthdate}</td>
                    <td>
                        <div class="d-flex">
                            <button class="btn btn-success btn-sm update-beneficiary" data-bs-toggle="modal" data-bs-target="#updateBeneficiaryModal" data-id="${row.beneficiary_id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm delete-beneficiary ms-2" data-id="${row.beneficiary_id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <button class="btn btn-info btn-sm view-beneficiary ms-2" data-id="${row.beneficiary_id}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-success btn-sm add-distribution ml-2" id="btnAddDistribution" data-bs-toggle="modal" data-bs-target="#addDistributionModal" data-beneficiary-id="${row.beneficiary_id}">
                                <i class="bx bx-plus"></i>
                                <span>Add Intervention</span>
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(newRow);
            });
        })
        .catch(error => console.error("Error fetching beneficiaries:", error));
}

    document.addEventListener("DOMContentLoaded", function() {
        fetchBeneficiaries("all");

        document.getElementById("btnAll").addEventListener("click", function() {
            fetchBeneficiaries("all");
        });

        document.getElementById("btnIndividual").addEventListener("click", function() {
            fetchBeneficiaries("Individual");
        });

        document.getElementById("btnGroup").addEventListener("click", function() {
            fetchBeneficiaries("Group");
        });
    });
    document.querySelector("#beneficiaryTable tbody").addEventListener("click", function(event) {
    let target = event.target.closest(".update-beneficiary");
    if (target) {
        let beneficiaryId = target.dataset.id;
        openUpdateBeneficiaryModal(beneficiaryId);
    }



        function openUpdateBeneficiaryModal(beneficiaryId) {
            // Fetch the beneficiary data
            fetch(`8beneficiaryManagement/fetch_update_beneficiary.php?id=${beneficiaryId}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        // Populate text inputs
                        document.getElementById('update_beneficiary_first_name').value = data.fname || '';
                        document.getElementById('update_beneficiary_middle_name').value = data.mname || '';
                        document.getElementById('update_beneficiary_last_name').value = data.lname || '';
                        document.getElementById('update_streetPurok').value = data.StreetPurok || '';

                        // Set the raw RSBSA number (no dashes)
                        const rsbsaNo = data.rsbsa_no || '';
                        const rsbsaInput = document.getElementById('update_rsbsa-no');
                        rsbsaInput.value = rsbsaNo; // Set the value without dashes

                        // Call formatRSBSA to display it with dashes in the modal
                        formatRSBSA(rsbsaInput); // Format the RSBSA number with dashes for display only

                        document.getElementById('update_birthdate').value = data.birthdate || '';
                        document.getElementById('update_contact_number').value = data.contact_no || '';

                        // Populate location dropdowns
                        document.getElementById('update_province_name').innerHTML = `<option selected>${data.province_name || 'Select a province'}</option>`;
                        document.getElementById('update_municipality_name').innerHTML = `<option selected>${data.municipality_name || 'Select a municipality'}</option>`;
                        document.getElementById('update_barangay_name').innerHTML = `<option selected>${data.barangay_name || 'Select a barangay'}</option>`;

                        // Set Sex radio button
                        if (data.Sex) {
                            let sexRadio = document.querySelector(`input[name="up_sex"][value="${data.Sex.trim()}"]`);
                            if (sexRadio) {
                                sexRadio.checked = true;
                            }
                        }

                        // Auto-check Beneficiary Category
                        if (data.beneficiary_category) {
                            let categoryRadio = document.getElementById(`update_${data.beneficiary_category.toLowerCase()}`);
                            if (categoryRadio) categoryRadio.checked = true;
                        }

                        // Auto-check Beneficiary Type
                        if (data.beneficiary_type) {
                            let beneficiaryTypeRadio = document.getElementById(`update_${data.beneficiary_type.toLowerCase()}`);
                            if (beneficiaryTypeRadio) beneficiaryTypeRadio.checked = true;
                        }

                        // Toggle Individual or Group visibility
                        toggleBeneficiaryType();

                        // Handle Individual Type
                        if (data.beneficiary_type === 'Individual' && data.individual_type) {
                            let individualTypeRadio = document.getElementById(`update_${data.individual_type.toLowerCase()}`);
                            if (individualTypeRadio) individualTypeRadio.checked = true;
                        }

                        // Handle Group Type
                        if (data.beneficiary_type === 'Group' && data.group_type) {
                            let groupTypeRadio = document.getElementById(`update_${data.group_type.toLowerCase()}`);
                            if (groupTypeRadio) groupTypeRadio.checked = true;
                        }

                        // Ensure "Others" fields are handled properly
                        handleOthersVisibility('individual', data.individual_type);
                        handleOthersVisibility('group', data.group_type);

                        // Auto-check "Others" if value is not in predefined lists
                        handleAutoCheckOthers(data);

                        // Check applicable checkboxes
                        let applicable = data.if_applicable ? data.if_applicable.split(',') : [];
                        document.querySelectorAll('[name="applicable[]"]').forEach(checkbox => {
                            checkbox.checked = applicable.includes(checkbox.value);
                        });

                        // Open the modal
                        const modalElement = document.getElementById('updateBeneficiaryModal');
                        if (modalElement) {
                            let modal = new bootstrap.Modal(modalElement);
                            modal.show();
                        }
                    } else {
                        alert("Error: Beneficiary data not found.");
                    }
                })
                .catch(error => {
                    console.error('Error fetching beneficiary data:', error);
                    alert("An error occurred while fetching the beneficiary data.");
                });


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

            // Function to show/hide "Others" input field
            function handleOthersVisibility(category, value) {
                let inputDiv, inputField;

                if (category === 'individual') {
                    inputDiv = document.getElementById('updateOthersInput');
                    inputField = document.getElementById('update_others_specify');
                } else if (category === 'group') {
                    inputDiv = document.getElementById('updateGroupOthersInput');
                    inputField = document.getElementById('update_group_others_specify');
                }

                if (inputDiv && inputField) {
                    inputDiv.style.display = value === 'Others' ? 'block' : 'none';
                    if (value === 'Others') inputField.value = '';
                }
            }

            // Function to toggle between Individual and Group visibility
            function toggleBeneficiaryType() {
                let individualRadio = document.getElementById("update_individual");
                let groupRadio = document.getElementById("update_group");
                let individualTypeDiv = document.getElementById("updateIndividualTypeRadio");
                let groupTypeDiv = document.getElementById("updateGroupTypeRadio");

                individualTypeDiv.style.display = individualRadio.checked ? "block" : "none";
                groupTypeDiv.style.display = groupRadio.checked ? "block" : "none";
            }

            function handleAutoCheckOthers(data) {
                console.log("Received data:", data); // Debugging

                const individualTypes = ["Farmer", "Fisher", "AEW"];
                const groupTypes = ["FCA", "Cluster", "LGU", "School"];

                // Handle Individual Type
                if (data.beneficiary_category === "Individual") {
                    let othersRadio = document.getElementById("update_others");
                    let othersInputDiv = document.getElementById("updateOthersInput");
                    let othersInput = document.getElementById("update_others_specify");

                    let individualRadios = document.getElementsByName("individual_type");
                    let foundMatch = false;

                    console.log("Checking individual_type:", data.beneficiary_type);

                    individualRadios.forEach(radio => {
                        if (radio.value === data.beneficiary_type) {
                            radio.checked = true;
                            foundMatch = true;
                        } else {
                            radio.checked = false;
                        }
                    });

                    if (!foundMatch) {
                        console.log("No match found. Selecting 'Others'.");
                        othersRadio.checked = true;
                        othersInputDiv.style.display = "block";
                        othersInput.value = data.beneficiary_type || "";
                    } else {
                        console.log("Match found. Hiding 'Others' input.");
                        othersRadio.checked = false;
                        othersInputDiv.style.display = "none";
                        othersInput.value = "";
                    }

                    // Hide Cooperative input when Individual is selected
                    document.getElementById("updateCooperativeInput").style.display = "none";
                }

                // Handle Group Type
                if (data.beneficiary_category === "Group") {
                    let groupOthersRadio = document.getElementById("update_group_others");
                    let groupOthersInputDiv = document.getElementById("updateGroupOthersInput");
                    let groupOthersInput = document.getElementById("update_group_others_specify");

                    let groupRadios = document.getElementsByName("group_type");
                    let foundMatch = false;

                    console.log("Checking group_type:", data.beneficiary_type);

                    groupRadios.forEach(radio => {
                        if (radio.value === data.beneficiary_type) {
                            radio.checked = true;
                            foundMatch = true;
                        } else {
                            radio.checked = false;
                        }
                    });

                    if (!foundMatch) {
                        console.log("No match found for group type. Selecting 'Others'.");
                        groupOthersRadio.checked = true;
                        groupOthersInputDiv.style.display = "block";
                        groupOthersInput.value = data.beneficiary_type || "";
                    } else {
                        console.log("Match found for group type. Hiding 'Others' input.");
                        groupOthersRadio.checked = false;
                        groupOthersInputDiv.style.display = "none";
                        groupOthersInput.value = "";
                    }

                    // Show Cooperative input when Group is selected
                    document.getElementById("updateCooperativeInput").style.display = "block";

                    // Fetch and display the cooperative name
                    fetchCooperatives(data.coop_id);
                }
            }

            // Function to fetch and populate the Cooperative dropdown
            function fetchCooperatives(selectedCoopId) {
                fetch("8beneficiaryManagement/get_cooperatives.php") // Replace with your actual PHP endpoint
                    .then(response => response.json())
                    .then(cooperatives => {
                        let select = document.getElementById("update_cooperative");
                        select.innerHTML = '<option value="" disabled>Select a Cooperative</option>'; // Reset options

                        cooperatives.forEach(coop => {
                            let option = document.createElement("option");
                            option.value = coop.id;
                            option.textContent = coop.name;

                            // Select the correct cooperative if it matches the data
                            if (coop.id == selectedCoopId) {
                                option.selected = true;
                            }

                            select.appendChild(option);
                        });
                    })
                    .catch(error => console.error("Error fetching cooperatives:", error));
            }
        }
    });
</script>

<!-- fetch for dashboard benefeciaries -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const filterButtons = document.querySelectorAll(".filter-btn");
        const tableRows = document.querySelectorAll("tbody tr");

        filterButtons.forEach((button) => {
            button.addEventListener("click", function() {
                const filter = this.getAttribute("data-filter");

                // Remove 'active' class from all buttons and add to clicked one
                filterButtons.forEach((btn) => btn.classList.remove("active"));
                this.classList.add("active");

                // Loop through table rows and filter based on category
                tableRows.forEach((row) => {
                    const beneficiaryType = row.getAttribute("data-type");

                    if (filter === "all" || beneficiaryType === filter) {
                        row.style.display = ""; // Show row
                    } else {
                        row.style.display = "none"; // Hide row
                    }
                });
            });
        });
    });
    
</script>
<!-- for viewing the interventions received -->
<script>
    $(document).ready(function() {
        // Event listener for the "View Interventions" button
        $('.view-interventions-btn').on('click', function() {
            var beneficiaryId = $(this).data('beneficiary-id');
            console.log("Beneficiary ID:", beneficiaryId);

            // Show the modal
            $('#viewInterventionsModal').modal('show');

            // Fetch interventions for the selected beneficiary
            $.ajax({
                url: '2interventionManagement/fetch_intervention_dashboard.php',
                type: 'GET',
                data: {
                    beneficiary_id: beneficiaryId
                },
                success: function(response) {
                    $('#modalContent').html(response);
                },
                error: function(xhr, status, error) {
                    $('#modalContent').html('<p class="text-danger">Error loading interventions.</p>');
                }
            });
        });

        // Manually close the modal (for debugging)
        $('#viewInterventionsModal .close, #viewInterventionsModal .btn-secondary').on('click', function() {
            $('#viewInterventionsModal').modal('hide');
        });
    });
</script>

<!-- for filtering type of distri if group or individual -->
<script>
    $(document).ready(function() {
        // Filter by type of distribution (Individual/Group)
        $('#filterType').on('change', function() {
            const selectedType = $(this).val(); // Get the selected filter value

            // Update the table header based on the selected filter
            const nameHeader = $('#nameHeader');
            if (selectedType === 'Group') {
                nameHeader.text('Representative Name'); // Change header for groups
            } else {
                nameHeader.text('Name'); // Default header for individuals or all
            }

            // Filter the table rows
            $('#beneficiariesTable tbody tr').each(function() {
                const rowType = $(this).data('type'); // Get the row's type of distribution
                if (selectedType === 'all' || rowType === selectedType) {
                    $(this).show(); // Show the row if it matches the filter
                } else {
                    $(this).hide(); // Hide the row if it doesn't match the filter
                }
            });
        });

        // Search functionality
        $('#searchInput').on('input', function() {
            const searchText = $(this).val().toLowerCase(); // Get the search text
            $('#beneficiariesTable tbody tr').each(function() {
                const rowText = $(this).text().toLowerCase(); // Get the row's text
                if (rowText.includes(searchText)) {
                    $(this).show(); // Show the row if it matches the search
                } else {
                    $(this).hide(); // Hide the row if it doesn't match the search
                }
            });
        });

        // Clear search
        $('#clearSearch').on('click', function() {
            $('#searchInput').val(''); // Clear the search input
            $('#beneficiariesTable tbody tr').show(); // Show all rows
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search_id");

    searchInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            searchClassificationTable();
        }
    });

    function searchClassificationTable() {
        const query = searchInput.value.trim();
        const url = `5seedTypeManagement/searchClassification.php?search=${encodeURIComponent(query)}`;

        fetch(url)
            .then(response => response.text())
            .then(data => {
                document.getElementById("seedTableBody").innerHTML = data;
                searchInput.focus();
            })
            .catch(error => console.error("Error fetching data:", error));
    }

    // Fix: Ensure modal gets correct data
    document.getElementById("seedTableBody").addEventListener("click", function (event) {
        if (event.target.closest(".edit-btn")) {
            const btn = event.target.closest(".edit-btn");
            const seedId = btn.getAttribute("data-id");
            const seedName = btn.getAttribute("data-seed-name");
            const interventionName = btn.getAttribute("data-intervention-name");

            // Assign values to modal fields
            document.getElementById("seed_id").value = seedId;
            document.getElementById("seed_name").value = seedName;
            document.getElementById("up_intervention_name").value = interventionName;

            // Ensure the modal is properly shown
            const modalElement = document.getElementById("editSeedlingModal");
            const modal = new bootstrap.Modal(modalElement);
            modal.show();

            // Fix black screen: Ensure modal backdrop is removed when closed
            modalElement.addEventListener("hidden.bs.modal", function () {
                document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());
                document.body.classList.remove("modal-open");
                document.body.style.overflow = "auto"; // Restore scroll
            });
        }
    });
});



</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let modalElement = document.getElementById("updateCooperativeModal");
    let modalInstance = new bootstrap.Modal(modalElement, { backdrop: "static", keyboard: false });

    // Function to properly close the modal and remove backdrops
    function closeModalProperly() {
        modalInstance.hide(); // Hide the modal

        setTimeout(() => {
            document.body.classList.remove("modal-open"); // Remove 'modal-open' class
            let backdrops = document.querySelectorAll(".modal-backdrop");
            backdrops.forEach(backdrop => backdrop.remove()); // Remove any leftover backdrops
        }, 200);
    }

    // Attach event listeners to all close buttons inside modal
    document.querySelectorAll("#closeUpdateModalBtn, #closeUpdateModalBtn2").forEach(button => {
        button.addEventListener("click", closeModalProperly);
    });

    // Prevent clicking outside from causing modal issues
    modalElement.addEventListener("hidden.bs.modal", function () {  
        closeModalProperly();
    });

    // Click outside the modal closes it properly
    modalElement.addEventListener("click", function (event) {
        if (event.target === modalElement) {
            closeModalProperly();
        }
    });

    // Fetch and open modal
    document.querySelectorAll(".update-btn").forEach(button => {
        button.addEventListener("click", function () {
            let coopId = this.getAttribute("data-id").trim();

            fetch("6cooperativeManagement/fetch_cooperative.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "coop_id=" + encodeURIComponent(coopId)
            })
            .then(response => response.json())
            .then(data => {
                if (!data.error) {
                    document.querySelector("#update_id").value = data.coop_id || "";
                    document.querySelector("#update_cooperative_name").value = data.cooperative_name || "";

                    // Populate dropdowns
                    let provinceDropdown = document.querySelector("#update_province");
                    provinceDropdown.innerHTML = `<option value="${data.province_name || ''}">${data.province_name || 'Select Province'}</option>`;

                    let municipalityDropdown = document.querySelector("#update_municipality");
                    municipalityDropdown.innerHTML = `<option value="${data.municipality_name || ''}">${data.municipality_name || 'Select Municipality'}</option>`;

                    let barangayDropdown = document.querySelector("#update_barangay");
                    barangayDropdown.innerHTML = `<option value="${data.barangay_name || ''}">${data.barangay_name || 'Select Barangay'}</option>`;

                    // Show modal
                    modalInstance.show();
                } else {
                    Swal.fire("Error!", data.message, "error");
                }
            })
            .catch(error => {
                console.error("Error fetching data:", error);
                Swal.fire("Error!", "Something went wrong.", "error");
            });
        });
    });

    // Handle update form submission
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
                    if (data.success) {
                        Swal.fire({
                            title: "Updated!",
                            text: "Cooperative has been updated successfully.",
                            icon: "success"
                        }).then(closeModalProperly); // Close modal properly
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

