<?php
// view_reservation.php

include '../config/connection.php';

// Check if the reservation ID is provided in the URL
if (isset($_GET['reservationId'])) {
    $reservationId = $_GET['reservationId'];
   
    
    // Check if the cancellation form is submitted
    if (isset($_POST['cancelReservation'])) {
        // Update the reservation status to "Cancelled" in the database
        $updateQuery = "UPDATE vehicle_reservations SET status = 'Cancelled' WHERE id = ?";
        $updateStmt = $connection->prepare($updateQuery);
        $updateStmt->bind_param('i', $reservationId);
        $updateStmt->execute();
        $updateStmt->close();

        $cancellationSuccess = true;
}  

    // Retrieve reservation details from the database
    $query = "SELECT * FROM vehicle_reservations WHERE id = ?";

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
            padding: 30px;
            
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

        .btn {
            margin-right: 10px;
            text-decoration: none;
        }

      

        .success-modal {
            display: none;
        }
        .required {
        color: red;
        margin-left: 3px;
    }

    .custom-select {
    height: 17rem;
    min-height: 3rem; /* Adjust this value as needed */
}

.custom-select option:disabled {
    color: red;
}

.navbar{
  
  margin-bottom: 2.5rem;
  max-width: 1320px;
}

.navbar-brand{
    font-size: 1rem;
    font-weight: 600;
}
.navbar-brand img{
 width: 50px;
 padding-right: 10px;
}

@media print{

    .hidden{
        display: none;
    }
    body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
    .container {
        max-width: 800px;
        
        background-color: #fff;
        padding: 30px;
        
    }
}
    </style>
</head>

<body>
    
    <div class="container">
        
    
   
    <nav class="navbar col-6">
        <div >
            <a class="navbar-brand" href="vehicle_reservation_copy.php" >
            <img src="../img/navbar_img/National_University_seal.png" alt="Logo" class="d-inline-block align-text-center" style="width:50px;">
         NU Laguna Reservation
            
            </a>
        </div>
    </nav>

    

   

        <h2>Reservation #<?php echo $row['id']; ?></h1>
        <div class="row">
            <div class="col-6">
            <p><strong>Date Submitted:</strong> <?php echo $row['date_submitted']; ?></p>
                <p><strong>Status:</strong> <?php echo $row['status']; ?></p>
               
            </div>
            
        </div>
        
        
        <div class="row pt-5">
        
            <div class="col-6 "><h3>Requestor Details</h3><p><strong>Full Name:</strong> <?php echo $row['fullName']; ?></p>
        <p><strong>Office:</strong> <?php echo $row['office']; ?></p></div>
            <div class="col-6"><h3>Form Details</h3>
            <p><strong>Destination:</strong> <?php echo $row['destination']; ?></p>
            <p><strong>Date Needed:</strong> <?php echo $row['date']; ?></p>
        <p><strong>Time Needed:</strong> <?php echo $row['time']; ?></p>
        
        <p><strong>Vehicle Name:</strong> <?php echo $row['vehicle_name']; ?></p>
        
        <p><strong>Purpose:</strong> <?php echo $row['purpose']; ?></p>
        <p><strong>No. of Passengers:</strong> <?php echo $row['num_of_passengers']; ?></p>
        </div>
        </div>

        <div class="row hidden" >
            <div class="col-6">
            <h3>ID Photo:</h3>
            
            <?php  if (!empty($row['uploadFilePath'])) {
        echo "<img src='".$row['uploadFilePath']."' alt='Uploaded Photo' style='max-width:100%; height: 400px;'>";
    } else {
        echo "<p>No uploaded photo.</p>";
    } ?>
            </div>
            </div>
        
       

    <?php

echo "<div class='mt-3 hidden'>
        <p><strong>Signatory 1: </strong>NU Laguna Reservation: ". $row['Act1'] ." ".$row['time1']."</p>
        <p><strong>Signatory 2: </strong>Senior Administration Director: ".$row['Act2'] ." ".$row['time2'] ."</p>
      </div>";
?>

<?php if ($row['status'] === 'Rejected'): ?>
            <p><strong>Admin Remarks:</strong> <?php echo $row['admin_remarks']; ?></p>
        <?php endif; ?>

        <?php if ($row['status'] === 'Approved'): ?>
            <p><strong>Admin Remarks:</strong> <?php echo $row['admin_remarks']; ?></p>
        <?php endif; ?>

        <?php if ($row['status'] === 'Approved'): ?>

<div class="row justify-content-end mt-5">
    <div class="col-3 me-2 text-center">
<p class=" border border-0 text-bg-secondary  px-4 ">ENDORSED BY</p>
<div class=" ">
    <img src="../img/signature_img/signature.jpg" alt="" style='max-width: 100%; height: 50px;'>
    <h6><strong>Mary Minette D. Robediso</strong></h6>
<p><small>NU Laguna Reservation</small></p>
</div>
    </div>
    <div class="col-3 me-2 text-center">
<p class=" border border-0 text-bg-secondary  px-4 ">APPROVED BY</p>
<div class=" ">
    <img src="../img/signature_img/sig2.png" alt="" style='max-width: 100%; height: 50px;'>
    <h6><strong>Jose Ricardo S.A. Ocampo</strong></h6>
    <p><small>Senior Administration Director</small></p>
</div>
    </div>
    
</div>
<?php endif;?>

 


        <div class="buttons hidden">
        <?php if ($row['status'] !== 'Cancelled'): ?>
            <?php if ($row['status'] === 'Pending' && $row['date'] !== date('Y-m-d')): ?>
                <button class="btn btn-primary reschedule" disabled>Reschedule</button>
            <?php elseif ($row['date'] !== date('Y-m-d')): ?>
                <a href="#" class="btn btn-primary reschedule" data-bs-toggle="modal" data-bs-target="#rescheduleModal<?php echo $row['id']; ?>">Reschedule</a>
            <?php endif; ?>
            <?php if ($row['date'] !== date('Y-m-d')): ?>
                <button class="btn btn-danger cancel" data-bs-toggle="modal" data-bs-target="#cancelConfirmationModal">Cancel</button>
            <?php endif; ?>
        <?php endif; ?>
        <button class="btn btn-primary print" onclick="window.print();">
    Print</button>
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
                    <form method="post" action="reschedule_vehicle_reservation.php">

    <div class="mb-3">
        <label for="datepicker" class="form-label">Date(s) <span class="required">*</span></label>
        <input class="form-control" type="text" id="datepicker" name="reservation-date" required>
        
    </div>


    <!-- time slot -->
   
    <div class="mb-3">
      <label for="timeSlot">Select Time:</label>
      <select id="timeSlot" class="form-control custom-select" multiple  required > </select>
      <small class="text-secondary">Click for one time slot. Click and drag for multiple time slots.</small>
      <br>
      <label for="selectedTime">Selected Time Range: <span class="required">*</span></label>
      <input type="text" id="selectedTime" class="form-control" readonly name="time-slot" required>

     
        <input type="hidden" name="vehicle_name" value="<?php echo $row['vehicle_name']; ?>">
        <input type="hidden" name="vehicle_id" value="<?php echo $row['vehicle_id']; ?>">
        <input type="hidden" name="fullName" value="<?php echo $row['fullName']; ?>">
       
        <input type="hidden" name="office" value="<?php echo $row['office']; ?>">
        <input type="hidden" name="purpose" value="<?php echo $row['purpose']; ?>">
        <input type="hidden" name="destination" value="<?php echo $row['destination']; ?>">
        <input type="hidden" name="email" value="<?php echo $userEmail; ?>">
        <input type="hidden" name="numOfPassengers" value="<?php echo $row['num_of_passengers']; ?>">
        <input type="hidden" name="date_submitted" value="<?php echo $row['date_submitted']; ?>">
        <input type="hidden" name="uploadFilePath" value="<?php echo $row['uploadFilePath']; ?>">
     
      
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

<script>
document.addEventListener("DOMContentLoaded", async function () {
    try {
        // Get the room_id from the PHP script
        const vehicleId = <?php echo $row['vehicle_id']; ?>;

        // Fetch booked dates for the specific room
        const bookedDates = await getBookedDates(vehicleId);

        // Log the booked dates to the console
        console.log('Booked Dates:', bookedDates);

        // Initialize flatpickr
        flatpickr("#datepicker", {
            mode: "single",
            dateFormat: "Y-m-d",
            minDate: new Date().fp_incr(4),
            disable: [
                {
                    from: "today",
                    to: "today",
                },
                // Include your booked dates
                ...bookedDates,
            ]
        });
    } catch (error) {
        console.error('Error fetching and initializing:', error);
    }
});
// Function to get booked dates from get_booked_dates.php
async function getBookedDates(vehicleId) {
    try {
        const response = await fetch(`controller/vehicle_booked_dates.php?vehicle_id=${vehicleId}`);
        if (!response.ok) {
            throw new Error('Failed to fetch booked dates');
        }
        const bookedDates = await response.json();
        return bookedDates;
    } catch (error) {
        console.error('Error fetching booked dates:', error);
        return [];
    }
}

    

document.addEventListener("DOMContentLoaded", function () {
    // Call the function to populate time slots on page load
    populateTimeSlots();
});

function populateTimeSlots() {
    const startHour = 7; // 7:00 AM
    const endHour = 21; // 9:00 PM
    const interval = 30; // 30 minutes interval

    const selectElement = document.getElementById('timeSlot');
    const selectedTimeInput = document.getElementById('selectedTime');
    const vehicleId = <?php echo isset($row['vehicle_id']) ? $row['vehicle_id'] : 'null'; ?>; // Pass the room ID from PHP
    const datePicker = document.getElementById('datepicker');

    selectElement.addEventListener('change', updateSelectedTime);

    function updateSelectedTime() {
        const selectedOptions = Array.from(selectElement.selectedOptions);
        const selectedRanges = selectedOptions.map(option => option.value);
        const combinedRange = combineTimeRanges(selectedRanges);

        selectedTimeInput.value = combinedRange;
    }

    function combineTimeRanges(ranges) {
        const startTime = ranges[0].split('-')[0];
        const endTime = ranges[ranges.length - 1].split('-')[1];

        return `${startTime}-${endTime}`;
    }

    // Fetch booked time slots only when a date is selected
    function getBookedTimeSlots() {
    const selectedDate = datePicker.value;

    if (selectedDate) {
        // Clear the selected options when the date changes
        selectElement.selectedIndex = -1;
        selectedTimeInput.value = '';

        fetch(`controller/vehicle_booked_time_slots.php?vehicle_id=${vehicleId}&date=${selectedDate}`)
            .then(response => response.json())
            .then(bookedTimeSlots => {
                console.log('Booked Time Slots:', bookedTimeSlots); // Print booked time slots to the console
                disableBookedTimeSlots(bookedTimeSlots);
            })
            .catch(error => console.error('Error fetching booked time slots:', error));
    }
}


    // Disable booked time slots in the select element
    function disableBookedTimeSlots(bookedTimeSlots) {
        const options = selectElement.options;

        for (let i = 0; i < options.length; i++) {
            const optionValue = options[i].value;

            // Disable the option if it is in the bookedTimeSlots array
            options[i].disabled = bookedTimeSlots.includes(optionValue);
        }
    }

    datePicker.addEventListener('change', getBookedTimeSlots);

    for (let hour = startHour; hour < endHour; hour++) {
        for (let minute = 0; minute < 60; minute += interval) {
            const formattedHour = hour % 12 || 12; // Convert to 12-hour format
            const ampm = hour < 12 ? 'AM' : 'PM';
            const nextHour = (hour + Math.floor((minute + interval) / 60)) % 12 || 12;
            const nextAmpm = (hour + Math.floor((minute + interval) / 60)) < 12 ? 'AM' : 'PM';
            const option = document.createElement('option');
            const timeSlot = `${formattedHour}:${String(minute).padStart(2, '0')} ${ampm}-${nextHour}:${String((minute + interval) % 60).padStart(2, '0')} ${nextAmpm}`;

            option.value = timeSlot;
            option.text = timeSlot;

            selectElement.add(option);
        }
    }
}
</script>
</body>



</html>