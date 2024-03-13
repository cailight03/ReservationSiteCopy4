<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: #1f3a7a;">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="admin_dashboard.php">
                <div class="sidebar-brand-icon">
                    <img src="images/nulogo.png" alt="NU Logo" width="32" height="35">
                </div>
                <div class="sidebar-brand-text mx-3">NU Laguna Reservations</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="admin_dashboard.php">
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
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#roomReservationDropdown" aria-expanded="true" aria-controls="roomReservationDropdown">
            <i class="fas fa-fw fa-calendar nav_icon"></i>
            <span class="sidebar-text">Room Reservation</span>
            </a>
        <div id="roomReservationDropdown" class="dropdown-menu collapse" aria-labelledby="roomReservationDropdown" data-parent="#accordionSidebar">
            <a class="dropdown-item" href="approved_roomReservations.php">Approved Reservations</a>
            <hr class="dropdown-divider">
            <a class="dropdown-item" href="past_reservations.php">Past Reservations</a>
            <hr class="dropdown-divider">
            <a class="dropdown-item" href="pending_roomReservations.php">Pending Reservations</a>
            <hr class="dropdown-divider">
            <a class="dropdown-item" href="cancelled_roomReservations.php">Cancelled Reservations</a>
        </div>
    </li>

         <!-- Nav Item - Vehicle Reservation -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#vehicleReservationDropdown" aria-expanded="true" aria-controls="vehicleReservationDropdown">
            <i class="fas fa-fw fa-calendar nav_icon"></i>
            <span class="sidebar-text">Vehicle Reservation</span>
        </a>
        <div id="vehicleReservationDropdown" class="dropdown-menu collapse" aria-labelledby="vehicleReservationDropdown" data-parent="#accordionSidebar">
            <a class="dropdown-item" href="approved_vehicleReservations.php">Approved Reservations</a>
            <hr class="dropdown-divider">
            <a class="dropdown-item" href="past_vehicle_reservations.php">Past Reservations</a>
            <hr class="dropdown-divider">
            <a class="dropdown-item" href="pending_vehicleReservations.php">Pending Reservations</a>
            <hr class="dropdown-divider">
            <a class="dropdown-item" href="cancelled_vehicleReservations.php">Cancelled Reservations</a>
        </div>
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
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#auditTrailDropdown" aria-expanded="true" aria-controls="auditTrailDropdown">
            <i class="fas fa-fw fa-clipboard-list nav_icon"></i>
            <span class="sidebar-text">Audit Trail</span>
        </a>
        <div id="auditTrailDropdown" class="dropdown-menu collapse" aria-labelledby="auditTrailDropdown" data-parent="#accordionSidebar">
            <a class="dropdown-item" href="category_management_logs.php">Venues Audit Trail</a>
            <hr class="dropdown-divider">
            <a class="dropdown-item" href="room_management_logs.php">Rooms Audit Trail</a>
            <hr class="dropdown-divider">
            <a class="dropdown-item" href="vehicle_management_logs.php">Vehicles Audit Trail</a>
            <hr class="dropdown-divider">
            <a class="dropdown-item" href="email_configuration_logs.php">Signatories Audit Trail</a>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Nav Item - Sign Out -->
    <li class="nav-item text-center">
        <a class="nav-link" href="../login_logout_controller/logout.php">
            <i class="fas fa-fw fa-sign-out-alt nav_icon"></i>
            <span>Sign Out</span>
        </a>
    </li>

            <!-- Sidebar Toggler (Sidebar) -->
            <!-- <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div> -->

        

        </ul>