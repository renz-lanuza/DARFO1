<?php
    session_start();
    header('Content-Type: application/json');

    // Debugging: Check if session contains station_id
    if (!isset($_SESSION['station_id'])) {
        echo json_encode(["error" => "No station ID found in session.", "session_data" => $_SESSION]);
        exit;
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_darfo1";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
    }

    $station_id = $_SESSION['station_id'];
    $start_date = isset($_GET['start_date']) && !empty($_GET['start_date']) ? $_GET['start_date'] : null;
    $end_date = isset($_GET['end_date']) && !empty($_GET['end_date']) ? $_GET['end_date'] : null;

    // ✅ Query: Retrieve intervention names and total distributed quantities based on date range
    if ($start_date && $end_date) {
        $sql = "SELECT 
                    it.intervention_name, 
                    COALESCE(SUM(d.quantity), 0) AS total_quantity
                FROM tbl_distribution d  
                JOIN tbl_seed_type st ON d.seed_id = st.seed_id
                JOIN tbl_intervention_type it ON st.int_type_id = it.int_type_id
                WHERE d.station_id = ? 
                AND DATE(d.distribution_date) BETWEEN ? AND ?  
                GROUP BY it.intervention_name
                ORDER BY it.intervention_name ASC;";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $station_id, $start_date, $end_date);
    } elseif ($start_date) { 
        // If only start date is provided
        $sql = "SELECT 
                    it.intervention_name, 
                    COALESCE(SUM(d.quantity), 0) AS total_quantity
                FROM tbl_distribution d  
                JOIN tbl_seed_type st ON d.seed_id = st.seed_id
                JOIN tbl_intervention_type it ON st.int_type_id = it.int_type_id
                WHERE d.station_id = ? 
                AND DATE(d.distribution_date) >= ?  
                GROUP BY it.intervention_name
                ORDER BY it.intervention_name ASC;";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $station_id, $start_date);
    } elseif ($end_date) { 
        // If only end date is provided
        $sql = "SELECT 
                    it.intervention_name, 
                    COALESCE(SUM(d.quantity), 0) AS total_quantity
                FROM tbl_distribution d  
                JOIN tbl_seed_type st ON d.seed_id = st.seed_id
                JOIN tbl_intervention_type it ON st.int_type_id = it.int_type_id
                WHERE d.station_id = ? 
                AND DATE(d.distribution_date) <= ?  
                GROUP BY it.intervention_name
                ORDER BY it.intervention_name ASC;";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $station_id, $end_date);
    } else {
        // If no date filters are provided, return all records
        $sql = "SELECT 
                    it.intervention_name, 
                    COALESCE(SUM(d.quantity), 0) AS total_quantity
                FROM tbl_distribution d  
                JOIN tbl_seed_type st ON d.seed_id = st.seed_id
                JOIN tbl_intervention_type it ON st.int_type_id = it.int_type_id
                WHERE d.station_id = ?  
                GROUP BY it.intervention_name
                ORDER BY it.intervention_name ASC;";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $station_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode($data);
?>
