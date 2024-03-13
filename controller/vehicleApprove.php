
<?php

include '../config/connection.php';

session_start();


$reservationId = isset($_POST['reservationId']) ? $_POST['reservationId'] : '';
$updateQuery = "UPDATE vehicle_reservations SET status = 'Approved' WHERE id = ?";
$stmt = $connection->prepare($updateQuery);
$stmt->bind_param("i", $reservationId); // Assuming reservation_id is an integer
$stmt->execute();

// Check if the update was successful
if ($stmt->affected_rows > 0) {
    echo "Status updated to Approved successfully.";
}else{
    echo"updated na";
}
?>

