<?php
include '../config/connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: ../login.php');
    exit();
}





// Query to retrieve all approved booked time slots for all rooms and dates
$sql = "SELECT time_slot FROM reservations WHERE status = 'Approved'";
$result = mysqli_query($connection, $sql);

// Prepare data for JavaScript
$peak_time_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $timeSlotsArray = explode(',', $row['time_slot']);
    $peak_time_data = array_merge($peak_time_data, $timeSlotsArray);
}

// Split time ranges into individual 30-minute intervals
$interval = 1800; // 30 minutes in seconds
$peak_time_slots = [];

foreach ($peak_time_data as $range) {
    list($start, $end) = explode("-", $range);

    $startTime = strtotime($start);
    $endTime = strtotime($end);

    for ($current = $startTime; $current < $endTime; $current += $interval) {
        $peak_time_slots[] = date("g:i A", $current) . '-' . date("g:i A", $current + $interval);
    }
}

// Sort the time slots
usort($peak_time_slots, function ($a, $b) {
    $timeA = strtotime(explode('-', $a)[0]);
    $timeB = strtotime(explode('-', $b)[0]);
    return $timeA - $timeB;
});

// Convert PHP data to JSON for JavaScript
$peak_time_json = json_encode($peak_time_slots);

$sql = "SELECT room_name, COUNT(*) as count FROM reservations GROUP BY room_name ORDER BY count DESC LIMIT 5";
$result = mysqli_query($connection, $sql);

// Prepare data for JavaScript
$room_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $room_data[] = $row;
}

// Convert PHP data to JSON for JavaScript
$room_json = json_encode($room_data);

// Query to get reservations per day
$sqlReservationsPerDay = "SELECT date FROM reservations";
$resultReservationsPerDay = mysqli_query($connection, $sqlReservationsPerDay);

// Prepare data for JavaScript
$reservations_per_day_data = [];
while ($row = mysqli_fetch_assoc($resultReservationsPerDay)) {
    // Split dates into array
    $dates = explode(',', $row['date']);
    
    // Add each date to the data array
    foreach ($dates as $date) {
        $reservations_per_day_data[] = [
            'reservation_day' => $date
        ];
    }
}

// Convert PHP data to JSON for JavaScript
$reservations_per_day_json = json_encode($reservations_per_day_data);





// Query to get the count of canceled reservations
$sqlCanceled = "SELECT COUNT(*) AS canceled_count FROM reservations WHERE status = 'Cancelled'";
$resultCanceled = mysqli_query($connection, $sqlCanceled);
$rowCanceled = mysqli_fetch_assoc($resultCanceled);
$canceledCount = $rowCanceled['canceled_count'];

// Query to get the count of all reservations
$sqlTotal = "SELECT COUNT(*) AS total_count FROM reservations";
$resultTotal = mysqli_query($connection, $sqlTotal);
$rowTotal = mysqli_fetch_assoc($resultTotal);
$totalCount = $rowTotal['total_count'];

// Calculate cancellation rate
$cancellationRate = ($canceledCount / $totalCount) * 100;
?>






<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
     <link rel="stylesheet" href="..\css\UI_Pages.css">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

    <?php include 'navitems.php'; ?>
       

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 12, 2019</div>
                                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-donate text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 7, 2019</div>
                                        $290.29 has been deposited into your account!
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 2, 2019</div>
                                        Spending Alert: We've noticed unusually high spending for your account.
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                            </div>
                        </li>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
                                <span class="badge badge-danger badge-counter">7</span>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                    Message Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_1.svg"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div class="font-weight-bold">
                                        <div class="text-truncate">Hi there! I am wondering if you can help me with a
                                            problem I've been having.</div>
                                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_2.svg"
                                            alt="...">
                                        <div class="status-indicator"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">I have the photos that you ordered last month, how
                                            would you like them sent to you?</div>
                                        <div class="small text-gray-500">Jae Chun · 1d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_3.svg"
                                            alt="...">
                                        <div class="status-indicator bg-warning"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Last month's report looks great, I am very happy with
                                            the progress so far, keep up the good work!</div>
                                        <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Am I a good boy? The reason I ask is because someone
                                            told me that people say this to all dogs, even if they aren't good...</div>
                                        <div class="small text-gray-500">Chicken the Dog · 2w</div>
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Douglas McGee</span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                                        <h2 class="grid-header" id="header">Pending Reservations</h2>

                        <!-- Table to display reservations -->
                        <table id="dataTable" class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Reservation ID</th>
                                    <th>Date Submitted</th>
                                    <th>Department</th>
                                    <th>Requestor</th>
                                    <th>Activity</th>
                                    <th>Date</th>
                                    <th>Time Slot</th>
                                    <th>Room Name</th>
                                    <th>Items Needed</th>
                                    <th>Remarks</th>
                                    <th>Status</th>
                                    <th>Number of Attendees</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch data from the database and populate the table rows
                        $query = "SELECT * FROM reservationdb.reservations WHERE status='pending'";
                        $result = mysqli_query($connection, $query);

                        while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['date_submitted']}</td>";
                        echo "<td>{$row['department']}</td>";
                        echo "<td>{$row['requestor']}</td>";
                        echo "<td>{$row['activity_name']}</td>";
                        echo "<td>{$row['date']}</td>";
                        echo "<td>{$row['time_slot']}</td>";
                        echo "<td>{$row['room_name']}</td>";

                        // Decode the JSON string for items_needed
                        $items_needed = json_decode($row['items_needed'], true);

                        // Check if items_needed is not null and is an array
                        if (!is_null($items_needed) && is_array($items_needed)) {
                            // Initialize an array to store formatted item strings
                            $formatted_items = [];

                            // Iterate through each item in items_needed
                            foreach ($items_needed as $item) {
                                // Build the formatted string (e.g., "table 9")
                                $formatted_items[] = $item['item'] . ' ' . $item['quantity'];
                            }

                            // Implode the formatted item strings with a comma separator
                            $formatted_items_str = implode(', ', $formatted_items);

                            // Output the formatted items
                            echo "<td>{$formatted_items_str}</td>";
                        } else {
                            // If items_needed is null or not an array, display "None"
                            echo "<td>None</td>";
                        }

                        echo "<td>{$row['remarks']}</td>";
                        echo "<td>{$row['status']}</td>";
                        echo "<td>{$row['num_of_attendees']}</td>";
                        echo "<td>";

                        echo "<a href='#?id={$row['id']}' class='btn btn-primary action-button'></i> Approve</a>";
                        echo "<a href='decline_roomreservations.php?id={$row['id']}' class='btn btn-danger action-button'></i> Reject</a>";
                        echo "</td>";
                        echo "</tr>";
                        }

                                ?>
                            </tbody>
                        </table>
                        <button class="btn btn-primary" id="addButton">Add</button>

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2021</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

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
                    <a class="btn btn-primary" href="#">Logout</a>
                </div>
            </div>
        </div>
    </div>
   

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>

</html>