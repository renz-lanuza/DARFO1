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
                CLASSIFICATION MANAGEMENT
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
                <h1 class=" mb-0 text-gray-800 fw-bolder">Seed Type Management</h1>
            </div> -->
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="background-color: #0D7C66;">

                    <div class="d-flex align-items-center">
                        <button type="button" class="btn d-flex align-items-center gap-2 rounded-pill shadow-sm" 
                                style="background-color: #DCFFB7; color: black; border: none; padding: 8px 16px;"
                                id="btnAddSeedType" data-bs-toggle="modal" data-bs-target="#addSeedTypeModal">
                            <i class='bx bx-plus' style="font-size: 1.2rem;"></i>
                            <span>Add Classification</span>
                        </button>


                        <form class="d-none d-sm-inline-block form-inline ml-auto my-2 my-md-0 mw-100 navbar-search custom-search-form">
                            <div class="input-group">
                                <input type="text" id="search_id" class="form-control bg-light border-0 small"
                                    placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" onkeyup="searchClassificationTable()">
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
                        <div style="max-height: 430px; overflow: auto; width: 100%; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);">
                        <table id="dataTable5" class="table table-bordered text-center" width="100%" cellspacing="0">
                    <thead class="thead" style="background-color: #0D7C66; color: white;">
                        <tr>
                            <th>Intervention Name</th>  
                            <th>Classification</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="seedTableBody" style="color: black;">
                        <?php
                        include('../conn.php');
                        $uid = $_SESSION['uid'];

                        // Fetch station_id for the logged-in user
                        $sqlStation = "SELECT station_id FROM tbl_user WHERE uid = ?";
                        $stmtStation = $conn->prepare($sqlStation);
                        $stmtStation->bind_param("i", $uid);
                        $stmtStation->execute();
                        $resultStation = $stmtStation->get_result();

                        if ($resultStation->num_rows > 0) {
                            $rowStation = $resultStation->fetch_assoc();
                            $stationId = $rowStation['station_id'];

                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $search = isset($_GET['search']) ? "%" . trim($_GET['search']) . "%" : "%";
                            $entries_per_page = 10;
                            $offset = ($page - 1) * $entries_per_page;

                            // Get total count
                            $sql_count = "SELECT COUNT(*) AS total FROM tbl_seed_type st
                                        INNER JOIN tbl_intervention_type it ON st.int_type_id = it.int_type_id
                                        WHERE st.station_id = ? AND (st.seed_name LIKE ? OR it.intervention_name LIKE ?)";

                            $stmt_count = $conn->prepare($sql_count);
                            $stmt_count->bind_param("iss", $stationId, $search, $search);
                            $stmt_count->execute();
                            $result_count = $stmt_count->get_result();
                            $total_entries = ($row_count = $result_count->fetch_assoc()) ? (int) $row_count['total'] : 0;
                            $stmt_count->close();
                            
                            $total_pages = ($total_entries > 0) ? ceil($total_entries / $entries_per_page) : 1;

                            // Fetch paginated data
                            $sql ="SELECT 
                                        st.seed_name, 
                                        st.seed_id, 
                                        it.intervention_name
                                    FROM 
                                        tbl_seed_type st
                                    INNER JOIN 
                                        tbl_intervention_type it 
                                        ON st.int_type_id = it.int_type_id
                                    WHERE 
                                        st.station_id = ? 
                                        AND (st.seed_name LIKE ? OR it.intervention_name LIKE ?)
                                        AND st.archived_at IS NULL
                                    ORDER BY 
                                        st.seed_id DESC
                                    LIMIT ? OFFSET ?";
                            
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("issii", $stationId, $search, $search, $entries_per_page, $offset);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['intervention_name']) ?></td>
                                        <td><?= htmlspecialchars($row['seed_name']) ?></td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-success btn-sm edit-btn rounded-0" 
                                                    data-id="<?= htmlspecialchars($row['seed_id']) ?>"
                                                    data-seed-name="<?= htmlspecialchars($row['seed_name']) ?>"
                                                    data-intervention-name="<?= htmlspecialchars($row['intervention_name']) ?>"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editSeedlingModal">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <!-- Archive Button -->
                                                <button class="btn btn-danger btn-sm archive-btn rounded-0" 
                                                    data-id="<?= htmlspecialchars($row['seed_id']) ?>">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php }
                            } else {
                                echo "<tr><td colspan='3' class='text-center'>No matching seed types found.</td></tr>";
                            }

                            $stmt->close();
                        } else {
                            echo "<tr><td colspan='3' class='text-center'>No station found for this user.</td></tr>";
                        }

                        $stmtStation->close();
                        $conn->close();
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
    </div>

    <!-- /.container-fluid -->
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
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
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
    <?php include 'modals/modal_for_seed_type.php'; ?>
