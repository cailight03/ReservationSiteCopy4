<?php
include '../config/connection.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $reservationId = $_GET['id'];
    
    // Fetch the reservation data from the database
    $query = "SELECT * FROM reservationdb.reservations WHERE reservation_id = $reservationId";
    $result = mysqli_query($connection, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        // Display a form for editing the reservation
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Edit Reservation</title>
            <!-- Add your CSS stylesheets or links here -->
        </head>
        <body>


        <section class="container">
            <h2>Edit Reservation</h2>
            <!-- Add your form elements and populate them with $row data -->
            <form method="post" action="update_reservation.php">
                <!-- Add form elements for editing reservation data -->
                <input type="hidden" name="reservation_id" value="<?php echo $row['reservation_id']; ?>">
                <button type="submit" class="btn btn-primary">Update Reservation</button>
            </form>
        </section>

        <!-- Add your script or script links here -->

        </body>
        </html>
        <?php

    // Insert log entry for the edit action ---------------------> added for logs
    $userId = $_SESSION['user_id'];
    $action = "edit";
    $log_query = "INSERT INTO audit_logs (user_id, reservation_id, action) VALUES ('$userId', '$reservationId', '$action')";
    mysqli_query($connection, $log_query);    

    } else {
        // Handle error: Reservation not found
        echo "Reservation not found.";
    }
} else {
    // Handle error: Invalid request
    echo "Invalid request.";
}
?>
