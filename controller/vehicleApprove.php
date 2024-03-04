<?php

include '../config/connection.php';

session_start();

$recipientName = $_POST["recipientName"];


$reservationId = isset($_POST['reservationId']) ? $_POST['reservationId'] : '';
$updateQuery = "UPDATE vehicle_reservations SET status = 'Approved' WHERE id = ?";
$stmt = $connection->prepare($updateQuery);
$stmt->bind_param("i", $reservationId); // Assuming reservation_id is an integer
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Update was successful, proceed with the second update
    $updateQuery = "UPDATE vehicle_reservations SET Sig2 = ?, Act2 = 'Approved', time2 = NOW() WHERE id = ?";
    $stmt = $connection->prepare($updateQuery);
    $stmt->bind_param("si", $recipientName, $reservationId);

    if ($stmt->execute()) {
        // Handle successful update
        echo "Status updated to Approved successfully.";
    } else {
        echo "Error: " . $updateQuery . "<br>" . $connection->error;
    }
} else {
    // Handle case where the first update did not affect any rows
    echo "No rows updated.";
}

// Close the statement
$stmt->close();
?>
