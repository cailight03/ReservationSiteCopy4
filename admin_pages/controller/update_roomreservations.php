<?php
include '../../config/connection.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reservation_id'])) {
    $reservationId = $_POST['reservation_id'];
    // Collect data from the form and sanitize it
    $dateSubmitted = mysqli_real_escape_string($connection, $_POST['date_submitted']);
    $department = mysqli_real_escape_string($connection, $_POST['department']);
    $requestor = mysqli_real_escape_string($connection, $_POST['requestor']);
    $activityName = mysqli_real_escape_string($connection, $_POST['activity_name']);
    $date = mysqli_real_escape_string($connection, $_POST['date']);
    $timeSlot = mysqli_real_escape_string($connection, $_POST['time_slot']);
    $roomName = mysqli_real_escape_string($connection, $_POST['room_name']);
    $itemsNeeded = mysqli_real_escape_string($connection, $_POST['items_needed']);
    $remarks = mysqli_real_escape_string($connection, $_POST['remarks']);
    $status = mysqli_real_escape_string($connection, $_POST['status']);
    $numOfAttendees = mysqli_real_escape_string($connection, $_POST['num_of_attendees']);

    // Update the reservation using prepared statement to prevent SQL injection
    $query = "UPDATE reservationdb.reservations SET date_submitted = ?, department = ?, requestor = ?, activity_name = ?, date = ?, time_slot = ?, room_name = ?, items_needed = ?, remarks = ?, status = ?, num_of_attendees = ? WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'sssssssssssi', $dateSubmitted, $department, $requestor, $activityName, $date, $timeSlot, $roomName, $itemsNeeded, $remarks, $status, $numOfAttendees, $reservationId);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // Reservation updated successfully, redirect to some page or display a success message
        header('Location: room_reservations.php');
        exit();
    } else {
        // Handle error: Reservation update failed
        echo "Reservation update failed.";
    }
} else {
    // Handle error: Invalid request
    echo "Invalid request.";
}
?>