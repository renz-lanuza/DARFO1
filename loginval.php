<?php
    session_start();
    require_once 'conn.php';

    // Enable detailed error reporting
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $data = array();

    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare SQL query to check if the user exists
        $sql = "SELECT * FROM tbl_user WHERE username=?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $data['response'] = 'error';
            $data['message'] = 'SQL prepare error: ' . $conn->error;
            echo json_encode($data);
            exit();
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $fetch = $result->fetch_assoc();

            // Check if the user account is active
            if ($fetch['status'] == 0) {
                if (password_verify($password, $fetch['password'])) {
                    // Set session variables
                    $_SESSION['uid'] = $fetch['uid'];
                    $_SESSION['user'] = $fetch['username'];
                    $_SESSION['fname'] = $fetch['fname'];
                    $_SESSION['mname'] = $fetch['mname'];
                    $_SESSION['lname'] = $fetch['lname'];
                    $_SESSION['userlvl'] = $fetch['ulevel'];
                    $_SESSION['station_id'] = $fetch['station_id']; // ✅ ADD THIS LINE

                    // Fetch the station name using the station_id
                    $station_id = $fetch['station_id']; // Assuming 'station' is the column name in tbl_user
                    $stationQuery = $conn->prepare("SELECT station_name FROM tbl_station WHERE station_id = ?");
                    $stationQuery->bind_param("i", $station_id);
                    $stationQuery->execute();
                    $stationResult = $stationQuery->get_result();

                    if ($stationResult->num_rows > 0) {
                        $stationFetch = $stationResult->fetch_assoc();
                        $_SESSION['station_name'] = $stationFetch['station_name']; // Store station name in session
                    } else {
                        $_SESSION['station_name'] = null; // Handle case where station is not found
                    }

                    // Respond based on user level
                    switch ($fetch['ulevel']) {
                        case 'Admin':
                            $data['response'] = 'success';
                            $data['ulevel'] = 'Admin';
                            break;
                        case 'ISREC':
                            $data['response'] = 'success1';
                            $data['ulevel'] = 'ISREC';
                            break;
                        case 'Viewer':
                            $data['response'] = 'success2';
                            $data['ulevel'] = 'Viewer';
                            break;
                        default:
                            $data['response'] = 'success';
                    }
                } else {
                    $data['response'] = 'error';
                    $data['message'] = 'Invalid password';
                }
            } else {
                $data['response'] = 'error';
                $data['message'] = 'Account is inactive';
            }
        } else {
            $data['response'] = 'error';
            $data['message'] = 'Invalid username';
        }
    } else {
        $data['response'] = 'error';
        $data['message'] = 'Username and password required';
    }

    echo json_encode($data);
?>