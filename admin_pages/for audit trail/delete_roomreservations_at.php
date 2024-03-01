<?php
$host = 'localhost'; // replace with your host name
$user = 'root'; // replace with your MySQL username
$password = ''; // replace with your MySQL password
$db = 'reservationdb'; // replace with your database name

// Create connection
$connection = mysqli_connect($host, $user, $password, $db);

// Check connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get reservation details before deletion
$query = "SELECT * FROM reservations WHERE id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
if (!mysqli_stmt_execute($stmt)) {
    // Redirect back to reservations page with an error message
    header("Location: /reservations_page.php?error=Failed to get reservation details");
    exit();
}
$result = mysqli_stmt_get_result($stmt);
$reservation = mysqli_fetch_assoc($result);

// Delete from reservations
$query = "DELETE FROM reservations WHERE id = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
if (!mysqli_stmt_execute($stmt)) {
    // Redirect back to reservations page with an error message
    header("Location: /reservations_page.php?error=Failed to delete reservation");
    exit();
}

// Insert into audit_logs
$query = "INSERT INTO audit_logs (action, details) VALUES ('delete', ?)";
$stmt = mysqli_prepare($connection, $query);
$details = "Deleted reservation with id $id for user {$reservation['user_id']} at {$reservation['reservation_time']}";
mysqli_stmt_bind_param($stmt, 's', $details); // 's' indicates the type of the parameter, a string
if (!mysqli_stmt_execute($stmt)) {
    // Redirect back to reservations page with an error message
    header("Location: /reservations_page.php?error=Failed to log deletion");
    exit();
}

// Commit transaction
mysqli_commit($connection);

// Redirect back to reservations page with a success message
header("Location: /reservations_page.php?success=Reservation deleted successfully");
exit();
?>