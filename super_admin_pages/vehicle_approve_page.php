<?php


// Include the file that establishes the database connection
include '../config/connection.php';

session_start();


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    date_default_timezone_set('Asia/Manila');

    $vehicleId = isset($_GET['vehicle_id']) ? $_GET['vehicle_id'] : '';
    $vehicleName = isset($_GET['vehicle_name']) ? $_GET['vehicle_name'] : '';
    $fullName = $_GET["fullName"];
    $userEmail = $_GET["userEmail"];
    $recipientName = $_GET["recipientName"];
    $recipientEmail = $_GET["recipientEmail"];
    $purpose = $_GET["purpose"];
    $office = $_GET["office"];
    $numOfPassengers = $_GET["numOfPassengers"];
    $date = $_GET["date"];
    $timeSlot = $_GET["timeSlot"];
    $destination = $_GET["destination"];
    $reservationId = isset($_GET['reservationId']) ? $_GET['reservationId'] : '';
    $submissionTime = isset($_GET['submissionTime']) ? $_GET['submissionTime'] : '';
    $uploadFilePath = isset($_GET['uploadFilePath']) ? $_GET['uploadFilePath'] : '';
   
}
// Define your SQL query to check if the recipient name exists in any of the sig columns for the given ID
$query = "SELECT COUNT(*) as count FROM vehicle_reservations WHERE id = ? AND (sig1 = ? OR sig2 = ?)";

// Prepare the query
$stmt = $connection->prepare($query);

$stmt->bind_param("iss", $reservationId, $recipientName, $recipientName);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Fetch the count of matching rows
$row = $result->fetch_assoc();

$statusQuery = "SELECT * FROM vehicle_reservations WHERE id = ?";

// Prepare the query
$statusStmt = $connection->prepare($statusQuery);

// Bind parameters
$statusStmt->bind_param("i", $reservationId);

// Execute the query
$statusStmt->execute();

// Get the result
$statusResult = $statusStmt->get_result();

// Fetch the result
$statusRow = $statusResult->fetch_assoc();

// Check if any rows were found with the recipientName in any of the sig columns
if ($row['count'] > 0) {
    // Recipient name exists in the database
    // Print the message
    echo 'You have already approved this reservation.';
} elseif ($statusRow['status'] === 'Cancelled') {
    echo 'This reservation has been cancelled by the requestor.';
}

 else {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" ></script>
</head>
<style>
<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f8f9fa;
        color: #495057;
    }

    section.container {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }

    h1 {
        color: #007bff;
    }

    h3 {
        color: #007bff;
    }

    .mb-3 {
        margin-bottom: 1.5rem !important;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover{
        filter: brightness(90%);
    }
    .btn-rectangle {
    border-radius: 20;
}
</style>
    </style>
<body>
    <section class="container">
    <h1 class="mb-5">Vehicle Form Data</h1>

    <!-- first row -->
    <div class="row mb-5">
        <!-- column for requestor details -->
        <div class="col-sm-6">
            <h3 class="mb-3">Requestor Details</h3>
            <p><strong>RESERVATION # <?php echo $reservationId?></strong></p>
            <p><strong>Form submitted on:</strong> <?php echo $submissionTime?></p>
            <p><strong>Full Name:</strong> <?php echo $fullName?></p>
            <p><strong>Email:</strong> <?php echo $userEmail?></p>
            <p><strong>Office:</strong> <?php echo $office?></p>
            <strong>ID Picture:</strong>
            
            <div> <?php  if (!empty($uploadFilePath)) {
        echo "<img src='$uploadFilePath' alt='Uploaded Photo' style='max-width: 100%; height: 400px;'>";
    } else {
        echo "<p>No uploaded photo.</p>";
    } ?></div>
        </div>

        <!-- column for activity details -->
        <div class="col-sm-6">
            <h3 class="mb-3">Activity Details</h3>
            <p><strong>Purpose:</strong> <?php echo $purpose?></p>
            <p><strong>Destination:</strong> <?php echo $destination?></p>
            <p><strong>Vehicle:</strong> <?php echo $vehicleName?></p>
            <p><strong>Number of Passengers:</strong> <?php echo $numOfPassengers?></p>
            <p><strong>Date Needed:</strong> <?php echo $date?></p>
            <p><strong>Time Needed:</strong> <?php echo $timeSlot?></p>

        </div>
    </div>
<!-- Buttons container -->
<div class="row">
    <div class="col-6">
    <form id="approvalForm" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="additionalRemarks" class="form-label"><strong>Additional Remarks:</strong></label>
                <textarea class="form-control" id="additionalRemarks" name="additionalRemarks" rows="3"></textarea>
            </div>
       
        <input type="hidden" name="reservationId" value="<?php echo $reservationId; ?>">
        <input type="hidden" name="vehicle_name" value="<?php echo $vehicleName; ?>">
        <input type="hidden" name="vehicle_id" value="<?php echo $vehicleId; ?>">
        <input type="hidden" name="fullName" value="<?php echo $fullName; ?>">
       
        <input type="hidden" name="office" value="<?php echo $office; ?>">
        <input type="hidden" name="purpose" value="<?php echo $purpose; ?>">
        <input type="hidden" name="destination" value="<?php echo $destination; ?>">
        <input type="hidden" name="userEmail" value="<?php echo $userEmail; ?>">
        <input type="hidden" name="recipientName" value="<?php echo $recipientName; ?>">
        <input type="hidden" name="recipientEmail" value="<?php echo $recipientEmail; ?>">
        <input type="hidden" name="numOfPassengers" value="<?php echo $numOfPassengers; ?>">
        <input type="hidden" name="reservation-date" value="<?php echo $date; ?>">
        <input type="hidden" name="time-slot" value="<?php echo $timeSlot; ?>">
       
        
        <input type="hidden" name="submissionTime" value="<?php echo $submissionTime; ?>">
        <input type="hidden" name="uploadFilePath" value="<?php echo $uploadFilePath; ?>">
        
        
         
   <button type="submit" class="btn btn-success btn-rectangle btn-lg" name="approve">Approve</button>
    
        </form>
    
</div>

</div>

    </div>
</div>

</div>              
</div>
</div>
</div>
</div>
   
    </section>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const approvalForm = document.getElementById('approvalForm');
        const recipientName = "<?php echo $recipientName; ?>";

        if (recipientName === 'SAD') {
            approvalForm.action = '../controller/vehicleApprove.php';
        } else {
            approvalForm.action = '../vendor/vehicle_send_signatory2.php';
        }
    });
</script>

  
</body>
</html>
<?php
}
?>
