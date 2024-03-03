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

    <title>NULR Admin - Pending Reservations</title>

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

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- <div class="topbar-divider d-none d-sm-block"></div>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li> -->

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <h2 class="grid-header" id="header">Pending Reservations</h2>

                    <!-- Table to display reservations -->
                    <table id="roomresTable" class="table table-hover table-bordered">
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

                            // -----dito ka magpapalit later to approved, cancelled, or pending------ //

                            // Fetch data from the database and populate the table rows
                            $query = "SELECT * FROM reservationdb.reservations WHERE status='Pending'"; // Change this query to filter by status dito!!
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
                            <span>Copyright &copy; NU Laguna Reservation Site 2024</span>
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

                <!-- DataTables JavaScript -->
                <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
                <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

                <!-- Custom scripts for all pages-->
                <script src="js/sb-admin-2.min.js"></script>

                <!-- Page level plugins -->
                <script src="vendor/chart.js/Chart.min.js"></script>

                <!-- Page level custom scripts -->
                <script src="js/demo/chart-area-demo.js"></script>
                <script src="js/demo/chart-pie-demo.js"></script>
                

                <script>
                $(document).ready(function() {
                    $('#roomresTable').DataTable({
                        "paging": true, // Enable pagination
                        "searching": true // Enable search
                    });
                });
                </script>
        </body>
    </html>