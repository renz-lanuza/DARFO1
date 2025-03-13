<?php
include('includes/header.php');
include('includes/navbar.php');
?>
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">
        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <span class="navbar-brand font-weight-bold" style="font-size: 18px; padding-left: 55px; color: Black; font-family: 'Roboto', sans-serif;">
                BENEFICIARY MANAGEMENT
            </span>

            <div style="padding-left: 20px; color: black; display: flex; flex-direction: column; line-height: 1;">
                <?php
                if (isset($_SESSION['fname']) && isset($_SESSION['lname'])) {
                    $fullName = htmlspecialchars($_SESSION['fname']) . ' ' . htmlspecialchars($_SESSION['mname']) . ' ' . htmlspecialchars($_SESSION['lname']);
                    echo '<span class="navbar-brand font-weight-bold" style="font-size: 18px;">' . $fullName . '</span>';
                }

                if (isset($_SESSION['station_name'])) {
                    echo '<span class="navbar-brand" style="font-size: 16px;">' . htmlspecialchars($_SESSION['station_name']) . '</span>';
                }
                ?>
            </div>

            <!-- Topbar Navbar -->
            <div class="topbar-divider d-none d-sm-block"></div>

            <ul class="navbar-nav ml-auto">
                <!-- Nav Item - User Information -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-none d-lg-inline text-black small font-weight-bold" style="color: grey">
                            <?php
                            // Retrieve the username from the session
                            $username = $_SESSION['user'];
                            echo 'Logged in as ' . htmlspecialchars($username);
                            ?>
                        </span>
                        <i style="font-size: 40px; color: black;" class="bx bxs-user-circle"></i>
                    </a>

                    <!-- Dropdown - User Information -->
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                        aria-labelledby="userDropdown">
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" id="logout-button">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                        <form id="logout-form" action="logout.php" method="POST" style="display: none;">
                            <input type="hidden" name="logout" value="1">
                        </form>
                    </div>
                </li>
            </ul>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <div class="card shadow mb-4">
                <div class="card-header py-3" style="background-color: #0D7C66;">
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn d-flex align-items-center gap-2 rounded-pill shadow-sm"
                            style="background-color: #DCFFB7; color: black; border: none; padding: 8px 16px;"
                            id="btnAddBeneficiary" data-bs-toggle="modal" data-bs-target="#addBeneficiaryModal">
                            <i class='bx bx-plus' style="font-size: 1.2rem;"></i>
                            <span>Add Beneficiary</span>
                        </button>

                        <form class="d-none d-sm-inline-block form-inline ml-auto my-2 my-md-0 mw-100 navbar-search custom-search-form">
                            <div class="input-group">
                                <input type="text" id="search_id" class="form-control bg-light border-0 small"
                                    placeholder="Search for beneficiaries..." aria-label="Search" aria-describedby="basic-addon2" onkeyup="searchBeneficiaryTable()">
                                <div class="input-group-append">
                                    <button class="btn text-white" style="background-color: #DCFFB7;" type="button">
                                        <i class="fas fa-search fa-sm" style="color: black;"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="tab1" class="tab-content active">
                    <div class="card-body">
                        <div style="max-height: 430px; overflow: auto; width: 100%; border: 1px solid #ddd;">
                            <!-- Table to display beneficiaries -->
                            <table class="table table-bordered text-center" width="100%" cellspacing="0" id="beneficiaryTable">
                                <thead class="thead" style="background-color: #0D7C66; color: white;">

                                    <tr>
                                        <th>Full Name</th>
                                        <th>RSBSA No.</th>
                                        <th>Province</th>
                                        <th>Municipality</th>
                                        <th>Barangay</th>
                                        <th>Birthdate</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Include your database connection file
                                    include('../conn.php');

                                    // Function to format RSBSA number
                                    function formatRsbsaNo($rsbsa_no)
                                    {
                                        // Check if the RSBSA number is valid and has the correct length
                                        if (strlen($rsbsa_no) === 15) {
                                            return substr($rsbsa_no, 0, 2) . '-' .
                                                substr($rsbsa_no, 2, 2) . '-' .
                                                substr($rsbsa_no, 4, 2) . '-' .
                                                substr($rsbsa_no, 6, 3) . '-' .
                                                substr($rsbsa_no, 9, 6);
                                        }
                                        return $rsbsa_no; // Return as is if not valid
                                    }

                                    // Fetch beneficiaries from the database
                                    $query = "SELECT beneficiary_id, fname, mname, lname, rsbsa_no, province_name, municipality_name, barangay_name, birthdate FROM tbl_beneficiary"; // Adjust the query as needed
                                    $result = $conn->query($query);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            // Construct the full name
                                            $fullName = $row['fname']; // First name
                                            if (!empty($row['mname'])) {
                                                $fullName .= ' ' . $row['mname']; // Add middle name if not empty
                                            }
                                            $fullName .= ' ' . $row['lname']; // Add last name

                                            // Format the birthdate (optional)
                                            $formattedBirthdate = date('F j, Y', strtotime($row['birthdate'])); // Format as needed

                                            // Format the RSBSA number
                                            $rsbsa_no = !empty($row['rsbsa_no']) ? formatRsbsaNo($row['rsbsa_no']) : 'N/A';

                                            // Output the table row
                                            echo "<tr>
                                                    <td>{$fullName}</td>
                                                    <td>{$rsbsa_no}</td>
                                                    <td>{$row['province_name']}</td>
                                                    <td>{$row['municipality_name']}</td>
                                                    <td>{$row['barangay_name']}</td>
                                                    <td>{$formattedBirthdate}</td>
                                                    <td>
                                                        <button class='btn btn-primary btn-sm'>Edit</button>
                                                        <button class='btn btn-danger btn-sm'>Delete</button>
                                                        <button class='btn btn-info btn-sm' onclick='viewBeneficiary({$row['beneficiary_id']})'>View</button>
                                                        <!-- Add Distribution Button with Beneficiary ID -->
                                                        <button type='button' class='btn btn-success btn-sm' id='btnAddDistribution' data-bs-toggle='modal' data-bs-target='#addDistributionModal' data-beneficiary-id='{$row['beneficiary_id']}'>
                                                            <i class='bx bx-plus'></i>
                                                            <span>Add Distribution</span>
                                                        </button>
                                                    </td>
                                                  </tr>";
                                                                                }
                                                                            } else {
                                                                                // If no beneficiaries are found
                                                                                echo "<tr>
                                                    <td colspan='7'>No beneficiaries found.</td>
                                                  </tr>";
                                                                            }
                                    // Close the database connection
                                    $conn->close();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            dropdownElementList.map(function(dropdownToggleEl) {
                new bootstrap.Dropdown(dropdownToggleEl);
            });
        });
    </script>
    <?php
    include('includes/scripts.php');
    include('includes/footer.php');
    ?>
    <?php include 'modals/modal_for_beneficiary.php'; ?>
    <?php include 'modals/modal_for_viewing_beneficiary.php'; ?>
    <?php include 'modals/modal_for_distribution.php'; ?>
