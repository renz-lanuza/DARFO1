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
                USER MANAGEMENT
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

    <!-- Page Heading -->
    <!-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="mb-0 text-gray-800 fw-bolder">User Management</h1>
    </div>  -->

    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background-color: #0D7C66;">
            <div class="d-flex align-items-center">
                <!-- Add User Button -->
                <button type="button" class="btn d-flex align-items-center gap-2 rounded-pill shadow-sm" 
                        style="background-color: #DCFFB7; color: black; border: none; padding: 8px 16px;"
                        id="btnAddUser" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class='bx bxs-user-plus' style="font-size: 1.2rem;"></i>
                    <span>Add User</span>
                </button>

                 <!-- Search Form -->
                <form class="d-none d-sm-inline-block form-inline ml-auto my-2 my-md-0 mw-100 navbar-search custom-search-form">
                    <div class="input-group">
                        <input type="text" id="search_id" class="form-control bg-light border-0 small"
                            placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button id="searchBtn" class="btn text-white" style="background-color: #DCFFB7;" type="button">
                                <i class="fas fa-search fa-sm" style="color: black;"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            </div>
            <div id = "tab1" class="tab-content active">
                <div class="card-body">
                    <div style="max-height: 430px; overflow: auto; width: 100%; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead class="thead" style="background-color: #0D7C66; color: white;">
                                <tr>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>User Level</th>
                                    <th>Station</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                <?php
                                // Database connection
                                include('../conn.php');

                                // Define the number of entries per page
                                $entries_per_page = 10; // Change this value as needed

                                // Get the current page number from the URL, default to 1
                                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                                $page = max(1, $page); // Ensure the page is at least 1

                                // Calculate the starting row for the SQL query
                                $start_from = ($page - 1) * $entries_per_page;

                                // Count the total number of entries
                                $count_query = "SELECT COUNT(*) as total FROM tbl_user";
                                $count_result = mysqli_query($conn, $count_query);
                                $count_row = mysqli_fetch_assoc($count_result);
                                $total_entries = (int)$count_row['total']; // Ensure it's an integer

                                // Calculate the total number of pages
                                $total_pages = ceil($total_entries / $entries_per_page);


                                // Get the search query from the AJAX request
                                $search_query = isset($_GET['search']) ? $_GET['search'] : '';

                                // Build the SQL query depending on whether there's a search query or not
                                if (!empty($search_query)) {
                                    // If search query is provided, search by username, first name, last name, etc.
                                    $query = "SELECT * FROM tbl_user 
                                            WHERE username LIKE '%$search_query%' 
                                            OR fname LIKE '%$search_query%' 
                                            OR mname LIKE '%$search_query%' 
                                            OR lname LIKE '%$search_query%' 
                                            OR ulevel LIKE '%$search_query%' 
                                            ORDER BY ulevel";
                                } else {
                                    // If no search query, return all users
                                    $query = "SELECT * FROM tbl_user INNER JOIN tbl_station ON tbl_user.station_id = tbl_station.station_id ORDER BY ulevel LIMIT $start_from, $entries_per_page";
                                }

                                // Execute the query
                                $view = mysqli_query($conn, $query);

                            // Start rendering the table rows
                            ?>
                            </thead>
                            <?php
                            // Database connection
                            include('../conn.php');

                            // Get the search query from the AJAX request
                            $search_query = isset($_GET['search']) ? $_GET['search'] : '';

                            // Build the SQL query depending on whether there's a search query or not
                            if (!empty($search_query)) {
                                // If search query is provided, search by username, first name, last name, etc.
                                $query = "SELECT * FROM tbl_user 
                                        WHERE username LIKE '%$search_query%' 
                                        OR fname LIKE '%$search_query%' 
                                        OR mname LIKE '%$search_query%' 
                                        OR lname LIKE '%$search_query%' 
                                        OR ulevel LIKE '%$search_query%' 
                                        ORDER BY ulevel";
                            } else {
                                // If no search query, return all users
                                $query = "SELECT * FROM tbl_user INNER JOIN tbl_station ON tbl_user.station_id = tbl_station.station_id WHERE status != 3  ORDER BY ulevel LIMIT $start_from, $entries_per_page";
                            }

                            // Execute the query
                            $view = mysqli_query($conn, $query);

                            // Start rendering the table rows
                            ?>
                            <tbody id="dataTable" style="color: black;">
                                <?php
                                // Loop through and display user records
                                if (mysqli_num_rows($view) > 0) {
                                    while ($data = mysqli_fetch_assoc($view)) {
                                        $uid = $data['uid'];
                                        $uname = $data['username'];
                                        $fname = $data['fname'];
                                        $mname = $data['mname'];
                                        $lname = $data['lname'];
                                        $userlvl = $data['ulevel'];
                                        $station = $data['station_name'];
                                        $status = $data['status'];
                                ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($uname); ?></td>
                                            <td><?php echo htmlspecialchars($lname . ', ' . $fname . ' ' . $mname); ?></td>
                                            <td><?php echo htmlspecialchars($userlvl); ?></td>
                                            <td><?php echo htmlspecialchars($station); ?></td>
                                            <td>
                                                <?php if ($status == 1) { ?>
                                                    <a onclick="toggleStatus('<?php echo $uname; ?>', 'deactivate')" class="btn btn-secondary">Inactive</a>
                                                <?php } else { ?>
                                                    <a onclick="toggleStatus('<?php echo $uname; ?>', 'activate')" class="btn btn-success">Active</a>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <a href="#" class="btn" style="background-color: #DCFFB7; color: black;"
                                                    data-toggle="modal" data-target="#updateUserModal"
                                                    data-user-id="<?php echo htmlspecialchars($uid, ENT_QUOTES, 'UTF-8'); ?>">
                                                    Update
                                                </a>    

                                                <button class="btn btn-danger" onclick="confirmArchive('<?php echo $uid; ?>')">Delete</button>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No records found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
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
    <!-- /.container-fluid -->
</div>
<!-- End of Main Content -->
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

<?php include 'modals/modal_for_user.php'; ?>
