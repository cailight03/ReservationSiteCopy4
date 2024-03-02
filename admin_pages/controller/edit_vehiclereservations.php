<?php
include '../../config/connection.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $vehicle_reservationId = $_GET['id'];
    
    // Fetch the reservation data from the database
    $query = "SELECT * FROM reservationdb.vehicle_reservations WHERE id = $vehicle_reservationId"; // Assuming 'id' is the reservation ID column
    $result = mysqli_query($connection, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        // Display a form for editing the reservation
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Edit Vehicle Reservation</title>
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
            <h2>Edit Vehicle Reservation</h2>
            <!-- Add your form elements and populate them with $row data -->
            <form method="post" action="update_vehiclereservations.php?id=<?php echo $vehicle_reservationId; ?>">
			
		<label for="Vehicle ID">Vehicle ID:</label>
        <input type="text" name="vehicle_id" id="vehicle id" value="<?php echo $row['id']; ?>" placeholder="Vehicle ID">

        <label for="Requestor">Requestor:</label>
        <input type="text" name="fullName" id="fullName" value="<?php echo $row['fullName']; ?>" placeholder="Requestor">

        <label for="office">Office:</label>
        <input type="text" name="office" id="office" value="<?php echo $row['office']; ?>" placeholder="office">

        <label for="purpose">Purpose:</label>
        <input type="text" name="purpose" id="purpose" value="<?php echo $row['purpose']; ?>" placeholder="purpose">

        <label for="num_of_passengers">Num of Passengers:</label>
        <input type="text" name="num_of_passengers" id="num_of_passengers" value="<?php echo $row['num_of_passengers']; ?>" placeholder="num_of_passengers">

        <label for="date">Date:</label>
        <input type="text" name="date" id="date" value="<?php echo $row['date']; ?>" placeholder="date">

        <label for="room_name">Time:</label>
        <input type="text" name="time" id="time" value="<?php echo $row['time']; ?>" placeholder="time">

        <label for="items_needed">Destination:</label>
        <input type="text" name="destination" id="destination" value="<?php echo $row['destination']; ?>" placeholder="destination">

        <label for="remarks">Vehicle Name:</label>
        <input type="text" name="vehicle_name" id="vehicle_name" value="<?php echo $row['vehicle_name']; ?>" placeholder="vehicle_name">

        <label for="status">Remarks:</label>
        <input type="text" name="admin_remarks" id="admin_remarks" value="<?php echo $row['admin_remarks']; ?>" placeholder="admin_remarks">

        <label for="num_of_attendees">Status:</label>
        <input type="text" name="status" id="status" value="<?php echo $row['status']; ?>" placeholder="status">

        <button type="submit" class="btn btn-primary">Update Vehicle Reservation</button>
            </form>
        </section>

        <!-- Add your script or script links here -->

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
?>
