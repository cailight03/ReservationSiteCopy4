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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Reservations</title>
    <link rel="stylesheet" href="../css/UI_Pages.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.6/css/dataTables.bootstrap5.min.css">
    <style>
        .action-button {
            width: 100px; /* Set the width of the buttons */
        }
    </style>
</head>
<body>

<!-- Include the navbar -->
<?php include 'navbarV2.php'; ?>

<section class="container">
    <h2 class="grid-header" id="header">Vehicle Reservations</h2>

    <!-- Table to display reservations -->
    <table id="vehicleReservationsTable" class="table table-hover table-bordered">
        <thead>
            <tr>
				<th>Room ID</th>
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
                $query = "SELECT * FROM reservationdb.reservations";
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
                    echo "<td>{$row['items_needed']}</td>";
                    echo "<td>{$row['remarks']}</td>";
                    echo "<td>{$row['status']}</td>";
					echo "<td>{$row['num_of_attendees']}</td>";
                    echo "<td>";
					echo "<a href='controller/edit_vehiclereservations.php?id={$row['id']}' class='btn btn-warning action-button'></i> Edit</a>";
                    echo "<a href='approve_roomreservations.php?id={$row['id']}' class='btn btn-primary action-button'></i> Approve</a>";
					echo "<a href='decline_roomreservations.php?id={$row['id']}' class='btn btn-danger action-button'></i> Decline</a>";
					echo "</td>";
					echo "</tr>";
                }
            ?>
        </tbody>
    </table>
	<button class="btn btn-primary" id="addButton">Add</button>
	
</section>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#vehicleReservationsTable').DataTable();
    });
</script>
</body>
</html>