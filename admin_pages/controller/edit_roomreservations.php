<?php
include '../../config/connection.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $reservationId = $_GET['id'];
    
    // Fetch the reservation data from the database
    $query = "SELECT * FROM reservationdb.reservations WHERE id = $reservationId"; // Assuming 'id' is the reservation ID column
    $result = mysqli_query($connection, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        // Display a form for editing the reservation
        ?>
    
       <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room Reservation</title>
    <style>
        /* CSS styles for input fields */
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        /* CSS styles for submit button */
        .btn-primary {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        /* CSS styles for labels */
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<?php include 'navbarV2.php'; ?>

<section class="container"> 
    <h2>Edit Room Reservation</h2>
    <form method="post" action="update_roomreservations.php?id=<?php echo $reservationId; ?>">
        <!-- Add form elements for editing reservation data -->
        

       
        <label for="date">Date:</label>
        <input type="text" name="date" id="date" value="<?php echo $row['date']; ?>" placeholder="Date">

        <label for="time_slot">Time Slot:</label>
        <input type="text" name="time_slot" id="time_slot" value="<?php echo $row['time_slot']; ?>" placeholder="Time Slot">

      
        

        

        <button type="submit" class="btn btn-primary">Update Reservation</button>
    </form>
</section>

</body>
</html>


        <?php
    } else {
        // Handle error: Reservation not found
        echo "Reservation not found.";
    }
} else {
    // Handle error: Invalid request
    echo "Invalid request.";
}

// Insert log entry for the edit action after form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reservation_id'])) {
    $reservationId = $_POST['reservation_id'];
    $userId = $_SESSION['user_id'];
    $action = "edit";
    $log_query = "INSERT INTO audit_logs (user_id, reservation_id, action) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($connection, $log_query);
    mysqli_stmt_bind_param($stmt, 'iss', $userId, $reservationId, $action);
    mysqli_stmt_execute($stmt);
}
?>
