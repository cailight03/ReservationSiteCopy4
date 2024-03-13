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

// Query to count pending reservations
$sql_pending_reservations = "SELECT COUNT(*) AS pending_reservations FROM reservations WHERE status = 'Pending'";
$result_pending_reservations = $connection->query($sql_pending_reservations);

if ($result_pending_reservations->num_rows > 0) {
    // Output data of the pending reservations
    while($row_pending_reservations = $result_pending_reservations->fetch_assoc()) {
        $pending_reservations = $row_pending_reservations["pending_reservations"];
    }
} else {
    $pending_reservations = 0;
}

// Query to count pending vehicle reservations
$sql_pending_vehicle_reservations = "SELECT COUNT(*) AS pending_vehicle_reservations FROM vehicle_reservations WHERE status = 'Pending'";
$result_pending_vehicle_reservations = $connection->query($sql_pending_vehicle_reservations);

if ($result_pending_vehicle_reservations->num_rows > 0) {
    // Output data of the pending vehicle reservations
    while($row_pending_vehicle_reservations = $result_pending_vehicle_reservations->fetch_assoc()) {
        $pending_vehicle_reservations = $row_pending_vehicle_reservations["pending_vehicle_reservations"];
    }
} else {
    $pending_vehicle_reservations = 0;
}


$sql = "SELECT room_name, COUNT(*) AS reservation_count 
        FROM reservations 
        GROUP BY room_name 
        ORDER BY reservation_count DESC 
        LIMIT 1";

$result = $connection->query($sql);

if ($result->num_rows > 0) {
    // Output data of the room with the most reservations
    while($row = $result->fetch_assoc()) {
        $most_reserved_room = $row["room_name"];
    }
} else {
    $most_reserved_room = "No reservations found";
}

$sql = "SELECT vehicle_name, COUNT(*) AS reservation_count 
        FROM vehicle_reservations 
        GROUP BY vehicle_name 
        ORDER BY reservation_count DESC 
        LIMIT 1";

$result = $connection->query($sql);

if ($result->num_rows > 0) {
    // Output data of the room with the most reservations
    while($row = $result->fetch_assoc()) {
        $most_reserved_vehicle = $row["vehicle_name"];
    }
} else {
    $most_reserved_vehicle = "No reservations found";
}


// Query to count total reservations
$sql_total_reservations = "SELECT COUNT(*) AS total_reservations FROM reservations";
$result_total_reservations = $connection->query($sql_total_reservations);

if ($result_total_reservations->num_rows > 0) {
    // Output data of the total reservations
    while($row_total_reservations = $result_total_reservations->fetch_assoc()) {
        $total_reservations = $row_total_reservations["total_reservations"];
    }
} else {
    $total_reservations = 0;
}

// Query to count total vehicle reservations
$sql_total_vehicle_reservations = "SELECT COUNT(*) AS total_vehicle_reservations FROM vehicle_reservations";
$result_total_vehicle_reservations = $connection->query($sql_total_vehicle_reservations);

if ($result_total_vehicle_reservations->num_rows > 0) {
    // Output data of the total vehicle reservations
    while($row_total_vehicle_reservations = $result_total_vehicle_reservations->fetch_assoc()) {
        $total_vehicle_reservations = $row_total_vehicle_reservations["total_vehicle_reservations"];
    }
} else {
    $total_vehicle_reservations = 0;
}






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
if ($totalCount != 0) {
    $cancellationRate = ($canceledCount / $totalCount) * 100;
} else {
    // Handle the case where $totalCount is zero
    // For example, set $cancellationRate to 0 or display an error message.
    $cancellationRate = 0;
    // Or you can display an error message
    // echo "Total count is zero, cannot calculate cancellation rate.";
}


?>






<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>NULR Admin | Dashboard MK4</title>

   <!-- Custom fonts for this template-->
   <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" >

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/UI_Pages.css">

    <!-- Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include 'navitems.php'; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white topbar mb-4 static-top shadow container-fluid">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto w-100 justify-content-end container-fluid">

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                     <!-- Content Row -->
                    <div class="row">

                        <!-- Total Reservation Card -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2 bg-gradient-primary">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" style="color: white; text-shadow: 1px 1px 5px #000000;">
                                                Total Room Reservations Nga
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-white" style="color: white; text-shadow: 1px 1px 2px #000000;">
                                            <?php echo $total_reservations; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Most Reserved Card -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2 bg-gradient-success">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1" style="color: white; text-shadow: 1px 1px 5px #000000;">
                                                Most Reserved Room
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-white" style="color: white; text-shadow: 1px 1px 2px #000000;">
                                            <?php echo $most_reserved_room; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-door-open fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                      
                        <!-- Pending Approvals Card -->
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2 bg-gradient-warning">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="color: white; text-shadow: 1px 1px 5px #000000;">
                                            Pending Room Reservations
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-white" style="color: white; text-shadow: 1px 1px 2px #000000;">
                                            <?php echo $pending_reservations; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                     <!-- Content Row -->
                     <div class="row">

<!-- Total Reservation Card -->
<div class="col-xl-4 col-md-6 mb-4">
    <div class="card border-left-primary shadow h-100 py-2 bg-gradient-primary">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1" style="color: white; text-shadow: 1px 1px 5px #000000;">
                        Total Vehicle Reservations
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-white" style="color: white; text-shadow: 1px 1px 2px #000000;">
                    <?php echo $total_vehicle_reservations;?>
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Most Reserved Card -->
<div class="col-xl-4 col-md-6 mb-4">
    <div class="card border-left-success shadow h-100 py-2 bg-gradient-success">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1" style="color: white; text-shadow: 1px 1px 5px #000000;">
                        Most Reserved Vehicle
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-white" style="color: white; text-shadow: 1px 1px 2px #000000;">
                    <?php echo $most_reserved_vehicle;?>
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-car fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Pending Approvals Card -->
<div class="col-xl-4 col-md-6 mb-4">
    <div class="card border-left-warning shadow h-100 py-2 bg-gradient-warning">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1" style="color: white; text-shadow: 1px 1px 5px #000000;">
                    Pending Vehicle Reservations
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-white" style="color: white; text-shadow: 1px 1px 2px #000000;">
                    <?php echo $pending_vehicle_reservations;?>
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

                        
                                <!--Analytics Section -->
                        <h2 class="grid-header text-center" id="header">Analytics</h2>

                        <hr> <!-- Add line divider -->

                        <div class="row mt-4">
                            <div class="col-6 text-center">
                                <div class="card custom-card shadow">
                                    <div class="card-body">
                                        <h3 class="chart-title">Most Booked Rooms</h3>
                                        <canvas id="bookedroomsGraph"></canvas>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 text-center">
                                <div class="card custom-card shadow">
                                    <div class="card-body">
                                        <h3 class="chart-title">Reservations Per Day</h3>
                                        <canvas id="reservationsPerDayChart"></canvas>
                                    </div>
                                </div>
                            </div>

                           

                        <div class="row mt-3 mb-3"><!-- Add space for the cards -->
                            <div class="col-6 text-center">
                                <div class="card custom-card shadow text-muted" style="background: linear-gradient(to bottom, #8B0000, #FF0000);">
                                    <div class="card-body">
                                        <h3 class="chart-title" style="color: white;">Cancellation Rate: <?php echo number_format($cancellationRate, 2); ?>%</h3>
                                    </div>
                                </div>
                            </div>
                        </div>



                       

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
    <script>
                // Use JavaScript to render the bar graph for most booked rooms
                var roomData = <?php echo $room_json; ?>;
                var ctx1 = document.getElementById('bookedroomsGraph').getContext('2d');
                var myBarChart = new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: roomData.map(item => item.room_name),
                        datasets: [{
                            data: roomData.map(item => item.count),
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.8)',
                                'rgba(54, 162, 235, 0.8)',
                                'rgba(255, 206, 86, 0.8)',
                                'rgba(75, 192, 192, 0.8)',
                                'rgba(153, 102, 255, 0.8)',
                            ],
                        }]
                    },
                });

                // Convert JSON data to JavaScript object
                var reservationsPerDayData = <?php echo $reservations_per_day_json; ?>;
                // Prepare data for the chart
                var labels = [];
                reservationsPerDayData.forEach(function(item) {
                    var reservationDay = item.reservation_day;
                    if (!labels.includes(reservationDay)) {
                        labels.push(reservationDay);
                    }
                });
                var data = Array(labels.length).fill(0);
                reservationsPerDayData.forEach(function(item) {
                    var reservationDay = item.reservation_day;
                    var index = labels.indexOf(reservationDay);
                    data[index]++;
                });

                // Render the chart
                var ctx = document.getElementById('reservationsPerDayChart').getContext('2d');
                var chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Reservations per Day',
                            data: data,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });

            // Retrieve peak time reservations data from PHP
            var peakTimeData = <?php echo $peak_time_json; ?>;
                
                // Create labels and data arrays for the chart
                var labels = peakTimeData.map(function(slot) {
                    return slot.split('-')[0]; // Get the start time of each time slot
                });

                var data = peakTimeData.map(function(slot) {
                    return Math.floor(Math.random() * 10); // Placeholder for the number of reservations
                });

                // Create the line chart
                var ctx = document.getElementById('peakTimeChart').getContext('2d');
                var peakTimeChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Peak Time Reservations',
                            data: data,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                });
                

       
    </script>
    
 



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
    <script src="js/demo/chart-bar-demo.js"></script>

</body>

</html>