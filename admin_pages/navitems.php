<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #1f3a7a;">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="admin_dash.php">
                <div class="sidebar-brand-icon">
                    <img src="images/nulogo.png" alt="NU Logo" width="32" height="35">
                </div>
                <div class="sidebar-brand-text mx-3">NU Laguna Reservations</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="admin_dash.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>


        <!-- Nav Item - Room Reservation -->
        <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="roomReservationDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-fw fa-calendar nav_icon"></i>
        <span class="sidebar-text">Room Reservation</span>
    </a>
    <div class="dropdown-menu" aria-labelledby="roomReservationDropdown">
        <a class="dropdown-item" href="approved_reservations.php">Approved Reservations</a>
        <a class="dropdown-item" href="pending_reservations.php">Pending Reservations</a>
        <a class="dropdown-item" href="cancelled_reservations.php">Cancelled Reservations</a>
    </div>
</li>

        <!-- Nav Item - Vehicle Reservation -->
        <li class="nav-item">
            <a class="nav-link" href="vehicle_reservations.php">
                <i class="fas fa-fw fa-car nav_icon"></i> 
                <span class="sidebar-text">Vehicle Reservation</span>
            </a>
        </li>

        <!-- Nav Item - Manage Rooms -->
        <li class="nav-item">
            <a class="nav-link" href="manage_rooms.php">
                <i class="fas fa-fw fa-door-open nav_icon"></i>
                <span>Manage Rooms</span>
            </a>
        </li>

        <!-- Nav Item - Manage Vehicles -->
        <li class="nav-item">
            <a class="nav-link" href="manage_vehicle.php">
                <i class="fas fa-fw fa-truck nav_icon"></i>
                <span>Manage Vehicles</span>
            </a>
        </li>

        <!-- Nav Item - Email Configuration -->
        <li class="nav-item">
            <a class="nav-link" href="email_config.php">
                <i class="fas fa-fw fa-envelope nav_icon"></i> 
                <span>Email Configuration</span>
            </a>
        </li>

        <!-- Nav Item - Audit Trail -->
        <li class="nav-item">
            <a class="nav-link" href="audit_trail.php">
                <i class="fas fa-fw fa-clipboard-list nav_icon"></i>
                <span>Audit Trail</span>
            </a>
        </li>

        <!-- Nav Item - Sign Out -->
        <li class="nav-item">
            <a class="nav-link" href="logout_admin.php">
                <i class="fas fa-fw fa-sign-out-alt nav_icon"></i>
                <span>Sign Out</span>
            </a>
        </li>

            

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        

        </ul>