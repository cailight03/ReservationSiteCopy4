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

    <title>NULR Admin | Approved Vehicle Reservations</title>

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
                <h2 class="grid-header" id="header">Approved Vehicle Reservations</h2>

<!-- Table to display reservations -->
<table id="vehicleresTable" class="table table-hover table-bordered table-responsive">
    <thead>
        <tr>
            <th>Room ID</th>
            <th>Date Submitted</th>
            <th>Department</th>
            <th>Requestor</th>
            <th>Vehicle</th>
            <th>Purpose</th>
            <th>Destination</th>
            <th>Number of Passengers</th>
            <th>Date Requested</th>
            <th>Time</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Fetch data from the database and populate the table rows
            $query = "SELECT * FROM reservationdb.vehicle_reservations WHERE status='Approved'";
            $result = mysqli_query($connection, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['date_submitted']}</td>";
                echo "<td>{$row['office']}</td>";
                echo "<td>{$row['fullName']}</td>";
                echo "<td>{$row['vehicle_name']}</td>";
                echo "<td>{$row['purpose']}</td>";
                echo "<td>{$row['destination']}</td>";
                echo "<td>{$row['num_of_passengers']}</td>";
                echo "<td>{$row['date']}</td>";
                echo "<td>{$row['time']}</td>";
                echo "<td>{$row['status']}</td>";
                // echo "<td>";
                // echo "<a href='controller/edit_vehiclereservations.php?id={$row['id']}' </a>";
                // echo "<a href='approve_roomreservations.php?id={$row['id']}' </a>";
                // echo "<a href='decline_roomreservations.php?id={$row['id']}' </a>";
                // echo "</td>";
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
    // Initialize DataTables with pagination and search enabled
    $('#vehicleresTable').DataTable({
        "paging": true,
        "searching": true    });
});
</script>
