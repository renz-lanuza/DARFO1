<?php
include('../../conn.php'); // Include database connection

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search_query)) {
    $query = "SELECT * FROM tbl_user 
              INNER JOIN tbl_station ON tbl_user.station_id = tbl_station.station_id
              WHERE (username LIKE '%$search_query%' 
              OR CONCAT(fname, ' ', COALESCE(mname, ''), ' ', lname) LIKE '%$search_query%')
              AND status != 3 
              ORDER BY ulevel";
} else {
    $query = "SELECT * FROM tbl_user 
              INNER JOIN tbl_station ON tbl_user.station_id = tbl_station.station_id 
              WHERE status != 3 
              ORDER BY ulevel 
              LIMIT 10"; // Default limit
}

$view = mysqli_query($conn, $query);

if (!$view) {
    die("Query failed: " . mysqli_error($conn));
}

if (mysqli_num_rows($view) > 0) {
    // Generate and return the HTML for the table rows
    while ($data = mysqli_fetch_assoc($view)) {
        $uid = htmlspecialchars($data['uid']);
        $uname = htmlspecialchars($data['username']);
        $fname = htmlspecialchars($data['fname']);
        $mname = htmlspecialchars($data['mname']);
        $lname = htmlspecialchars($data['lname']);
        $userlvl = htmlspecialchars($data['ulevel']);
        $station = htmlspecialchars($data['station_name']);
        $status = htmlspecialchars($data['status']);

        echo "<tr>
                <td>$uname</td>
                <td>$lname, $fname $mname</td>
                <td>$userlvl</td>
                <td>$station</td>
                <td>";
        if ($status == 1) {
            echo "<a onclick=\"toggleStatus('$uname', 'deactivate')\" class='btn btn-secondary'>Inactive</a>";
        } else {
            echo "<a onclick=\"toggleStatus('$uname', 'activate')\" class='btn btn-success'>Active</a>";
        }
        echo "</td>
              <td>
                  <a href='#' class='btn' style='background-color: #DCFFB7; color: black;' 
                     data-toggle='modal' data-target='#updateUserModal' 
                     data-user-id='$uid'>Update</a>    
                  <button class='btn btn-warning' onclick=\"confirmArchive('$uid')\">Archive</button>
              </td>
            </tr>";
    }
} else {
    // Display "No users found" if no results
    echo "<tr><td colspan='6' class='text-center text-danger'>No Users Found</td></tr>";
}
?>
