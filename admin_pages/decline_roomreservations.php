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
    $query = "SELECT * FROM reservationdb.reservations WHERE id = $reservationId";
    $result = mysqli_query($connection, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        // Insert the reservation data into the Deleted_reservations table
        $insertQuery = "INSERT INTO reservationdb.declined_reservations SELECT *, NOW() FROM reservationdb.reservations WHERE id = $reservationId";
        $insertResult = mysqli_query($connection, $insertQuery);

        if ($insertResult) {
            // Delete the reservation from the reservations table
            $deleteQuery = "DELETE FROM reservationdb.reservations WHERE id = $reservationId";
            $deleteResult = mysqli_query($connection, $deleteQuery);

            if ($deleteResult) {
                // Log the event in the audit_logs table
                $logQuery = "INSERT INTO reservationdb.audit_logs (user_id, event, event_time) VALUES ('".$_SESSION['user_id']."', 'Reservation declined', NOW())";
                mysqli_query($connection, $logQuery);

                // Display a pop-up message
                echo '<script>alert("Reservation request declined.");</script>';
                // Redirect back to the reservations page after deletion
                header('Location: room_reservations.php');
                exit();
            } else {
                // Handle error: Deletion failed
                echo "Deletion failed.";
            }
        } else {
            // Handle error: Insertion failed
            echo "Insertion into declined_reservations table failed.";
        }
    } else {
        // Handle error: Reservation not found
        echo "Reservation not found.";
    }
} else {
    // Handle error: Invalid request
    echo "Invalid request.";
}
?>