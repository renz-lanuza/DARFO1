<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DEPARTMENT OF AGRICULTURE</title>
  <link rel="icon" href="img/da.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />
  <link rel="stylesheet" href="style.css"> <!-- Assuming you have a style.css for styling -->
  <link href="https://cdn.jsdelivr.net/npm/boxicons/css/boxicons.min.css" rel="stylesheet">

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      overflow: hidden;
      background: linear-gradient(to bottom, black, darkgreen, seagreen, goldenrod, black);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding-top: 60px;
      /* Prevents content from hiding under fixed header */
    }

    header {
      background-color: rgb(18, 105, 88);
      color: white;
      padding: 15px 20px;
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 10;
      box-shadow: 0 2px 5px rgba(243, 227, 227, 0.2);
    }

    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header-logo {
      width: 70px;
      height: auto;
      margin-right: 10px;
    }

    .header-divider {
      width: 2px;
      height: 50px;
      background-color: white;
      margin: 0 15px;
    }

    .header-titles h1 {
      margin: 0;
      font-size: 24px;
    }

    .header-titles h2 {
      margin: 0;
      font-size: 18px;
    }

    .menu {
      margin-left: auto;
    }

    .menu ul {
      list-style: none;
      display: flex;
    }

    .menu li {
      margin-left: 20px;
    }

    .menu a {
      color: white;
      text-decoration: none;
      font-size: 16px;
      padding: 10px 15px;
      border-radius: 5px;
      transition: background 0.3s;
    }

    .menu a:hover {
      background-color: rgba(255, 255, 255, 0.2);
    }

    .image-overlay {
      width: 100%;
      max-width: 440px;
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 10px;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
      padding: 20px;
      margin-top: 90px;
      /* Increased space for better visual */
    }

    #da_logo {
      width: 145px;
      height: auto;
      border-radius: 50%;
      margin-bottom: 5px;
      margin-top: 10px;
    }

    .wrapper form {
      padding: 30px 25px 25px 25px;
    }

    .wrapper form .row {
      height: 45px;
      margin-bottom: 15px;
      position: relative;
    }

    .wrapper form .row input {
      height: 100%;
      width: 100%;
      outline: none;
      padding-left: 60px;
      border-radius: 5px;
      border: 1px solid lightgrey;
      font-size: 16px;
      transition: border-color 0.3s ease;
    }

    .wrapper form .row input:focus {
      border-color: #16a085;
    }

    .wrapper form .row i {
      position: absolute;
      top: 50%;
      left: 15px;
      transform: translateY(-50%);
      color: #16a085;
    }

    .wrapper form .button {
      display: flex;
      justify-content: center;
    }

    .btnn {
      width: 100%;
      background: #16a085;
      border: none;
      padding: 12px;
      border-radius: 5px;
      color: white;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
      font-size: 16px;
    }

    .btnn:hover {
      background: #148f77;
    }

    @media (max-width: 768px) {
      .image-overlay {
        margin-top: 80px;
        /* Adjusted for smaller screen */
        width: 90%;
        max-width: 350px;
        padding: 15px;
      }

      .header-content {
        flex-direction: column;
        align-items: flex-start;
      }

      .menu ul {
        flex-direction: column;
        align-items: flex-start;
        margin-top: 10px;
      }

      .menu li {
        margin-left: 0;
        margin-bottom: 10px;
      }
    }

    .input-group {
      position: relative;
      margin-bottom: 15px;
    }

    .input {
      border: solid 1.5px #9e9e9e;
      border-radius: 1rem;
      background: none;
      padding: 1rem 1rem 1rem 40px;
      /* Increased left padding for space */
      font-size: 1rem;
      color: black;
      width: 100%;
      transition: border 150ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    .input-icon {
      position: absolute;
      left: 15px;
      /* Adjusted to create more space between the icon and the border */
      top: 50%;
      transform: translateY(-50%);
      font-size: 18px;
    }

    .user-label {
      position: absolute;
      left: 40px;
      /* Adjusted to avoid overlap with the icon */
      color: #000;
      pointer-events: none;
      transform: translateY(1rem);
      transition: 150ms cubic-bezier(0.4, 0, 0.2, 1);
    }

    .input:focus,
    .input:valid {
      outline: none;
      border: 1.5px solidrgb(37, 255, 4);
    }

    .input:focus~.user-label,
    .input:valid~.user-label {
      transform: translateY(-50%) scale(0.8);
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 3%;
      padding: 0 .2em;
      color: rgb(0, 0, 0);
    }
  </style>
</head>

<body>
  <header>
    <div class="header-content">
      <img src="img/da.png" alt="Logo" class="header-logo"> <!-- Logo on the left -->
      <div class="header-divider"></div>
      <div class="header-titles"> <!-- Container for titles -->
        <h1>STATIONS PORTAL</h1>
        <h2>DEPARTMENT OF AGRICULTURE REGIONAL FIELD OFFICE 1</h2>
      </div>
      <div class="menu"> <!-- Menu container -->
        <ul>
          <!-- <li><a href="index.php" class="slide-link">HOME</a></li>
          <li><a href="about.php" class="slide-link">ABOUT</a></li>
          <li><a href="services.php" class="slide-link">SERVICES</a></li>
          <li><a href="contact.php" class="slide-link">CONTACT</a></li> -->
        </ul>
      </div>
    </div>
  </header>

  <div class="image-overlay">
    <div class="container">
      <div class="wrapper">
        <center><img src="img/da.png" id="da_logo"></center> <!-- Logo -->
        <form action="#" onsubmit="return login()">
          <div class="form">
            <div class="input-group">
              <span class="input-icon"><i class="bx bx-user"></i></span>
              <input type="text" id="uname" placeholder=" " required class="input">
              <label for="uname" class="user-label">Username</label>
            </div>
            <div class="input-group">
              <span class="input-icon"><i class="bx bx-lock"></i></span>
              <input type="password" id="pword" placeholder=" " required class="input">
              <label for="pword" class="user-label">Password</label>
            </div>
            <div class="row button">
              <br>
              <button type="submit" class="btnn">Login</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.all.min.js"></script>

  <script>
    document.addEventListener("keypress", function(event) {
      if (event.key === "Enter") {
        login();
      }
    });

    function login() {
      var username = $('#uname').val();
      var password = $('#pword').val();

      var formData = new FormData();
      formData.append('username', username);
      formData.append('password', password);

      if (username === "" || password === "") {
        Swal.fire({
          title: 'Please complete the login form',
          icon: 'error',
          showConfirmButton: false,
          timer: 2000
        });
        return false; // Prevent form submission if validation fails
      } else {
        $.ajax({
          url: 'loginval.php', // Server-side validation script
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          dataType: 'json',
          success: function(data) {
            if (data.response === 'success') {
              Swal.fire({
                title: 'Login Successfully',
                html: 'Welcome, ' + data.ulevel,
                icon: 'success',
                showConfirmButton: false,
                timer: 1000
              });
              setTimeout(function() {
                location.replace('admin/index.php'); // Redirect after login
              }, 1000);
            } else if (data.response === 'success1') {
              Swal.fire({
                title: 'Login Successfully',
                html: 'Welcome, ' + data.ulevel,
                icon: 'success',
                showConfirmButton: false,
                timer: 1000
              });
              setTimeout(function() {
                location.replace('users/mao/index.php'); // Redirect after login
              }, 1000);
            } else if (data.response === 'success2') {
              Swal.fire({
                title: 'Login Successfully',
                html: 'Welcome, ' + data.ulevel,
                icon: 'success',
                showConfirmButton: false,
                timer: 1000
              });
              setTimeout(function() {
                location.replace('users/keyperson/index.php'); // Redirect after login
              }, 1000);
            } else if (data.response === 'error') {
              Swal.fire({
                title: 'Wrong Username or Password',
                icon: 'error',
                showConfirmButton: false,
                timer: 1000
              });

              // Clear input fields after an incorrect login attempt
              $('#uname').val('');
              $('#pword').val('');

              // Focus back on the username field for better UX
              $('#uname').focus();
            }
          }
        });
      }
      return false; // Prevent default form submission
    }
  </script>
</body>

</html>
