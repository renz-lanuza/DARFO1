    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-custom sidebar sidebar-light accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex flex-column align-items-center justify-content-center" href="#" onclick="event.preventDefault();">
            <div class="sidebar-brand-icon">
                <img src="img/da.png" alt="AG PNGH Logo" width="60">
            </div>

        </a>
        <h6 class="sidebar-title">STATIONS PORTAL</h6>
        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="index.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Nav Item - User Management -->
        <li class="nav-item">
            <a class="nav-link" href="1UserManagement.php">
                <i class="fas fa-fw fa-user"></i>
                <span class="sidebar-text">User Management</span>
            </a>
        </li>

        <!-- Nav Item - Intervention Management -->
        <li class="nav-item">
            <a class="nav-link" href="2InterventionManagement.php">
                <i class="fas fa-fw fa-cogs"></i>
                <span class="sidebar-text">Intervention Management</span>
            </a>
        </li>

        <!-- Nav Item - Distribution Management -->
        <li class="nav-item">
            <a class="nav-link" href="3DistributionManagement.php">
                <i class="fas fa-fw fa-truck"></i>
                <span class="sidebar-text">Distribution Management</span>
            </a>
        </li>

        <!-- Nav Item - Intervention Type Management
        <li class="nav-item">
            <a class="nav-link" href="4InterventionType.php">
                <i class="fas fa-fw fa-tools"></i>
                <span class="sidebar-text">Intervention Type Management</span>
            </a>
        </li> -->

        <!-- Nav Item - Seed Type Management -->
        <!-- <li class="nav-item">
            <a class="nav-link" href="5SeedType.php">
                <i class="fas fa-tags"></i>
                <span class="sidebar-text">Classification Management</span>
            </a>
        </li> -->

        <!-- Nav Item - Seed Type Management -->
        <li class="nav-item">
            <a class="nav-link" href="6CooperativeManagement.php">
                <i class="fas fa-home"></i>
                <span class="sidebar-text">Cooperative Management</span>
            </a>
        </li>
<!-- 

        <li class="nav-item">
            <a class="nav-link" href="7UnitManagement.php">
                <i class="fas fa-home"></i>
                <span class="sidebar-text">Unit Management</span>
            </a>
        </li> -->
        <li class="nav-item">
            <a class="nav-link" href="8BeneficiaryManagement.php">
                <i class="fas fa-users"></i>
                <span class="sidebar-text">Beneficiary Management</span>
            </a>
        </li>

        <!-- Nav Item - Utilities Collapse Menu -->
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" 
            aria-expanded="false" aria-controls="collapseUtilities">
                <i class="fas fa-wrench"></i>
                <span class="sidebar-text">Utilities</span>
            </a>
            <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                <div class="bg-gradient-light py-2 collapse-inner rounded utilities-scroll">
                    <h6 class="collapse-header text-uppercase font-weight-bold text-dark">Utility Options:</h6>
                    <a class="collapse-item" href="4InterventionType.php">
                        <i class="fas fa-tools mr-2"></i> Intervention Type 
                    </a>
                    <a class="collapse-item" href="5SeedType.php">
                        <i class="fas fa-tags mr-2"></i> Classification 
                    </a>
                    <a class="collapse-item" href="7UnitManagement.php">
                        <i class="fas fa-th-large mr-2"></i> Unit Management
                    </a>
                    <!-- Add more items if needed -->
                </div>
            </div>
        </li>
        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">
    </ul>
    <!-- End of Sidebar -->

    <!-- Hamburger Icon below Logo -->
    <div class="hamburger-icon" id="hamburger-icon" onclick="toggleSidebar()">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles (for enhancement) -->
    <style>
        .utilities-scroll {
            max-height: 200px; /* Adjust height as needed */
            overflow-y: auto; /* Enables vertical scrolling */
            scrollbar-width: thin; /* For Firefox */
            scrollbar-color: #ccc #f8f9fa; /* Custom scrollbar color */
        }

        /* Custom scrollbar for Webkit (Chrome, Edge, Safari) */
        .utilities-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .utilities-scroll::-webkit-scrollbar-thumb {
            background-color: #aaa;
            border-radius: 4px;
        }

        .utilities-scroll::-webkit-scrollbar-track {
            background: #f8f9fa;
        }
        /* Enhancing the dropdown items */  
        .collapse-item {
            padding: 10px 15px;
            border-radius: 5px;
            transition: all 0.3s ease-in-out;
            font-weight: 500;
        }

        /* Change background on hover */
        .collapse-item:hover {
            background-color: #e3f2fd; /* Light blue shade */
            transform: translateX(5px); /* Slight movement effect */
        }
        /* Custom Green Gradient for Sidebar */
        .bg-gradient-custom {
            background: linear-gradient(to right, #0D7C66, #0A6B5A);
        }

        /* Sidebar Active Item */
        .nav-item.active .nav-link {
            background-color: #0D7C66;
            color: #fff;
        }

        /* Hover Effect with Animation */
        .nav-item .nav-link {
            color: #fff !important;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease-in-out;
            /* Adding transition for smooth effect */
        }

        /* Hover Effects */
        .nav-item .nav-link:hover {
            background-color: #0A6B5A;
            transform: scale(1.05);
            /* Slight scale animation */
        }

        /* Sidebar Text and Icon Color */
        .sidebar-light .nav-link {
            color: #fff !important;
            font-size: 16px;
            font-weight: 500;
        }

        .sidebar-light .nav-link i {
            color: #fff;
        }

        .sidebar-light .nav-link:hover i {
            color: #c2c2c2;
        }

        /* Active Item Icon */
        .nav-item.active .nav-link i {
            color: #f8f9fa;
        }

        /* Adding better spacing for labels */
        .sidebar-text {
            margin-left: 10px;
            font-weight: 600;
        }

        /* Improved readability for labels */
        .sidebar-light .nav-link:hover {
            background-color: #0A6B5A;
            color: #fff;
        }

        .sidebar-light .nav-link:hover .sidebar-text {
            color: #f8f9fa;
        }

        /* Mobile-friendly Sidebar */
        @media (max-width: 768px) {
            .sidebar-text {
                font-size: 14px;
            }
        }

        /* Styling the logo container */
        .sidebar-brand {
            position: relative;
            text-align: center;
        }

        /* Hamburger icon styles */
        .hamburger-icon {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 22px;
            position: absolute;
            top: 20px;
            left: 15px;
            /* Positioned on the top left of the screen */
            cursor: pointer;
            z-index: 9999;
            /* Ensures it's always on top */
        }

        .hamburger-icon span {
            width: 30px;
            height: 4px;
            background-color: #808080;
            /* Greyish color */
            border-radius: 5px;
        }

        /* Optional: Add hover effect */
        .hamburger-icon:hover span {
            background-color: rgb(255, 255, 255);
            /* Darker grey when hovered */
        }

        /* Sidebar hidden by default on small screens */
        .sidebar.hidden {
            display: none;
        }

        /* Adjust the sidebar layout */
        @media (max-width: 768px) {
            .sidebar {
                position: absolute;
                top: 0;
                left: 0;
                z-index: 9998;
                height: 100%;
            }
        }

        /* Center the Sidebar Title */
        .sidebar-title {
            text-align: center;
            color: #fff;
            /* Matching the sidebar text color */
            font-weight: 300;
            font-size: 16px;
            margin-top: 5px;
            /* Adjust spacing */
        }
        
    </style>
    <!-- JavaScript to toggle sidebar visibility with animation -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('accordionSidebar');
            sidebar.classList.toggle('hidden');
        }
    </script>
