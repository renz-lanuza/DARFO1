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
                UNIT MANAGEMENT
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
                            id="btnAddUnit" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                            <i class='bx bx-plus' style="font-size: 1.2rem;"></i>
                            <span>Add Unit</span>
                        </button>

                       <form class="d-none d-sm-inline-block form-inline ml-auto my-2 my-md-0 mw-100 navbar-search custom-search-form">
                            <div class="input-group">
                                <input type="text" id="search_id" class="form-control bg-light border-0 small"
                                    placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
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
                            <!-- Table to display units -->
                            <table class="table table-bordered text-center" width="100%" cellspacing="0">
                                <thead class="thead" style="background-color: #0D7C66; color: white;">
                                    <tr>
                                        <th>Unit Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="dataTable6" style="color: black;">
                                    <?php
                                    include('../conn.php'); // Include database connection

                                    // Check if user is logged in
                                    if (!isset($_SESSION['uid'])) {
                                        echo "<tr><td colspan='2' class='text-center'>User not logged in.</td></tr>";
                                        exit;
                                    }

                                    $user_id = $_SESSION['uid'];

                                    // Retrieve the station_id based on the logged-in user's uid
                                    $stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
                                    $stationQuery->bind_param("i", $user_id);
                                    $stationQuery->execute();
                                    $stationQuery->bind_result($station_id);
                                    $stationQuery->fetch();
                                    $stationQuery->close();

                                    // Check if station_id was found
                                    if (empty($station_id)) {
                                        echo "<tr><td colspan='2' class='text-center'>No station found for the user.</td></tr>";
                                        exit;
                                    }

                                    // Pagination settings
                                    $entries_per_page = 10;
                                    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                                    $offset = ($page - 1) * $entries_per_page;

                                    // Count total records
                                    $countSQL = "SELECT COUNT(*) FROM tbl_unit WHERE station_id = ?";
                                    $countQuery = $conn->prepare($countSQL);
                                    if (!$countQuery) {
                                        die("Query failed: " . $conn->error);
                                    }
                                    $countQuery->bind_param("i", $station_id);
                                    $countQuery->execute();
                                    $countQuery->bind_result($total_records);
                                    $countQuery->fetch();
                                    $countQuery->close();

                                    // Fetch paginated records
                                    $query = "SELECT unit_id, unit_name FROM tbl_unit WHERE station_id = ? ORDER BY unit_id DESC LIMIT ?, ?";
                                    $stmt = $conn->prepare($query);

                                    if (!$stmt) {
                                        die("Query failed: " . $conn->error); // Debugging output
                                    }

                                    $stmt->bind_param("iii", $station_id, $offset, $entries_per_page);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    // Display fetched records
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['unit_name']); ?></td>
                                                <td>
                                                <a href="#" class="btn update-btn" style="background-color: #DCFFB7; color: black;"
                                                    data-toggle="modal" data-target="#updateUnitModal"
                                                    data-unit-id="<?php echo htmlspecialchars($row['unit_id']); ?>"
                                                    data-unit-name="<?php echo htmlspecialchars($row['unit_name']); ?>">Update</a>

                                                    <a href="#" class="btn btn-warning archive-unit-btn"
                                                        data-unit-id="<?php echo htmlspecialchars($row['unit_id']); ?>">
                                                        Archive
                                                    </a>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='2' class='text-center'>No records found</td></tr>";
                                    }

                                    $stmt->close();
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
    <?php include 'modals/modal_for_unit.php'; ?>

  <script>
        document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search_id");

    searchInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            fetchUnitData(searchInput.value);
        }
    });

    function fetchUnitData(searchValue) {
        fetch(`7unitManagement/searchUnit.php?search=${encodeURIComponent(searchValue)}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById("dataTable6").innerHTML = data;
                searchInput.focus(); // Refocus after search
            })
            .catch(error => console.error("Error fetching unit data:", error));
    }

    window.searchUnitTable = function () {
        fetchUnitData(searchInput.value);
    };
});

    </script>
