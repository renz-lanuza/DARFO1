<?php
include('includes/header.php');
include('includes/navbar.php');
?>
<link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
<!-- Include Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Include Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
<!-- Include Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx-populate/1.21.0/xlsx-populate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx-populate/1.21.0/xlsx-populate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.3.0/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>


<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">
    <!-- Main Content -->
    <div id="content">
        <!-- Topbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white topbar mb-4 static-top shadow">
            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>
            <span class="navbar-brand font-weight-bold" style="font-size: 18px; padding-left: 55px; color: Black; font-family: 'Roboto', sans-serif;">
                DASHBOARD
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
        <div id="scrollable-container" style="max-height: 85vh; overflow-y: auto; padding-right: 10px;">
            <!-- Begin Page Content -->
            <div class="container-fluid">
                <div class="row">
                    <!-- Dashboard Cards (Left Side - 4 Rows) -->
                    <div class="col-xl-6">
                        <div class="row">
                        <?php
                            $servername = "localhost";
                            $username = "root";
                            $password = "";
                            $dbname = "db_darfo1";

                            // Ensure the user is logged in
                            if (!isset($_SESSION['uid'])) {
                                die("User not logged in.");
                            }

                            $user_id = $_SESSION['uid'];

                            // Create connection
                            $conn = new mysqli($servername, $username, $password, $dbname);

                            // Check connection
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }

                            // Get the station ID of the logged-in user
                            $stationQuery = $conn->prepare("SELECT station_id FROM tbl_user WHERE uid = ?");
                            $stationQuery->bind_param("i", $user_id);
                            $stationQuery->execute();
                            $stationQuery->store_result();

                            if ($stationQuery->num_rows > 0) {
                                $stationQuery->bind_result($station_id);
                                $stationQuery->fetch();
                            } else {
                                die("No station assigned to this user.");
                            }
                            $stationQuery->close();

                            // Fetching the data with station filter
                            $queries = [
                                "interventions" => "SELECT COUNT(*) AS total_interventions FROM tbl_distribution WHERE station_id = ?;",
                                "individual_count" => "SELECT COUNT(*) AS individual_count FROM tbl_beneficiary WHERE coop_id = 0 AND station_id = ?;",
                                "group_count" => "SELECT COUNT(DISTINCT coop_id) AS group_count FROM tbl_beneficiary WHERE coop_id > 0 AND station_id = ?;"
                            ];

                            $results = [];

                            foreach ($queries as $key => $query) {
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param("i", $station_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                
                                if ($result === false) {
                                    die("Query failed: " . $conn->error);
                                }

                                $row = $result->fetch_assoc();
                                $results[$key] = $row[array_keys($row)[0]] ?? 0;
                                $stmt->close();
                            }

                            // Fetch beneficiaries data (Filtered by station)
                            $beneficiaries_query = "SELECT beneficiary_id, fname, mname, lname, barangay_name, municipality_name, province_name FROM tbl_beneficiary WHERE station_id = ?";
                            $stmt = $conn->prepare($beneficiaries_query);
                            $stmt->bind_param("i", $station_id);
                            $stmt->execute();
                            $beneficiaries_result = $stmt->get_result();

                            $beneficiaries_data = [];
                            if ($beneficiaries_result->num_rows > 0) {
                                while ($row = $beneficiaries_result->fetch_assoc()) {
                                    $beneficiaries_data[] = $row;
                                }
                            }
                            $stmt->close();
                            $conn->close();

                            // Dynamic card data
                            $cardData = [
                                ["title" => "Total Interventions", "value" => $results["interventions"], "icon" => "fa-box-open", "color" => "primary", "link" => "3DistributionManagement.php"],
                                ["title" => "Individual Beneficiaries", "value" => number_format($results["individual_count"]), "icon" => "fa-user", "color" => "info", "link" => "individual_beneficiaries.php"],
                                ["title" => "Group Beneficiaries", "value" => number_format($results["group_count"]), "icon" => "fa-users", "color" => "warning", "link" => "group_beneficiaries.php"]
                            ];
                            ?>

                            <!-- First Row: 3 Cards in a Single Line -->
                            <div class="row d-flex flex-wrap justify-content-center">
                                <?php foreach (array_slice($cardData, 0, 3) as $card) { ?>
                                    <div class="col-lg-4 col-md-1 mb-4">
                                        <div class="card border-left-<?= htmlspecialchars($card['color']); ?> shadow h-100 py-1">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col-auto">
                                                        <!-- Icon as a clickable button -->
                                                        <a href="<?= htmlspecialchars($card['link']); ?>" class="btn btn-<?= htmlspecialchars($card['color']); ?> btn-circle">
                                                            <i class="fas <?= htmlspecialchars($card['icon']); ?> text-white"></i>
                                                        </a>
                                                    </div>
                                                    <div class="col ml-2">
                                                        <div class="text-xs font-weight-bold text-<?= htmlspecialchars($card['color']); ?> text-uppercase mb-1 text-center">
                                                            <?= htmlspecialchars($card['title']); ?>
                                                        </div>
                                                        <div class="mt-2 font-weight-bold text-gray-800 text-center" style="font-size: 26px;">
                                                            <?= htmlspecialchars($card['value']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- Second Row: Bigger Card (Full Width & Taller) -->
                            <div class="col-xl-12 mb-4">
                                <div class="card border-left-primary shadow h-100 py-1">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Intervention Distributed
                                                </div>
                                                <!-- Date Filter -->
                                                <div class="d-flex justify-content-start align-items-center mb-3">
                                                    <label class="mr-2">Start Date:</label>
                                                    <input type="date" id="startDate" class="form-control form-control-sm" style="width: 150px;">
                                                    
                                                    <label class="ml-3 mr-2">End Date:</label>
                                                    <input type="date" id="endDate" class="form-control form-control-sm" style="width: 150px;">
                                                    
                                                    <!-- <button class="btn btn-primary btn-sm ml-2" onclick="filterChart()">Apply</button> -->
                                                    <button class="btn btn-success btn-sm ml-4" onclick="downloadExcelWithImage()" data-toggle="tooltip" data-placement="top" title="Download Excel">
                                                        <i class="fas fa-file-excel"></i>
                                                    </button>
                                                </div>
                                                <div style="width: 100%; max-width: 500px; height: 347px; margin: auto;">
                                                    <canvas id="barChart"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Larger Map Card (Right Side - Half Page) -->
                    <div class="col-xl-6">
                        <div class="card border-left-warning shadow py-4">
                            <div class="card-body">
                                <div class="text-xl font-weight-bold text-warning text-uppercase mb-3 mt-0 ">
                                    Region 1 (Ilocos Region)
                                </div>
                                <div id="map" style="width: 100%; height: 490px; border-radius: 10px;"></div>
                            </div>
                        </div>
                    </div>
                
                    <div class="col-xl-12 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <!-- Header with Title and Filter Dropdown -->
                                        <div class="d-flex justify-content-between align-items-center mb-3">                                            
                                            <div class="text-lg font-weight-bold text-info text-uppercase">
                                                Beneficiaries
                                            </div>    
                                        </div>
                                        <!-- Filter Buttons Above Table -->
                                        <div class="d-flex justify-content-start mb-3">
                                            <button class="btn btn-outline-primary px-4 py-2 me-2 filter-btn active" data-filter="all">All</button>
                                            <button class="btn btn-outline-primary px-4 py-2 me-2 filter-btn" data-filter="Individual">Individual</button>
                                            <button class="btn btn-outline-success px-4 py-2 filter-btn" data-filter="Group">Group</button>
                                        </div>

                                        <!-- Table -->
                                        <div class="table-responsive mt-3" style="max-height: 400px; overflow-y: auto;">
                                            <?php
                                                include("../conn.php");
                                                $station_id = intval($_SESSION['station_id']); // Get the logged-in user's station_id

                                                // Prepare the SQL query
                                                $query = "SELECT b.beneficiary_id,
                                                                    b.fname,
                                                                    b.mname,
                                                                    b.lname,
                                                                    b.province_name,
                                                                    b.municipality_name,
                                                                    b.barangay_name,
                                                                    b.beneficiary_category,
                                                                    COALESCE(c.cooperative_name, 'N/A') AS cooperative_name  -- ✅ Ensure cooperative_name is never NULL
                                                                FROM 
                                                                    tbl_beneficiary b
                                                                LEFT JOIN 
                                                                    tbl_distribution d ON b.beneficiary_id = d.beneficiary_id
                                                                LEFT JOIN 
                                                                    tbl_cooperative c ON b.coop_id = c.coop_id  -- ✅ Ensure coop_id properly maps
                                                                WHERE 
                                                                    b.station_id = ?  -- ✅ Filter based on logged-in user's station
                                                                GROUP BY 
                                                                    b.beneficiary_id
                                                                ORDER BY
                                                                    b.beneficiary_id DESC";

                                                // ✅ Use prepared statements
                                                $stmt = mysqli_prepare($conn, $query);
                                                if (!$stmt) {
                                                    die("SQL Error: " . mysqli_error($conn)); // Debugging error message
                                                }

                                                // ✅ Bind parameter (station_id as integer)
                                                mysqli_stmt_bind_param($stmt, "i", $station_id);

                                                // ✅ Execute the statement
                                                mysqli_stmt_execute($stmt);

                                                // ✅ Get the result
                                                $result = mysqli_stmt_get_result($stmt);

                                                    // ✅ Fetch the data
                                                $beneficiaries_data = mysqli_fetch_all($result, MYSQLI_ASSOC);

                                                // ✅ Close the statement and connection
                                                mysqli_stmt_close($stmt);
                                                mysqli_close($conn);
                                            ?>
                                           <table class="table table-bordered table-striped table-hover text-center" style="width: 100%; border-radius: 10px; overflow: hidden;">
                                                <thead style="background-color: #0D7C66; color: white;">
                                                    <tr>
                                                        <th>#</th>
                                                        <th id="nameHeader">Name</th>
                                                        <th>Barangay</th>
                                                        <th>Municipality</th>
                                                        <th>Province</th>
                                                        <th>Type of Beneficiary</th>
                                                        <th>Cooperative Name</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($beneficiaries_data)): ?>
                                                        <?php foreach ($beneficiaries_data as $index => $beneficiary): ?>
                                                            <tr data-type="<?= htmlspecialchars($beneficiary['beneficiary_category']) ?>">
                                                                <td><?= $index + 1 ?></td>
                                                                <td><?= htmlspecialchars($beneficiary['fname'] . ' ' . $beneficiary['mname'] . ' ' . $beneficiary['lname']) ?></td>
                                                                <td><?= htmlspecialchars($beneficiary['barangay_name']) ?></td>
                                                                <td><?= htmlspecialchars($beneficiary['municipality_name']) ?></td>
                                                                <td><?= htmlspecialchars($beneficiary['province_name']) ?></td>
                                                                <td><?= ucfirst(htmlspecialchars($beneficiary['beneficiary_category'])) ?></td>
                                                                <td><?= htmlspecialchars($beneficiary['cooperative_name']) ?></td>
                                                                <td>
                                                                    <button class="btn btn-primary btn-sm view-interventions-btn" data-beneficiary-id="<?= $beneficiary['beneficiary_id'] ?>">
                                                                        <i class="fas fa-eye"></i> View
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="8" class="text-center">No beneficiaries found.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- JavaScript for Filtering and Dynamic Header Change -->
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script>
                        $(document).ready(function() {
                            $('#filterType').on('change', function() {
                                const selectedType = $(this).val(); // Get selected filter value

                                // Change the Name header if the type is organization-based
                                const nameHeader = $('#nameHeader');
                                const groupCategories = ["FCA", "Cluster", "LGU", "School", "Others"]; // Define group categories
                                
                                if (groupCategories.includes(selectedType)) {
                                    nameHeader.text('Representative Name'); // Change header for groups
                                } else {
                                    nameHeader.text('Name'); // Default header for individuals
                                }

                                // Filter the table rows
                                $('table tbody tr').each(function() {
                                    const rowType = $(this).data('type'); // Get row's type of beneficiary
                                    if (selectedType === 'all' || rowType === selectedType) {
                                        $(this).show(); // Show matching rows
                                    } else {
                                        $(this).hide(); // Hide non-matching rows
                                    }
                                });
                            });
                        });
                    </script>
                </div>
            </div>            
        </div>
    </div>
</div>
<!-- Include Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    async function downloadExcelWithImage() {
    if (!window.myChart) {
        alert("No chart data available.");
        return;
    }

    let labels = window.myChart.data.labels;
    let datasets = window.myChart.data.datasets;

    let dataArray = [["Category", "Value"]]; // Header row
    datasets.forEach(dataset => {
        labels.forEach((label, index) => {
            dataArray.push([label, dataset.data[index]]);
        });
    });

    // Create a new Excel workbook
    let workbook = new ExcelJS.Workbook();
    let worksheet = workbook.addWorksheet("Chart Data");

    // Insert data into the worksheet
    worksheet.addRows(dataArray);

    // Capture chart as Base64 image
    let canvas = document.getElementById("barChart");
    let imgData = canvas.toDataURL("image/png").split(",")[1]; // Remove metadata

    // Add the image to the workbook
    let imageId = workbook.addImage({
        base64: imgData,
        extension: "png",
    });

    // Position the image below the data
    worksheet.addImage(imageId, {
        tl: { col: 0, row: dataArray.length + 2 }, // Adjust position
        ext: { width: 500, height: 300 }, // Resize image
    });

    // Correctly generate and download the Excel file
    try {
        const buffer = await workbook.xlsx.writeBuffer();
        const blob = new Blob([buffer], { 
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" 
        });

        saveAs(blob, "chart_data.xlsx");
    } catch (error) {
        console.error("Error generating Excel file:", error);
        alert("An error occurred while generating the Excel file.");
    }
}   
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const startDateInput = document.getElementById("startDate");
        const endDateInput = document.getElementById("endDate");

        // Function to set the default end date (today)
        function setDefaultEndDate() {
            const today = new Date().toISOString().split("T")[0]; // Format YYYY-MM-DD
            endDateInput.value = today;
        }

        function fetchData(startDate, endDate) {
            let url = `Dashboard/fetch_data.php`;
            let params = [];

            if (startDate) params.push(`start_date=${startDate}`);
            if (endDate) params.push(`end_date=${endDate}`);

            if (params.length) url += `?${params.join("&")}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    console.log("Fetched Data:", data);

                    const ctx = document.getElementById("barChart").getContext("2d");

                    if (window.myChart) {
                        window.myChart.destroy();
                    }

                    let labels, quantities, noDataMessage = false;

                    if (!data || data.length === 0) {
                        console.warn("No data available. Displaying 'No Data Available' message.");

                        // Placeholder to ensure chart remains visible
                        labels = ["No Data Available"];
                        quantities = [0]; // Zero quantity to keep the bar chart structure
                        noDataMessage = true;
                    } else {
                        labels = data.map(item => item.intervention_name);
                        quantities = data.map(item => item.total_quantity);
                    }

                    const colorPalette = [
                        "rgba(255, 99, 132, 0.7)",
                        "rgba(54, 162, 235, 0.7)",
                        "rgba(255, 206, 86, 0.7)",
                        "rgba(75, 192, 192, 0.7)",
                        "rgba(153, 102, 255, 0.7)",
                        "rgba(255, 159, 64, 0.7)"
                    ];
                    const backgroundColors = labels.map((_, index) => colorPalette[index % colorPalette.length]);
                    const borderColors = backgroundColors.map(color => color.replace("0.7", "1"));

                    // Compute Y-axis max dynamically, ensuring at least 10 for visibility
                    const maxValue = Math.max(...quantities, 10);
                    const adjustedMax = Math.ceil((maxValue + 10) / 10) * 10;

                    window.myChart = new Chart(ctx, {
                        type: "bar",
                        data: {
                            labels: labels,
                            datasets: [{
                                label: "Quantity Distributed",
                                data: quantities,
                                backgroundColor: backgroundColors,
                                borderColor: borderColors,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    min: 0,
                                    max: adjustedMax,
                                    ticks: {
                                        stepSize: 10
                                    }
                                }
                            },
                            plugins: {
                                legend: { display: true },
                                annotation: {
                                    annotations: noDataMessage ? [{
                                        type: 'label',
                                        content: 'No Data Available',
                                        position: 'center',
                                        font: { size: 14, weight: 'bold' },
                                        color: 'red'
                                    }] : []
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error("Error fetching data:", error));
        }


        // Automatically set today's date for end date on page load
        setDefaultEndDate();

        // Fetch data when the start date is selected
        startDateInput.addEventListener("change", () => {
            fetchData(startDateInput.value, endDateInput.value);
        });

        // Load chart with default start date empty & today's end date on page load
        fetchData("", endDateInput.value);
    }); 
</script>
<script>
    function filterChart() {
        let startDate = document.getElementById('startDate').value;
        let endDate = document.getElementById('endDate').value;
        if (startDate && endDate) {
            alert(`Filtering chart from ${startDate} to ${endDate}`);
            // Add logic here to update the barChart based on selected date range
        } else {
            alert("Please select both start and end dates.");
        }
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", async function () {
        var map = L.map('map').setView([16.616, 120.316], 8);

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        try {
            // Fetch intervention data
            let response = await fetch('map/get_intervention_data.php');
            let interventionData = await response.json();

            console.log("Intervention Data:", interventionData); // Debugging Output

            if (interventionData.error) {
                console.error("Error from PHP:", interventionData.error);
                return;
            }

            // Normalize intervention data keys (lowercase & trim spaces)
            let normalizedInterventionData = {};
            let maxValue = 0;
            let minValue = Infinity;

            Object.keys(interventionData).forEach(key => {
                let normalizedKey = key.trim().toLowerCase();
                let value = interventionData[key];

                normalizedInterventionData[normalizedKey] = value;

                if (value > maxValue) maxValue = value;
                if (value < minValue) minValue = value;
            });

            if (minValue === maxValue) {
                minValue = 0;
            }

            // Fetch GeoJSON data
            response = await fetch('map/map.geojson');
            let geoData = await response.json();

            // Green color scale
            function interpolateGreen(value) {
                if (value === 0) return "#d5d8dc"; 

                let intensity = (value - minValue) / (maxValue - minValue);
                if (isNaN(intensity)) intensity = 0;

                let darkGreen = [0, 100, 0];  
                let lightGreen = [173, 255, 47]; 

                let r = Math.round(lightGreen[0] + (darkGreen[0] - lightGreen[0]) * intensity);
                let g = Math.round(lightGreen[1] + (darkGreen[1] - lightGreen[1]) * intensity);
                let b = Math.round(lightGreen[2] + (darkGreen[2] - lightGreen[2]) * intensity);

                return `rgb(${r}, ${g}, ${b})`;
            }

            function showModal(municipalityName) {
                let modalTitle = document.getElementById("modalTitle");
                let modalBody = document.getElementById("modalBody");

                modalTitle.innerHTML = `Interventions in ${municipalityName}`;

                // Fetch intervention data from PHP script
                fetch(`map/get_data.php?municipality=${encodeURIComponent(municipalityName)}`)
                    .then(response => response.text()) // Get response as HTML
                    .then(html => {
                        modalBody.innerHTML = html; // Insert into modal
                        let modal = new bootstrap.Modal(document.getElementById("interventionModal"));
                        modal.show();
                    })
                    .catch(error => {
                        console.error("Error loading intervention data:", error);
                        modalBody.innerHTML = `<p class="text-danger">Error loading data.</p>`;
                    });
            }

            function onEachFeature(feature, layer) {
                let municipalityName = feature.properties.adm3_en 
                    ? feature.properties.adm3_en.trim().toLowerCase()
                    : "";

                let interventionCount = normalizedInterventionData[municipalityName] || 0;

                // layer.bindPopup(`
                //     <b>${feature.properties.adm3_en}</b><br>
                //     <b>Interventions:</b> <b>${interventionCount}</b>
                // `);

                layer.on({
                    mouseover: function (e) {
                        let layer = e.target;
                        layer.setStyle({
                            fillOpacity: 1,
                            color: "#FFFF00"
                        });

                        layer.bindTooltip(`
                            <b>${feature.properties.adm3_en}</b><br>
                            Interventions: ${interventionCount}
                        `, {
                            permanent: false,
                            direction: "top"
                        }).openTooltip();
                    },
                    mouseout: function (e) {
                        let layer = e.target;
                        layer.setStyle({
                            fillOpacity: 0.8,
                            color: "#000"
                        });

                        layer.closeTooltip();
                    },
                    click: function () {
                        showModal(feature.properties.adm3_en, interventionCount);
                    }
                });
            }

            L.geoJSON(geoData, {
                style: function (feature) {
                    let municipalityName = feature.properties.adm3_en 
                        ? feature.properties.adm3_en.trim().toLowerCase()
                        : "";

                    let interventionCount = normalizedInterventionData[municipalityName] || 0;

                    let fillColor = interpolateGreen(interventionCount);

                    return {
                        fillColor: fillColor,
                        color: "#000",
                        weight: 0.5,
                        fillOpacity: 0.8
                    };
                },
                onEachFeature: onEachFeature // Use the function properly
            }).addTo(map);

        } catch (error) {
            console.error('Error loading data:', error);
        }
    });
</script>

<style>
    /* Custom modal width */
    #viewInterventionsModal .modal-dialog {
        max-width: 90%;
        /* Adjust this value as needed */
        width: 90%;
        /* Adjust this value as needed */
    }
    thead tr {
    background-color: #0D7C66 !important;
    color: white !important;
}

</style>
<!-- Ensure Bootstrap JS and jQuery are loaded -->
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
    include('modals/modal_for_viewing_intervention.php');
    include('modals/modal_for_map.php');
?>
