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
                            $sql = "SELECT st.seed_name, st.seed_id, it.intervention_name
                                    FROM tbl_seed_type st
                                    INNER JOIN tbl_intervention_type it ON st.int_type_id = it.int_type_id
                                    WHERE st.station_id = ? AND (st.seed_name LIKE ? OR it.intervention_name LIKE ?)
                                    ORDER BY st.seed_id DESC
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
                                        <button class="btn btn-success btn-sm edit-btn" 
                                            data-id="<?= htmlspecialchars($row['seed_id']) ?>"
                                            data-seed-name="<?= htmlspecialchars($row['seed_name']) ?>"
                                            data-intervention-name="<?= htmlspecialchars($row['intervention_name']) ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editSeedlingModal">
                                            Update
                                        </button>

                                            <!-- Archive Button -->
                                            <button class="btn btn-warning btn-sm archive-btn" 
                                                data-id="<?= htmlspecialchars($row['seed_id']) ?>">
                                                Archive
                                            </button>
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
