<?php
include('includes/header.php');
include('includes/navbar.php');
?>
<link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
<!-- Include Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Include Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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
                                "interventions" => "SELECT SUM(quantity) AS total_quantity FROM tbl_distribution WHERE station_id = ?;",
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
                                ["title" => "Intervention Distributed", "value" => $results["interventions"], "icon" => "fa-box-open", "color" => "primary", "link" => "3DistributionManagement.php"],
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
                                                    Product Distributed
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
                                <div class="text-xl font-weight-bold text-warning text-uppercase mb-3 text-center">
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
                                            <!-- Filter Dropdown -->
                                            <div>
                                                <select id="filterType" class="form-control">
                                                    <option value="all">All</option>
                                                    <option value="Individual">Individual</option>
                                                    <option value="Group">Group</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Table -->
                                        <div class="table-responsive mt-3" style="max-height: 400px; overflow-y: auto;">
                                            <?php
                                                include("../conn.php");
                                                $station_id = intval($_SESSION['station_id']); // Get the logged-in user's station_id

                                                // Prepare the SQL query
                                                $query = "SELECT 
                                                                b.beneficiary_id,
                                                                b.fname,
                                                                b.mname,
                                                                b.lname,
                                                                b.province_name,
                                                                b.municipality_name,
                                                                b.barangay_name,
                                                                d.type_of_distribution,
                                                                IF(b.coop_id = 0, 'N/A', c.cooperative_name) AS cooperative_name
                                                            FROM 
                                                                tbl_beneficiary b
                                                            LEFT JOIN 
                                                                tbl_distribution d ON b.beneficiary_id = d.beneficiary_id
                                                            LEFT JOIN 
                                                                tbl_cooperative c ON b.coop_id = c.coop_id
                                                            WHERE 
                                                                b.station_id = ?  -- ✅ Filter based on logged-in user's station
                                                            GROUP BY 
                                                                b.beneficiary_id";

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
                                                        <th>Type of Distribution</th>
                                                        <th>Cooperative Name</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($beneficiaries_data)): ?>
                                                        <?php foreach ($beneficiaries_data as $index => $beneficiary): ?>
                                                            <tr data-type="<?= $beneficiary['type_of_distribution'] ?>">
                                                                <td><?= $index + 1 ?></td>
                                                                <td>
                                                                    <?= htmlspecialchars($beneficiary['fname'] . ' ' . $beneficiary['mname'] . ' ' . $beneficiary['lname']) ?>
                                                                </td>
                                                                <td><?= htmlspecialchars($beneficiary['barangay_name']) ?></td>
                                                                <td><?= htmlspecialchars($beneficiary['municipality_name']) ?></td>
                                                                <td><?= htmlspecialchars($beneficiary['province_name']) ?></td>
                                                                <td><?= ucfirst($beneficiary['type_of_distribution']) ?></td>
                                                                <td>
                                                                    <?= ($beneficiary['type_of_distribution'] === 'Group') ? htmlspecialchars($beneficiary['cooperative_name'] ?? 'N/A') : 'N/A' ?>
                                                                </td>
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
                                $('table tbody tr').each(function() {
                                    const rowType = $(this).data('type'); // Get the row's type of distribution
                                    if (selectedType === 'all' || rowType === selectedType) {
                                        $(this).show(); // Show the row if it matches the filter
                                    } else {
                                        $(this).hide(); // Hide the row if it doesn't match the filter
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
function downloadExcelWithImage() {
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

    // Convert data to a worksheet
    let ws = XLSX.utils.aoa_to_sheet(dataArray);

    // Capture chart as Base64 image
    let canvas = document.getElementById("barChart");
    let imgData = canvas.toDataURL("image/png");

    // Insert image placeholder text
    XLSX.utils.sheet_add_aoa(ws, [["Chart Image Below"]], { origin: { r: dataArray.length + 2, c: 0 } });

    // Convert Base64 image to a downloadable file
    fetch(imgData)
        .then(res => res.blob())
        .then(blob => blob.arrayBuffer())
        .then(buffer => {
            let wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Chart Data");

            // Prompt user to manually insert image
            alert("The Excel file contains data. Please manually insert the downloaded chart image into the sheet.");

            // Download Excel file
            XLSX.writeFile(wb, "chart_data.xlsx");

            // Automatically trigger image download
            let link = document.createElement("a");
            link.href = imgData;
            link.download = "chart_image.png";
            link.click();
        })
        .catch(err => console.error("Error processing image:", err));
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

                if (!data || data.length === 0) {
                    console.error("No data received.");
                    document.getElementById("barChart").style.display = "none";
                    return;
                }

                document.getElementById("barChart").style.display = "block";

                const labels = data.map(item => item.intervention_name);
                const quantities = data.map(item => item.total_quantity);

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

                const ctx = document.getElementById("barChart").getContext("2d");

                if (window.myChart) {
                    window.myChart.destroy();
                }

                // Compute Y-axis max (always starts at 0)
                const maxValue = Math.max(...quantities);
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
                            legend: { display: true }
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

    let interventionData = {}; // Store intervention counts

    try {
        // Fetch intervention data first
        let response = await fetch('map/get_intervention_data.php');
        interventionData = await response.json();
        
        // Fetch and process GeoJSON after intervention data is loaded
        response = await fetch('map/map.geojson');
        let geoData = await response.json();

        // Process and add GeoJSON to map
        L.geoJSON(geoData, {
            style: function (feature) {
                let provinceColors = {
                    "Ilocos Norte": "#B22222",
                    "Ilocos Sur": "#00008B",
                    "La Union": "#006400",
                    "Pangasinan": "#FF8C00"
                };
                return {
                    fillColor: provinceColors[feature.properties.adm2_en] || "#CCCCCC",
                    color: "#000",
                    weight: 0.5,
                    fillOpacity: 0.6
                };
            },
            onEachFeature: function (feature, layer) {
                let municipalityName = feature.properties.adm3_en;
                let interventionCount = interventionData[municipalityName] || 0;

                // Bind province popup on click (with intervention data)
                layer.bindPopup(`<b>${municipalityName}</b><br>Interventions Distributed: <b>${interventionCount}</b>`);

                // Show municipality name and intervention count tooltip on hover
                layer.on({
                    mouseover: function (e) {
                        let layer = e.target;
                        layer.setStyle({
                            fillOpacity: 0.9,
                            color: "#FFFF00"
                        });

                        // Show tooltip with municipality name and intervention data
                        layer.bindTooltip(`<b>${municipalityName}</b><br>Interventions: ${interventionCount}`, {
                            permanent: false,
                            direction: "top"
                        }).openTooltip();
                    },
                    mouseout: function (e) {
                        let layer = e.target;
                        layer.setStyle({
                            fillOpacity: 0.6,
                            color: "#000"
                        });

                        // Close the tooltip
                        layer.closeTooltip();
                    }
                });
            }
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
?>
