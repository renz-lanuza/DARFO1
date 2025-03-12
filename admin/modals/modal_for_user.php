<!-- Add SweetAlert CDN for popups -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Modal for Adding User -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width: 600px;">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #0D7C66; color: white;">
        <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addUserForm">
          <!-- First Name -->
          <div class="mb-3">
            <label for="firstName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstName" name="firstName" required>
          </div>
          <!-- Middle Name -->
          <div class="mb-3">
            <label for="middleName" class="form-label">Middle Name</label>
            <input type="text" class="form-control" id="middleName" name="middleName">
          </div>
          <!-- Last Name -->
          <div class="mb-3">
            <label for="lastName" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="lastName" name="lastName" required>
          </div>
          <!-- Username -->
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
          </div>
          <!-- Password -->
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <!-- User Level -->
          <div class="mb-3">
            <label for="userLevel" class="form-label">User Level</label>
            <select class="form-control" id="userLevel" name="userLevel" required>
              <option value="" disabled selected>Select a User Level</option>
              <option value="Admin">Admin</option>
              <option value="ISREC">ISREC</option>
              <option value="Viewer">Viewer</option>
            </select>
          </div>
          <!-- Station -->
          <?php
          // Database connection
          include('../conn.php');
          // Check connection

          // Fetch stations from the database
          $stationQuery = "SELECT station_id, station_name FROM tbl_station";
          $stationResult = $conn->query($stationQuery);
          ?>
          <div class="mb-3">
            <label for="station" class="form-label">Station</label>
            <select class="form-control" id="station" name="station" required>
              <option value="" disabled selected>Select a Station</option>
              <?php
              if ($stationResult->num_rows > 0) {
                while ($row = $stationResult->fetch_assoc()) {
                  echo '<option value="' . $row["station_id"] . '">' . $row["station_name"] . '</option>';
                }
              }
              ?>
            </select>
          </div>
          <?php
          $conn->close();
          ?>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success">Add</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  /* Enhance Select Dropdown */
  .form-select {
    border-radius: 8px;
    /* Rounded corners */
    border: 2px solid #0D7C66;
    /* Green border */
    padding: 10px;
    font-size: 16px;
    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
    /* Soft shadow */
    transition: all 0.3s ease-in-out;
  }

  /* Hover & Focus Effect */
  .form-select:hover,
  .form-select:focus {
    border-color: #055D48;
    /* Darker green on hover */
    box-shadow: 0px 0px 8px rgba(13, 124, 102, 0.5);
  }
</style>

<!-- Edit User Modal -->
<div class="modal fade" id="updateUserModal" tabindex="-1" role="dialog" aria-labelledby="updateUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width: 600px;">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #0D7C66; color: white;">
        <h5 class="modal-title" id="updateUserModalLabel">Update User Details</h5>
        <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="updateUserForm">
          <input type="hidden" id="userId" name="userId">

          <div class="form-group">
            <label for="uname">Username</label>
            <input type="text" class="form-control rounded-input" id="uname" name="username" required>
          </div>

          <div class="form-group">
            <label for="ulevel">User Level</label>
            <select class="form-control" id="ulevel" name="ulevel">
              <option value="Admin">Admin</option>
              <option value="ISREC">ISREC</option>
              <option value="Viewer">Viewer</option>
            </select>
          </div>

          <div class="form-group">
            <label for="fname">First Name</label>
            <input type="text" class="form-control rounded-input" id="fname" name="fname" required>
          </div>

          <div class="form-group">
            <label for="mname">Middle Name</label>
            <input type="text" class="form-control rounded-input" id="mname" name="mname">
          </div>

          <div class="form-group">
            <label for="lname">Last Name</label>
            <input type="text" class="form-control rounded-input" id="lname" name="lname" required>
          </div>

          <!-- Station Selection -->
          <?php
          include('../conn.php');
          $stationQuery = "SELECT station_id, station_name FROM tbl_station";
          $stationResult = $conn->query($stationQuery);
          ?>
          <div class="mb-3">
            <label for="userstation" class="form-label">Station</label>
            <select class="form-control" id="userstation" name="station" required>
              <option value="" disabled selected>Select a Station</option>
              <?php
              while ($row = $stationResult->fetch_assoc()) {
                  echo '<option value="' . $row["station_id"] . '">' . $row["station_name"] . '</option>';
              }
              ?>
            </select>
          </div>
          <?php $conn->close(); ?>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success" id="updateUserBtn">Save changes</button>
      </div>
    </div>
  </div>
</div>
