<?php
// view_reservation.php

include '../config/connection.php';

// Check if the reservation ID is provided in the URL
if (isset($_GET['reservationId'])) {
    $reservationId = $_GET['reservationId'];
   
    
    // Check if the cancellation form is submitted
    if (isset($_POST['cancelReservation'])) {
        // Update the reservation status to "Cancelled" in the database
        $updateQuery = "UPDATE reservations SET status = 'Cancelled' WHERE id = ?";
        $updateStmt = $connection->prepare($updateQuery);
        $updateStmt->bind_param('i', $reservationId);
        $updateStmt->execute();
        $updateStmt->close();

        $cancellationSuccess = true;
}  
    // Retrieve reservation details from the database
    $query = "SELECT * FROM reservations WHERE id = ?";

    $stmt = $connection->prepare($query);
    if (!$stmt) {
        die('Error in preparing the query: ' . $connection->error);
    } else {
        $stmt->bind_param('i', $reservationId);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result) {
            die('Error in executing the query: ' . $stmt->error);
        }
    }

    // Check if the reservation ID exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Reservation not found.";
        $stmt->close();
        $connection->close();
        exit();
    }

    $stmt->close();
    $connection->close();
} else {
    echo "Reservation ID not provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        p {
            margin-bottom: 10px;
        }

        .buttons {
            margin-top: 20px;
            text-align: right;
            /* Align buttons to the right */
        }

        .buttons a {
            margin-right: 10px;
            text-decoration: none;
        }

        .reschedule {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        .cancel {
            background-color: #e74c3c;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        .success-modal {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Reservation Details</h1>
        <p><strong>Reservation ID:</strong> <?php echo $row['id']; ?></p>
        <!-- ... (other details) ... -->
        <p><strong>Status:</strong> <?php echo $row['status']; ?></p>
        <p><strong>Room ID:</strong> <?php echo $row['room_id']; ?></p>
        <p><strong>Room Name:</strong> <?php echo $row['room_name']; ?></p>
        <p><strong>Full Name:</strong> <?php echo $row['requestor']; ?></p>
        <p><strong>Organization/College:</strong> <?php echo $row['department']; ?></p>
        <p><strong>Activity Name:</strong> <?php echo $row['activity_name']; ?></p>
        <p><strong>No. of Attendees:</strong> <?php echo $row['num_of_attendees']; ?></p>
        <p><strong>Date:</strong> <?php echo $row['date']; ?></p>
        <p><strong>Time Slot:</strong> <?php echo $row['time_slot']; ?></p>
        <p><strong>Remarks:</strong> <?php echo $row['remarks']; ?></p>
        
        <?php if ($row['status'] === 'Rejected'): ?>
            <p><strong>Admin Remarks:</strong> <?php echo $row['admin_remarks']; ?></p>
        <?php endif; ?>

        <?php if ($row['status'] === 'Approved'): ?>
            <p><strong>Admin Remarks:</strong> <?php echo $row['admin_remarks']; ?></p>
        <?php endif; ?>
       

        <div class="buttons">
            <?php if ($row['status'] !== 'Cancelled'): ?>
                <?php if ($row['status'] === 'pending'): ?>
                    <button class="btn btn-primary reschedule" disabled>Reschedule</button>
                <?php else: ?>
                    <a href="#" class="btn btn-primary reschedule" data-bs-toggle="modal" data-bs-target="#rescheduleModal<?php echo $row['id']; ?>">Reschedule</a>
                <?php endif; ?>
                <button class="btn btn-danger cancel" data-bs-toggle="modal" data-bs-target="#cancelConfirmationModal">Cancel</button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div class="modal fade" id="cancelConfirmationModal" tabindex="-1" aria-labelledby="cancelConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelConfirmationModalLabel">Cancel Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to cancel your reservation?
                </div>
                <div class="modal-footer">
                    <form method="post">
                        <button type="submit" name="cancelReservation" class="btn btn-danger">Yes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reschedule Modal -->
    <div class="modal fade" id="rescheduleModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="rescheduleModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rescheduleModalLabel<?php echo $row['id']; ?>">Reschedule Reservation - <?php echo $row['id']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Add date, start time, and end time inputs here -->
                    <form method="post" action="reservation_manager/reschedule_reservation.php">
                        <div class="mb-3">
                            <label for="newDate" class="form-label">Date:</label>
                            <input type="date" class="form-control" name="newDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="newStartTime" class="form-label">Start Time:</label>
                            <input type="time" class="form-control" name="newStartTime" required>
                        </div>
                        <div class="mb-3">
                            <label for="newEndTime" class="form-label">End Time:</label>
                            <input type="time" class="form-control" name="newEndTime" required>
                        </div>
                        <input type="hidden" name="reservationId" value="<?php echo $row['id']; ?>">
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Reschedule</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade success-modal" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Cancelled</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Reservation cancelled successfully!
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($cancellationSuccess) && $cancellationSuccess): ?>
        <script>
            $(document).ready(function () {
                $('#successModal').modal('show');
            });
        </script>
    <?php endif; ?>
</body>

</html>