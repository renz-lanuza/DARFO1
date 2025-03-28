<?php
include('includes/header.php');
include('includes/navbar.php');

?>
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> -->

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
                DISTRIBUTION MANAGEMENT
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
                    <h1 class=" mb-0 text-gray-800 fw-bolder">Distribution Management</h1>
                </div> -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3" style="background-color: #0D7C66;">
                        <div class="d-flex align-items-center gap-3">
                            <!-- Print Report Button -->
                            <button type="button" class="btn d-flex align-items-center gap-2 rounded-pill shadow-sm" 
                                    style="background-color: #DCFFB7; color: black; border: none; padding: 8px 16px;"
                                    id="btnPrintReport" onclick="printReport()">
                                <i class='bx bx-printer' style="font-size: 1.2rem;"></i>
                                <span>Print Report</span>
                            </button>

                            <!-- Date Filter Inputs -->
                            <div class="d-flex align-items-center gap-4">
                                <label for="start_date" class="text-white ml-3 mr- 2">Start: </label>
                                <input type="date" id="start_date" class="form-control bg-light border-0 small ml-2">
                                
                                <label for="end_date" class="text-white ml-2">End: </label>
                                <input type="date" id="end_date" class="form-control bg-light border-0 small ml-2">
                           
                                <!-- <button id="filterButton" class="btn text-white" style="background-color: #DCFFB7;" type="button" onclick="filterData()">
                                    <i class="fas fa-filter fa-sm" style="color: black;"></i>
                                </button> -->
                            </div>

                            <!-- Search Bar (Right Aligned) -->
                            <form class="d-none d-sm-inline-block form-inline ml-auto my-2 my-md-0 mw-100 navbar-search custom-search-form">
                                <div class="input-group">
                                    <input type="text" id="search_id" class="form-control bg-light border-0 small"
                                        placeholder="Search for distributed..." aria-label="Search" aria-describedby="basic-addon2">
                                    
                                    <div class="input-group-append">
                                        <button id="searchButton" class="btn text-white" style="background-color: #DCFFB7;" type="button" onclick="searchData()">
                                            <i class="fas fa-search fa-sm" style="color: black;"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                <div id="tab1" class="tab-content active">
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php
                            // Database connection
                            include('../conn.php');

                            // Retrieve the user's station ID from the session
                            if (!isset($_SESSION['uid'])) {
                                echo "<tr><td colspan='11' class='text-center'>User not logged in.</td></tr>";
                                exit;
                            }

                            $user_id = $_SESSION['uid'];
                            $stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
                            $stationQuery->bind_param("i", $user_id);
                            $stationQuery->execute();
                            $stationQuery->bind_result($station_id);
                            $stationQuery->fetch();
                            $stationQuery->close();

                            $entries_per_page = 10;
                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $offset = ($page - 1) * $entries_per_page;

                            $countQuery = "SELECT COUNT(*) as total FROM tbl_distribution WHERE station_id = ?";
                            $stmt_count = $conn->prepare($countQuery);
                            $stmt_count->bind_param("i", $station_id);
                            $stmt_count->execute();
                            $count_result = $stmt_count->get_result();
                            $total_entries = $count_result->fetch_assoc()['total'];
                            $stmt_count->close();

                            // Define the query to fetch data filtered by station ID
                            $query = "SELECT 
                                            d.distribution_id,
                                            d.distribution_date,
                                            CONCAT(b.fname, ' ', IFNULL(b.mname, ''), ' ', b.lname) AS beneficiary_name,
                                            b.province_name,
                                            b.municipality_name,
                                            b.barangay_name,
                                            IF(b.coop_id = 0, 'N/A', c.cooperative_name) AS cooperative_name,
                                            it.intervention_name,
                                            st.seed_name,
                                            d.quantity
                                        FROM 
                                            tbl_distribution AS d
                                        INNER JOIN 
                                            tbl_beneficiary AS b ON d.beneficiary_id = b.beneficiary_id
                                        INNER JOIN 
                                            tbl_seed_type AS st ON d.seed_id = st.seed_id
                                        INNER JOIN 
                                            tbl_intervention_type AS it ON d.intervention_id = it.int_type_id
                                        LEFT JOIN 
                                            tbl_cooperative AS c ON b.coop_id = c.coop_id
                                        WHERE 
                                            d.station_id = ? 
                                            AND (d.archived_at IS NULL OR d.archived_at = '') -- Ensures archived records are not shown
                                        ORDER BY 
                                            d.distribution_date DESC
                                        LIMIT $offset, $entries_per_page;";

                            $stmt = $conn->prepare($query);
                            if (!$stmt) {
                                die("Query preparation failed: " . $conn->error);
                            }
                            $stmt->bind_param("i", $station_id); // Only bind station_id
                            $stmt->execute();
                            $result = $stmt->get_result();

                            // Check for errors in the query
                            if (!$result) {
                                die("Query failed: " . $conn->error);
                            }
                            ?>
                            <div style="max-height: 430px; overflow: auto; width: 100%; border: 1px solid #ddd; border-radius: 10px;">
                                <table class="table table-bordered text-center" width="100%" cellspacing="0">
                                    <thead class="thead" style="background-color: #0D7C66; color: white;">
                                        <tr>
                                            <th>Date</th>
                                            <th>Beneficiary Name</th>
                                            <th>Province</th>
                                            <th>Municipality</th>
                                            <th>Barangay</th>
                                            <th>Cooperative</th>
                                            <th>Intervention Name</th>
                                            <th>Classification</th>
                                            <th>Quantity</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dataTable3" style="color: black;">
                                        <?php
                                        // Loop through and display the records
                                            if ($result->num_rows > 0) {
                                                while ($data = $result->fetch_assoc()) {
                                                    // Fetch the values for each row
                                                    $beneficiary_name = $data['beneficiary_name'];
                                                    $province = $data['province_name'];
                                                    $municipality = $data['municipality_name'];
                                                    $barangay = $data['barangay_name'];
                                                    $cooperative_name = $data['cooperative_name']; // Fetch the cooperative name
                                                    $intervention_name = $data['intervention_name'];
                                                    $seed_name = $data['seed_name'];
                                                    $quantity = $data['quantity'];
                                                    $date = date("F j, Y", strtotime($data['distribution_date']));
                                            ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($date); ?></td> <!-- Display the formatted date -->
                                                    <td><?php echo htmlspecialchars($beneficiary_name); ?></td>
                                                    <td><?php echo htmlspecialchars($province); ?></td>
                                                    <td><?php echo htmlspecialchars($municipality); ?></td>
                                                    <td><?php echo htmlspecialchars($barangay); ?></td>
                                                    <td><?php echo htmlspecialchars($cooperative_name); ?></td> <!-- Display cooperative name or N/A -->
                                                    <td><?php echo htmlspecialchars($intervention_name); ?></td>
                                                    <td><?php echo htmlspecialchars($seed_name); ?></td>
                                                    <td><?php echo htmlspecialchars($quantity); ?></td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-success rounded-0" data-bs-toggle="modal" data-bs-target="#updateDistributionModal"
                                                            data-distribution-id="<?php echo htmlspecialchars($data['distribution_id']); ?>"
                                                            data-quantity="<?php echo htmlspecialchars($data['quantity'] ?? ''); ?>"
                                                            data-intervention-name="<?php echo htmlspecialchars($intervention_name ?? ''); ?>"
                                                            data-seedling-name="<?php echo htmlspecialchars($seed_name ?? ''); ?>"
                                                            data-distribution-date="<?php echo htmlspecialchars($data['distribution_date'] ?? ''); ?>"
                                                            data-quantity-left="<?php echo isset($data['quantity_left']) ? htmlspecialchars($data['quantity_left']) : '0'; ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>

                                                        <button class="btn btn-danger rounded-0 archivedistribution-btn"
                                                            data-distribution-id="<?php echo htmlspecialchars($data['distribution_id']); ?>">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='10' class='text-center'>No records found</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <nav>
                    <ul class="pagination justify-content-center mt-3">
                        <?php
                        $total_pages = ceil($total_entries / $entries_per_page);

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
</div>

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

    .custom-search-form {
        margin-left: 500px;
        /* Adjust as needed */
    }
</style>
<!-- /.container-fluid -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function printReport() {
        let startDate = document.getElementById("start_date").value;
        let endDate = document.getElementById("end_date").value;
        
        if (!startDate || !endDate) {
            Swal.fire({
                icon: "warning",
                title: "Incomplete Input",
                text: "Please select both start and end dates.",
            });
            return;
        }
        
        let url = `9report/print_distribution_report.php?start_date=${startDate}&end_date=${endDate}`;
        window.open(url, '_blank');
    }
</script>
<script>
    // Set default date to today
    document.addEventListener("DOMContentLoaded", function () {
        let today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
        document.getElementById("end_date").value = today;
    });
</script>
<?php
    include('includes/scripts.php');
    include('includes/footer.php');
?>
<?php include ('modals/modal_for_distribution.php'); ?>
