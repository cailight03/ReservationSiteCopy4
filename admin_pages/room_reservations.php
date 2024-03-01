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
    <title>Room Reservations</title>
    <link rel="stylesheet" href="../css/UI_Pages.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <style>
        .action-button {
            width: 100px; /* Set the width of the buttons */
        }
    </style>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.6/css/dataTables.bootstrap5.min.css">
</head>
<body>

<!-- Include the navbar -->
<?php include 'navbarV2.php'; ?>

<section class="container">
    <h2 class="grid-header" id="header">Pending Reservations</h2>

    <!-- Table to display reservations -->
    <table id="roomreservationsTable" class="table table-hover table-bordered">
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
</section>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.6/js/dataTables.bootstrap5.min.js"></script>

<script>
     $(document).ready(function() {
        $('#roomreservationsTable').DataTable({
            'paging': true, // Enable pagination
            'pageLength': 10, // Set the number of records per page to 10
            'searching': true, // Enable instant search
            'ordering': true // Enable sorting
        });
    });
</script>


</body>
</html>
