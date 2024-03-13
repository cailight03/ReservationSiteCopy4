<?php 
include '../config/connection.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: ../login.php');
    exit();
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

    <title>NULR Admin | Approved Room Reservations</title>

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

        <!-- Sidebar -->
        <?php include 'navitems.php'; ?>
        <!-- End of Sidebar -->

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
                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                        <h2 class="grid-header" id="header">Past Room Reservations</h2>
                        

                        <!-- Table to display reservations -->
                        <table id="approvedreservationsTable" class="table table-hover table-bordered table-responsive">
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
                                <th>Number of Attendees</th>
                                <th>Items Needed</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch data from the database and populate the table rows
                                $query = "SELECT * FROM reservationdb.previous_reservations";
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
                                echo "<td>{$row['num_of_attendees']}</td>";

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
                                   
                                    for ($i = 1; $i <= 6; $i++) {
                                        $signature = "Sig$i";
                                        if (!empty($row[$signature])) {
                                            echo "<td>{$row[$signature]}</td>";
                                        }
                                    }
                                    echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                        <button class="btn btn-primary" id="addButton">Add</button>
                <!-- /.container-fluid -->

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
                // Use JavaScript to render the pie chart for room reservation
                var roomData = <?php echo $room_json; ?>;
                var ctx1 = document.getElementById('departmentChart').getContext('2d');
                var myPieChart = new Chart(ctx1, {
                    type: 'pie',
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

    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>`

    <script>
    $(document).ready(function() {
         // Initialize DataTables
         $('#approvedreservationsTable').DataTable({
            "paging": true, // Enable pagination
             "searching": true // Enable search functionality
            
      });
   });
</script>
</body>

</html>