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
                INTERVENTION MANAGEMENT
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
            <!-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class=" mb-0 text-gray-800 fw-bolder">Intervention Management</h1>
            </div> -->
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="background-color: #0D7C66;">
                    <div class="d-flex align-items-center">

                        <!-- Button -->
                        <button type="button" class="btn d-flex align-items-center gap-2 rounded-pill shadow-sm"
                            style="background-color: #DCFFB7; color: black; border: none; padding: 8px 16px;"
                            id="adminbtn" data-bs-toggle="modal" data-bs-target="#addInterventionModal">
                            <i class='bx bx-plus' style="font-size: 1.2rem;"></i>
                            <span>Add Intervention</span>
                        </button>

                        <form class="d-none d-sm-inline-block form-inline ml-auto my-2 my-md-0 mw-100 navbar-search custom-search-form">
                            <div class="input-group">
                                <input type="text" id="search_id" class="form-control bg-light border-0 small"
                                    placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" onkeyup="searchInterventionTable()">
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
                        <div style="max-height: 430px; overflow: auto; width: 100%; border: 1px solid #ddd; border-radius: 10px;">
                            <table class="table table-bordered text-center" width="100%" cellspacing="0">
                                <thead class="thead" style="background-color: #0D7C66; color: white;">
                                    <tr>
                                        <th>Intervention Name</th>
                                        <th>Classification</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Quantity Left</th>
                                        <th>Indicator</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="dataTable2" style="color: black;">
                                    <?php
                                    include('../conn.php'); // Include database connection

                                    // Ensure the user is logged in
                                    if (!isset($_SESSION['uid'])) {
                                        echo "<tr><td colspan='8' class='text-center'>User not logged in.</td></tr>";
                                        exit;
                                    }

                                    $user_id = $_SESSION['uid'];

                                    // Retrieve the user's station ID
                                    $stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
                                    $stationQuery->bind_param("i", $user_id);
                                    $stationQuery->execute();
                                    $stationQuery->store_result();

                                    if ($stationQuery->num_rows > 0) {
                                        $stationQuery->bind_result($station_name);
                                        $stationQuery->fetch();
                                    } else {
                                        echo "<tr><td colspan='8' class='text-center'>No station assigned to this user.</td></tr>";
                                        exit;
                                    }
                                    $stationQuery->close();

                                    // Pagination Setup
                                    $entries_per_page = 10; // Number of records per page
                                    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                                    $offset = ($page - 1) * $entries_per_page; // Calculate the starting row

                                    // Get total records count for pagination
                                    $countQuery = $conn->prepare("SELECT COUNT(*) FROM tbl_intervention_inventory WHERE station_id = ?");
                                    $countQuery->bind_param("i", $station_name);
                                    $countQuery->execute();
                                    $countQuery->bind_result($total_records);
                                    $countQuery->fetch();
                                    $countQuery->close();

                                    // Ensure $total_records is not zero to avoid division by zero error
                                    $total_records = max(1, $total_records);
                                    $total_pages = ceil($total_records / $entries_per_page);

                                    // SQL query to fetch paginated data with INNER JOIN on tbl_unit
                                     $sql = "SELECT 
                                        tbl_intervention_inventory.intervention_id,
                                        tbl_intervention_inventory.int_type_id, 
                                        tbl_intervention_inventory.description, 
                                        tbl_intervention_inventory.quantity, 
                                        tbl_intervention_inventory.quantity_left,
                                        tbl_intervention_inventory.seed_id,
                                        tbl_unit.unit_name AS unit_name,
                                        tbl_intervention_type.intervention_name AS intervention_name,
                                        tbl_seed_type.seed_name AS seedling_name
                                    FROM tbl_intervention_inventory
                                    INNER JOIN tbl_intervention_type ON tbl_intervention_inventory.int_type_id = tbl_intervention_type.int_type_id
                                    LEFT JOIN tbl_seed_type ON tbl_intervention_inventory.seed_id = tbl_seed_type.seed_id
                                    INNER JOIN tbl_unit ON tbl_intervention_inventory.unit_id = tbl_unit.unit_id
                                    WHERE tbl_intervention_inventory.station_id = ?
                                        AND tbl_intervention_inventory.archived_at IS NULL  -- Exclude archived records
                                    LIMIT ? OFFSET ?";

                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("iii", $station_name, $entries_per_page, $offset);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    // Check if records exist
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['intervention_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['seedling_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                                <td><?php echo htmlspecialchars($row['quantity_left']); ?></td>
                                                <td><?php echo htmlspecialchars($row['unit_name']); ?></td> <!-- Fetch Unit Name -->
                                                <td>
                                                    <a href="#" class="btn" style="background-color: #DCFFB7; color: black;"
                                                        data-bs-toggle="modal" data-bs-target="#updateInterventionModal"
                                                        data-intervention-id="<?php echo htmlspecialchars($row['intervention_id']); ?>">
                                                        Update
                                                    </a>

                                                    <button class="btn btn-warning archiveintervention-btn"
                                                        data-intervention-id="<?php echo htmlspecialchars($row['intervention_id']); ?>">
                                                        Archive
                                                    </button>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='8' class='text-center'>No records found</td></tr>";
                                    }

                                    $stmt->close();
                                    $conn->close();
                                    ?>
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <nav>
                        <ul class="pagination justify-content-center mt-3">
                            <?php
                            $total_pages = ceil($total_records / $entries_per_page);

                            // Previous button with left arrow
                            if ($page > 1) {
                                echo "<li class='page-item'><a class='page-link' href='?page=" . ($page - 1) . "'><i class='fas fa-chevron-left'></i></a></li>";
                            } else {
                                echo "<li class='page-item disabled'><a class='page-link'><i class='fas fa-chevron-left'></i></a></li>";
                            }
                            // Next button with right arrow
                            if ($page < $total_pages) {
                                echo "<li class='page-item'><a class='page-link' href='?page=" . ($page + 1) . "'><i class='fas fa-chevron-right'></i></a></li>";
                            } else {
                                echo "<li class='page-item disabled'><a class='page-link'><i class='fas fa-chevron-right'></i></a></li>";
                            }
                            ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- /.container-fluid -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Reduce table font size */
        .table {
            font-size: 14px;
            /* Adjust as needed */
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
        .form-control,
        .btn {
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
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
    <?php include 'modals/modal_for_intervention.php'; ?>
