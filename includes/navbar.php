 <!-- Sidebar -->
 <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

     <!-- Sidebar - Brand -->
     <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
         <div class="sidebar-brand-icon rotate-n-15">
             <i class="fas fa-laugh-wink"></i>
         </div>
         <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
     </a>

     <!-- Divider -->
     <hr class="sidebar-divider my-0">

     <!-- Nav Item - Dashboard -->
     <li class="nav-item active">   
         <a class="nav-link" href="index.php">
             <i class="fas fa-fw fa-tachometer-alt"></i>
             <span>Dashboard</span></a>
     </li>

     <!-- Divider -->
     <hr class="sidebar-divider">
     <!-- Nav Item - Charts -->
     <li class="nav-item">
         <a class="nav-link" href="1UserManagement.php">
             <i class="fas fa-fw fa-chart-area"></i>
             <span>User Management</span></a>
     </li>

     <!-- Nav Item - Pages Collapse Menu -->
     <li class="nav-item">
         <a class="nav-link" href="2InterventionManagement.php">
             <i class="fas fa-fw fa-cog"></i>
             <span>Intervention Management</span>
         </a>
     </li>

     <!-- Nav Item - Utilities Collapse Menu -->
     <li class="nav-item">
         <a class="nav-link" href="3DistributionManagement.php">
             <i class="fas fa-fw fa-wrench"></i>
             <span>Distribution Management</span>
         </a>
     </li>





     <!-- Divider -->
     <hr class="sidebar-divider d-none d-md-block">

     <!-- Sidebar Toggler (Sidebar) -->
     <div class="text-center d-none d-md-inline">
         <button class="rounded-circle border-0" id="sidebarToggle"></button>
     </div>



 </ul>
 <!-- End of Sidebar -->


 <!-- Scroll to Top Button-->
 <a class="scroll-to-top rounded" href="#page-top">
     <i class="fas fa-angle-up"></i>
 </a>
 li class="nav-item dropdown no-arrow">
 <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
     aria-haspopup="true" aria-expanded="false">
     <span class="mr-2 d-none d-lg-inline text-black small font-weight-bold" style="color: green">
         <?php
            // Retrieve the username from the database
            $username = $_SESSION['user'];
            echo 'Logged in as ' . htmlspecialchars($username);
            ?>

     </span>
     <i style="font-size: 40px;" class="bx bxs-user-circle"></i>

 </a>
 <!-- Dropdown - User Information -->
 <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
     aria-labelledby="userDropdown">
     <div class="dropdown-divider"></div>
     <form action="" id="logout-link" method="post">
         <a class="dropdown-item" href="#" id="logout-link">
             <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
             Logout
         </a>
     </form>
 </div>
 </li>