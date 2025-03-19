<?php
include('includes/header.php');
include('includes/navbar.php');
?>



<link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
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
            <div class="d-flex justify-content-start mb-3">
                <button class="btn btn-outline-primary px-4 py-2 me-2 filter-btn active" id="btnAll">All</button>
                <button class="btn btn-outline-primary px-4 py-2 me-2 filter-btn" id="btnIndividual">Individual</button> 
                <button class="btn btn-outline-success px-4 py-2 filter-btn" id="btnGroup">Group</button>
            </div>
                <div style="max-height: 430px; overflow: auto; width: 100%; border: 1px solid #ddd; border-radius: 10px;">
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
                            include('../conn.php');

                            function formatRsbsaNo($rsbsa_no) {
                                if (strlen($rsbsa_no) === 15) {
                                    return substr($rsbsa_no, 0, 2) . '-' .
                                        substr($rsbsa_no, 2, 2) . '-' .
                                        substr($rsbsa_no, 4, 2) . '-' .
                                        substr($rsbsa_no, 6, 3) . '-' .
                                        substr($rsbsa_no, 9, 6);
                                }
                                return $rsbsa_no;
                            }

                            $query = "SELECT beneficiary_id, fname, mname, lname, rsbsa_no, province_name, municipality_name, barangay_name, birthdate FROM tbl_beneficiary";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) :
                                while ($row = $result->fetch_assoc()) :
                                    $fullName = $row['fname'] . ' ' . (!empty($row['mname']) ? $row['mname'] . ' ' : '') . $row['lname'];
                                    $formattedBirthdate = date('F j, Y', strtotime($row['birthdate']));
                                    $rsbsa_no = !empty($row['rsbsa_no']) ? formatRsbsaNo($row['rsbsa_no']) : 'N/A';
                            ?>
                                    <tr>
                                        <td><?= $fullName ?></td>
                                        <td><?= $rsbsa_no ?></td>
                                        <td><?= $row['province_name'] ?></td>
                                        <td><?= $row['municipality_name'] ?></td>
                                        <td><?= $row['barangay_name'] ?></td>
                                        <td><?= $formattedBirthdate ?></td>
                                        <td>
                                             <button class="btn btn-primary" onclick="openUpdateBeneficiaryModal(<?= $row['beneficiary_id'] ?>)">
                                                Update
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteBeneficiary(<?= $row['beneficiary_id'] ?>)">
                                                Delete
                                            </button>
                                            <!-- View Beneficiary Button -->
                                            <button class="btn btn-info btn-sm view-beneficiary" data-id="<?= $row['beneficiary_id'] ?>">View</button>
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addDistributionModal" 
                                                data-beneficiary-id="<?= $row['beneficiary_id'] ?>">
                                                <i class="bx bx-plus"></i>
                                                <span>Add Intervention</span>
                                            </button>
                                        </td>
                                    </tr>
                            <?php
                                endwhile;
                            else :
                            ?>
                                <tr>
                                    <td colspan="7">No beneficiaries found.</td>
                                </tr>
                            <?php
                            endif;

                            $conn->close();
                            ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


    <style>
        /* Reduce table font size */
        .table {
            font-size: 14px; /* Adjust as needed */
        }

        /* Reduce navbar text size */
        .navbar-brand {
            font-size: 16px !important;
        }

        /* Reduce pagination font size */
        .pagination .page-link {
            font-size: 14px;
        }

        /* Reduce input and button font size */
        .form-control, .btn {
            font-size: 14px;
        }
        /* Header row */
        .table thead {
            background-color: #0D7C66;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Header cells */
        .table thead th {
            padding: 10px;
            text-align: center;
            border-bottom: 2px solid #ffffff;
        }

        /* Body rows */
        .table tbody tr {
            background-color: #f8f9fa;
            transition: background-color 0.3s ease;
        }

        /* Alternate row colors */
        .table tbody tr:nth-child(even) {
            background-color: #e9ecef;
        }

        /* Hover effect */
        .table tbody tr:hover {
            background-color: #cde8e5;
        }

        /* Table data cells */
        .table tbody td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
            text-align: center;
            color: #333;
        }

        /* Action button */
        .table tbody td .btn-info {
            background-color: #0D7C66;
            border: none;
            padding: 5px 12px;
            border-radius: 6px;
            color: white;
            transition: 0.3s ease;
        }

        .table tbody td .btn-info:hover {
            background-color: #066395;
        }

        #btnAddDistribution:hover {                                                                                 
            background-color: #C8E6A0 !important;
            /* Slightly darker green */
        }
    .custom-search-form {
        margin-left: 30px; /* Adjust as needed */
    }   
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        dropdownElementList.map(function (dropdownToggleEl) {
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
