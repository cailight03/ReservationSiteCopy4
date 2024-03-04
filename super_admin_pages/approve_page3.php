<?php
// print_page.php
include '../config/connection.php';
session_start();


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    date_default_timezone_set('Asia/Manila');

    // Retrieve form data from URL parameters
    $roomId = isset($_GET['roomId']) ? $_GET['roomId'] : '';
    $roomName = isset($_GET['roomName']) ? $_GET['roomName'] : '';
    $fullName = isset($_GET['fullName']) ? $_GET['fullName'] : '';
    $college = isset($_GET['college']) ? $_GET['college'] : '';
    $activityName = isset($_GET['activityName']) ? $_GET['activityName'] : '';
    $numOfAttendees = isset($_GET['numOfAttendees']) ? $_GET['numOfAttendees'] : '';
    $userEmail = isset($_GET['userEmail']) ? $_GET['userEmail'] : '';
    $adviserEmail = isset($_GET['adviserEmail']) ? $_GET['adviserEmail'] : '';
    $recipientName = isset($_GET['recipientName']) ? $_GET['recipientName'] : '';
    $organization = isset($_GET['org']) ? $_GET['org'] : '';
    $categoryId = isset($_GET['category_id']) ? $_GET['category_id'] : '';
    $userType = isset($_GET['userType']) ? $_GET['userType'] : '';
    $activityType = isset($_GET['activityType']) ? $_GET['activityType'] : '';
    $date = isset($_GET['date']) ? $_GET['date'] : '';
    $timeSlot = isset($_GET['timeSlot']) ? $_GET['timeSlot'] : '';
    $endTime = isset($_GET['endTime']) ? $_GET['endTime'] : '';
    $speakerName = isset($_GET['speakerName']) ? $_GET['speakerName'] : '';
    $remarks = isset($_GET['remarks']) ? $_GET['remarks'] : '';
    $reservationId = isset($_GET['reservationId']) ? $_GET['reservationId'] : '';
    $submissionTime = isset($_GET['submissionTime']) ? $_GET['submissionTime'] : '';
    $uploadFilePath = isset($_GET['uploadFilePath']) ? $_GET['uploadFilePath'] : '';
    $selectedItems = isset($_GET['selectedItems']) ? json_decode($_GET['selectedItems'], true) : null;
    

}

// Define your SQL query to check if the recipient name exists in any of the sig columns for the given ID
$query = "SELECT COUNT(*) as count FROM reservations WHERE id = ? AND (sig1 = ? OR sig2 = ? OR sig3 = ? OR sig4 = ? OR sig5 = ?)";

// Prepare the query
$stmt = $connection->prepare($query);

// Bind parameters
$stmt->bind_param("isssss", $reservationId, $recipientName, $recipientName, $recipientName, $recipientName, $recipientName);

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Fetch the count of matching rows
$row = $result->fetch_assoc();

// Check if any rows were found with the recipientName in any of the sig columns
if ($row['count'] > 0) {
    // Recipient name exists in the database
    // Print the message
    echo 'You have already approved this reservation.';
} else {
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
    <h1 class="mb-5">Form Data</h1>

    <!-- first row -->
    <div class="row mb-5">
        <!-- column for requestor details -->
        <div class="col-sm-6">
            <h3 class="mb-3">Requestor Details</h3>
            <p><strong>Reservation ID:</strong> <?php echo $reservationId?></p>
            <p><strong>Form submitted on:</strong> <?php echo $submissionTime?></p>
            <p><strong>Full Name:</strong> <?php echo $fullName?></p>
            <p><strong>User Type:</strong> <?php echo $userType?></p>
            <p><strong>Department/College:</strong> <?php echo $college?></p>
            <p><strong>Org:</strong> <?php echo $organization?></p>
            <strong>ID Picture:</strong>
            
            <div> <?php  if (!empty($uploadFilePath)) {
        echo "<img src='$uploadFilePath' alt='Uploaded Photo' style='max-width: 100%; height: auto;'>";
    } else {
        echo "<p>No uploaded photo.</p>";
    } ?></div>
        </div>

        <!-- column for activity details -->
        <div class="col-sm-6">
            <h3 class="mb-3">Activity Details</h3>
            <p><strong>Speaker's Name:</strong> <?php echo $speakerName?></p>
            <p><strong>Activity Name:</strong> <?php echo $activityName?></p>
            <p><strong>Activity Type:</strong> <?php echo $activityType?></p>
            <p><strong>Venue:</strong> <?php echo $roomName?></p>
            <p><strong>Number of Attendees:</strong> <?php echo $numOfAttendees?></p>
            <p><strong>Dates of Activity:</strong> <?php echo $date?></p>
            <p><strong>Time:</strong> <?php echo $timeSlot?></p>
            
            
          
        </div>
        </div>

    <div class="row">
        <div class="col-sm-6">
            <h3>Items Needed</h3>
            <div class="mb-3"> 
                <?php   if ($selectedItems !== null) {
        foreach ($selectedItems as $item) {
            echo "<li>{$item['item']} {$item['quantity']}</li>";
        }
    }?>
            </div>  
        <p><strong>Remarks:</strong> <?php echo $remarks?></p>
        </div>
<!-- Buttons container -->
<div class="row">
    <div class="col-sm-6">
    <form id="approvalForm" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
            <div class="mb-3">
                <label for="additionalRemarks" class="form-label"><strong>Additional Remarks:</strong></label>
                <textarea class="form-control" id="additionalRemarks" name="additionalRemarks" rows="3"></textarea>
            </div>
    </div>
    <div class="col-sm-6 d-flex justify-content-end align-items-center mt-5">
        <input type="hidden" name="roomId" value="<?php echo $roomId; ?>">
        <input type="hidden" name="reservationId" value="<?php echo $reservationId; ?>">
        <input type="hidden" name="roomName" value="<?php echo $roomName; ?>">
        <input type="hidden" name="fullName" value="<?php echo $fullName; ?>">
        <input type="hidden" name="org" value="<?php echo $organization; ?>">
        <input type="hidden" name="recipientName" value="<?php echo $recipientName; ?>">
        <input type="hidden" name="userType" value="<?php echo $userType; ?>">
        <input type="hidden" name="activityType" value="<?php echo $activityType; ?>">
        <input type="hidden" name="category_id" value="<?php echo $categoryId; ?>">
        <input type="hidden" name="adviserEmail" value="<?php echo $adviserEmail; ?>">
        <input type="hidden" name="college" value="<?php echo $college; ?>">
        <input type="hidden" name="userEmail" value="<?php echo $userEmail; ?>">
        <input type="hidden" name="activityName" value="<?php echo $activityName; ?>">
        <input type="hidden" name="numOfAttendees" value="<?php echo $numOfAttendees; ?>">
        <input type="hidden" name="reservation-date" value="<?php echo $date; ?>">
        <input type="hidden" name="time-slot" value="<?php echo $timeSlot; ?>">
        <input type="hidden" name="endTime" value="<?php echo $endTime; ?>">
        <input type="hidden" name="speakerName" value="<?php echo $speakerName; ?>">
        <input type="hidden" name="remarks" value="<?php echo $remarks; ?>">
        
        <input type="hidden" name="submissionTime" value="<?php echo $submissionTime; ?>">
        <input type="hidden" name="uploadFilePath" value="<?php echo $uploadFilePath; ?>">
        
        <input type="hidden" name="selectedItems" value="<?php echo htmlspecialchars(json_encode($selectedItems)); ?>">

        
        
        <?php
    if ($recipientName === 'Academic Director' || $recipientName === 'SAD') {
        echo '<button type="submit" class="btn btn-success btn-rectangle btn-lg" name="approve">Approve</button>';
    } else {
        echo '<button type="submit" class="btn btn-success btn-rectangle btn-lg" name="approve">Approve</button>';
    }
    ?>
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
</div>
   
    </section>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const approvalForm = document.getElementById('approvalForm');
        const recipientName = "<?php echo $recipientName; ?>";

        if (recipientName === 'Academic Director' || recipientName === 'SAD') {
            approvalForm.action = '../controller/approve.php';
        } else {
            approvalForm.action = '../vendor/send-to-signatory4.php';
        }
    });
</script>

  
</body>
</html>
<?php
}
?>
