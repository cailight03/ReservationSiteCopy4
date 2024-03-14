<?php
// view_reservation.php

include '../config/connection.php';

// Check if the reservation ID is provided in the URL
if (isset($_GET['reservationId'])) {
    $reservationId = $_GET['reservationId'];
    $userEmail = $_GET['userEmail'];
    $adviserEmail = isset($_GET['adviserEmail']) ? $_GET['adviserEmail'] : '';
    $recipientName = $_GET['recipientName'];
    $categoryId = $_GET['category_id'];
  



   
    
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

    $selectedItems = isset($row['items_needed']) ? json_decode($row['items_needed'], true) : null;
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

        .btn {
            margin-right: 10px;
            text-decoration: none;
        }

       

        .success-modal {
            display: none;
        }

        <style>
    .highlight-range {
        background-color: #e0f7fa; /* Set your desired highlight color */
    }
    
       .required {
        color: red;
        margin-left: 3px;
    }

    .custom-select {
    height: 17rem;
    min-height: 3rem; /* Adjust this value as needed */
}

.custom-select2 {
    height: 9rem;
    min-height: 3rem; /* Adjust this value as needed */
}




.custom-select option:disabled {
    color: red;
    cursor: not-allowed;
}

.custom-select2  option:disabled {
    color: red;
    cursor: not-allowed;
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

    .container {
            
            box-shadow: none;
        }

        body {
            
            background-color: #fff;
            
        }
}
</style>
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

    <div class="mb-3">

    <h1 >Reservation #<?php echo $row['id']; ?></h1>
    <p><strong>Date Submitted:</strong> <?php echo $row['date_submitted']; ?></p>
        <p><strong>Status:</strong> <?php echo $row['status']; ?></p>

    </div>


        <div class="row">
            <div class="col-6 ">
            <h3>Requestor Details</h3>
            <p><strong>Full Name:</strong> <?php echo $row['requestor']; ?></p>
        <p><strong>Organization:</strong> <?php echo $row['organization']; ?></p>
        <p><strong>User Type:</strong> <?php echo $row['userType']; ?></p>
        <p><strong>College/Department/Office:</strong> <?php echo $row['department']; ?></p>
            </div>

            <div class="col-6">
                <h3> Form Details</h3>
                <p><strong>Room Name:</strong> <?php echo $row['room_name']; ?></p>
        
        <p><strong>Activity Type:</strong> <?php echo $row['activityType']; ?></p>
        <p><strong>Activity Name:</strong> <?php echo $row['activity_name']; ?></p>
        <p><strong>No. of Attendees:</strong> <?php echo $row['num_of_attendees']; ?></p>
        <p><strong>Date:</strong> <?php echo $row['date']; ?></p>
        <p><strong>Time Slot:</strong> <?php echo $row['time_slot']; ?></p>
        <div class="row">
        <p><strong>Items Needed:</strong></p>
            
            <div class="mb-3"> 
                <?php   if ($selectedItems !== null) {
        foreach ($selectedItems as $item) {
            echo "<li>{$item['item']} {$item['quantity']}</li>";
        }
    }?>
            </div>  
        
        <p><strong>Remarks:</strong> <?php echo $row['remarks']; ?></p>

            </div>
        </div>
        
      
        
        
        
            <div class="col-6 mb-3 hidden">
            <h3>ID Photo:</h3>
            
            <?php  if (!empty($row['uploadFilePath'])) {
        echo "<img src='".$row['uploadFilePath']."' alt='Uploaded Photo' style='max-width:auto; height: 400px;'>";
    } else {
        echo "<p>No uploaded photo.</p>";
    } ?>
            </div>
            </div>
        
        
          
        
       
        
        <?php if ($row['status'] === 'Rejected'): ?>
            <p><strong>Admin Remarks:</strong> <?php echo $row['admin_remarks']; ?></p>
        <?php endif; ?>

        <?php if ($row['status'] === 'Approved'): ?>
            <p><strong>Admin Remarks:</strong> <?php echo $row['admin_remarks']; ?></p>
        <?php endif; ?>

        <?php

if ($row['userType'] == 'student' && $row['activityType'] == 'Academic Activity') {
    switch ($row['room_name']) {
        case 'Comlab 1':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Comlab head 1 ". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                  </div>";
            break;
        case 'Comlab 2':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Comlab head 2". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Academic Director ". $row['Act4'] ." ".$row['time4']."</p>
                    <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                  </div>";
            break;
        case 'Comlab 3':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Comlab head 3". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                  </div>";
            break;
        case 'Comlab 4':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Comlab head 4". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                  </div>";
            break;
        case 'Comlab 5':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Comlab head 5". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                    </div>";
            break;
        case 'Chemlab 1':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Chemlab head 1". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                    </div>";
            break;
            case 'Chemlab 2':
                echo "<div>
                        <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                        <p><strong>Signatory 2</strong>: Chemlab head 2". $row['Act2'] ." ".$row['time2']."</p>
                        <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                        <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                        <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                        </div>";
            break;
        case 'Crimlab 1':
        echo "<div>
                <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                <p><strong>Signatory 2</strong>: Crimlab head 1". $row['Act2'] ." ".$row['time2']."</p>
                <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                </div>";
            break;
        case 'Crimlab 2':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Crimlab head 2". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                    </div>";
            break;
        case 'TSMJ Lab':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: TSMJ Lab head". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                    </div>";
            break;
         
        default:
            echo "<div class='hidden'>
            <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
            <p><strong>Signatory 2: </strong>NU Laguna Reservation ". $row['Act2'] ." ".$row['time2']."</p>
            <p><strong>Signatory 3: </strong>Academic Director ". $row['Act3'] ." ".$row['time3']."</p>
            <p><strong>Signatory 4: </strong> Physical Facilities ". $row['Act4'] ." ".$row['time4']."</p>
                  </div>";

                  if ($row['status'] === 'Approved'):

                  echo '<div class="row justify-content-end mt-5">
                  <div class="col-3 me-2 text-center">
              <p class=" border border-0 text-bg-secondary  px-4 ">NUL RSRVN</p>
              <div class=" ">
                  <img src="../img/signature_img/signature.jpg" alt="" style="max-width: 100%; height: 50px;">
                  <h6><strong>Mary Minette D. Robediso</strong></h6>
            
              </div>
                  </div>
                  <div class="col-3 me-2 text-center">
              <p class=" border border-0 text-bg-secondary  px-4 ">ACADEMIC DIR.</p>
              <div class=" ">
                  <img src="../img/signature_img/sig2.png" alt="" style="max-width: 100%; height: 50px;">
                  <h6><strong>Josefina G. San Miguel</strong></h6>
                  
              </div>
                  </div>
                  <div class="col-3 me-2 text-center">
              <p class=" border border-0 text-bg-secondary  px-4 ">PHY. FACI.</p>
              <div class=" ">
                  <img src="../img/signature_img/sig2.png" alt="" style="max-width: 100%; height: 50px;">
                  <h6><strong>Jane D. Doe</strong></h6>
                  
              </div>
                  </div>
                  
              </div>';

                  endif;

                  
            break;
    }
}elseif($row['userType'] == 'student' && ($row['activityType'] == 'Non-Academic Activity' )) 
{
    switch ($row['room_name']) {
        case 'Comlab 1':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Comlab head 1 ". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>SDAO ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>NU Laguna Reservation ". $row['Act4'] ." ".$row['time4']."</p>
                    <p><strong>Signatory 5: </strong>Academic Director ". $row['Act5'] ." ".$row['time5']."</p>
                    <p><strong>Signatory 6: </strong>Physical Facilities ". $row['Act6'] ." ".$row['time6']."</p>
                  </div>";
            break;
        case 'Comlab 2':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Comlab head 2". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>SDAO ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>NU Laguna Reservation ". $row['Act4'] ." ".$row['time4']."</p>
                    <p><strong>Signatory 5: </strong>Academic Director ". $row['Act5'] ." ".$row['time5']."</p>
                    <p><strong>Signatory 6: </strong>Physical Facilities ". $row['Act6'] ." ".$row['time6']."</p>
                  </div>";
            break;
        case 'Comlab 3':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Comlab head 3". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>SDAO ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>NU Laguna Reservation ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong>Academic Director ". $row['Act5'] ." ".$row['time5']."</p>
                    <p><strong>Signatory 6: </strong>Physical Facilities ". $row['Act6'] ." ".$row['time6']."</p>
                  </div>";
            break;
        case 'Comlab 4':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Comlab head 4". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>SDAO ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>NU Laguna Reservation ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong>Academic Director ". $row['Act5'] ." ".$row['time5']."</p>
                    <p><strong>Signatory 6: </strong>Physical Facilities ". $row['Act6'] ." ".$row['time6']."</p>
                  </div>";
            break;
        case 'Comlab 5':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Comlab head 5". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>SDAO ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>NU Laguna Reservation ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong>Academic Director ". $row['Act5'] ." ".$row['time5']."</p>
                    <p><strong>Signatory 6: </strong>Physical Facilities ". $row['Act6'] ." ".$row['time6']."</p>
                    </div>";
            break;
        case 'Chemlab 1':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Chemlab head 1". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>SDAO ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>NU Laguna Reservation ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong>Academic Director ". $row['Act5'] ." ".$row['time5']."</p>
                    <p><strong>Signatory 6: </strong>Physical Facilities ". $row['Act6'] ." ".$row['time6']."</p>
                    </div>";
            break;
            case 'Chemlab 2':
                echo "<div>
                        <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                        <p><strong>Signatory 2</strong>: Chemlab head 2". $row['Act2'] ." ".$row['time2']."</p>
                        <p><strong>Signatory 3: </strong>SDAO ". $row['Act3'] ." ".$row['time3']."</p>
                        <p><strong>Signatory 4: </strong>NU Laguna Reservation ".$row['Act4'] ." ".$row['time4'] ."</p>
                        <p><strong>Signatory 5: </strong>Academic Director ". $row['Act5'] ." ".$row['time5']."</p>
                        <p><strong>Signatory 6: </strong>Physical Facilities ". $row['Act6'] ." ".$row['time6']."</p>
                        </div>";
            break;
        case 'Crimlab 1':
        echo "<div>
                <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                <p><strong>Signatory 2</strong>: Crimlab head 1". $row['Act2'] ." ".$row['time2']."</p>
                <p><strong>Signatory 3: </strong>SDAO ". $row['Act3'] ." ".$row['time3']."</p>
                <p><strong>Signatory 4: </strong>NU Laguna Reservation ".$row['Act4'] ." ".$row['time4'] ."</p>
                <p><strong>Signatory 5: </strong>Academic Director ". $row['Act5'] ." ".$row['time5']."</p>
                <p><strong>Signatory 6: </strong>Physical Facilities ". $row['Act6'] ." ".$row['time6']."</p>
                </div>";
            break;
        case 'Crimlab 2':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Crimlab head 2". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>SDAO ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>NU Laguna Reservation ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong>Academic Director ". $row['Act5'] ." ".$row['time5']."</p>
                    <p><strong>Signatory 6: </strong>Physical Facilities ". $row['Act6'] ." ".$row['time6']."</p>
                    </div>";
            break;
        case 'TSMJ Lab':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: TSMJ Lab head". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>SDAO ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>NU Laguna Reservation ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong>Academic Director ". $row['Act5'] ." ".$row['time5']."</p>
                    <p><strong>Signatory 6: </strong>Physical Facilities ". $row['Act6'] ." ".$row['time6']."</p>
                    </div>";
            break;
         
        default:
            echo "<div class='hidden'>
            <p><strong>Signatory 1: </strong>".$adviserEmail." ". $row['Act1'] ." ".$row['time1']."</p>
            <p><strong>Signatory 2</strong>:SDAO". $row['Act2'] ." ".$row['time2']."</p>
            <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
            <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
            <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
            </div>";

            if ($row['status'] === 'Approved'):

                echo '<div class="row justify-content-end mt-5">
                <div class="col-3 me-2 text-center">
            <p class=" border border-0 text-bg-secondary  px-4 ">NUL RSRVN</p>
            <div class=" ">
                <img src="../img/signature_img/signature.jpg" alt="" style="max-width: 100%; height: 50px;">
                <h6><strong>Mary Minette D. Robediso</strong></h6>
          
            </div>
                </div>
                <div class="col-3 me-2 text-center">
            <p class=" border border-0 text-bg-secondary  px-4 ">SDAO</p>
            <div class=" ">
                <img src="../img/signature_img/signature.jpg" alt="" style="max-width: 100%; height: 50px;">
                <h6><strong>John D. Doe</strong></h6>
          
            </div>
                </div>
                <div class="col-3 me-2 text-center">
            <p class=" border border-0 text-bg-secondary  px-4 ">ACADEMIC DIR.</p>
            <div class=" ">
                <img src="../img/signature_img/sig2.png" alt="" style="max-width: 100%; height: 50px;">
                <h6><strong>Josefina G. San Miguel</strong></h6>
                
            </div>
                </div>
                <div class="col-3 me-2 text-center">
            <p class=" border border-0 text-bg-secondary  px-4 ">PHY. FACI.</p>
            <div class=" ">
                <img src="../img/signature_img/sig2.png" alt="" style="max-width: 100%; height: 50px;">
                <h6><strong>Jane D. Doe</strong></h6>
                
            </div>
                </div>
                
            </div>';

                endif;

                  
            break;
    }
}elseif($row['userType'] == 'employee' && $row['activityType'] == 'Others Academic'){
    switch ($row['room_name']) {
        case 'Comlab 1':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$recipientName." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Comlab head 1 ". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Academic Director ". $row['Act4'] ." ".$row['time4']."</p>
                    <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                  </div>";
            break;
        case 'Comlab 2':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$recipientName." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Comlab head 2". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Academic Director ". $row['Act4'] ." ".$row['time4']."</p>
                    <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                  </div>";
            break;
        case 'Comlab 3':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$recipientName." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Comlab head 3". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                  </div>";
            break;
        case 'Comlab 4':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$recipientName." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Comlab head 4". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                  </div>";
            break;
        case 'Comlab 5':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$recipientName." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Comlab head 5". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                    </div>";
            break;
        case 'Chemlab 1':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$recipientName." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Chemlab head 1". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                    </div>";
            break;
            case 'Chemlab 2':
                echo "<div>
                        <p><strong>Signatory 1: </strong>".$recipientName." ". $row['Act1'] ." ".$row['time1']."</p>
                        <p><strong>Signatory 2</strong>: Chemlab head 2". $row['Act2'] ." ".$row['time2']."</p>
                        <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                        <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                        <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                        </div>";
            break;
        case 'Crimlab 1':
        echo "<div>
                <p><strong>Signatory 1: </strong>".$recipientName." ". $row['Act1'] ." ".$row['time1']."</p>
                <p><strong>Signatory 2</strong>: Crimlab head 1". $row['Act2'] ." ".$row['time2']."</p>
                <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                </div>";
            break;
        case 'Crimlab 2':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$recipientName." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: Crimlab head 2". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                    </div>";
            break;
        case 'TSMJ Lab':
            echo "<div>
                    <p><strong>Signatory 1: </strong>".$recipientName." ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2</strong>: TSMJ Lab head". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>NU Laguna Reservation ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Academic Director ".$row['Act4'] ." ".$row['time4'] ."</p>
                    <p><strong>Signatory 5: </strong> Physical Facilities ". $row['Act5'] ." ".$row['time5']."</p>
                    </div>";
            break;
         
        default:
            echo "<div class='hidden'>
            <p><strong>Signatory 1: </strong>".$recipientName." ". $row['Act1'] ." ".$row['time1']."</p>
            <p><strong>Signatory 2: </strong>NU Laguna Reservation ". $row['Act2'] ." ".$row['time2']."</p>
            <p><strong>Signatory 3: </strong>Academic Director ". $row['Act3'] ." ".$row['time3']."</p>
            <p><strong>Signatory 4: </strong> Physical Facilities ". $row['Act4'] ." ".$row['time4']."</p>
                  </div>";

                  if ($row['status'] === 'Approved'):

                    echo '<div class="row justify-content-end mt-5">
                    <div class="col-3 me-2 text-center">
                <p class=" border border-0 text-bg-secondary  px-4 ">NUL RSRVN</p>
                <div class=" ">
                    <img src="../img/signature_img/signature.jpg" alt="" style="max-width: 100%; height: 50px;">
                    <h6><strong>Mary Minette D. Robediso</strong></h6>
              
                </div>
                    
                    <div class="col-3 me-2 text-center">
                <p class=" border border-0 text-bg-secondary  px-4 ">ACADEMIC DIR.</p>
                <div class=" ">
                    <img src="../img/signature_img/sig2.png" alt="" style="max-width: 100%; height: 50px;">
                    <h6><strong>Josefina G. San Miguel</strong></h6>
                    
                </div>
                    </div>
                    <div class="col-3 me-2 text-center">
                <p class=" border border-0 text-bg-secondary  px-4 ">PHY. FACI.</p>
                <div class=" ">
                    <img src="../img/signature_img/sig2.png" alt="" style="max-width: 100%; height: 50px;">
                    <h6><strong>Jane D. Doe</strong></h6>
                    
                </div>
                    </div>
                    
                </div>';
    
                    endif;

                  
            break;
    }
}elseif($row['userType'] == 'admin' && $row['activityType'] == 'Others Admin'){
    switch ($row['room_name']) {
        case 'Comlab 1':
            echo "<div>
                    
                    <p><strong>Signatory 1</strong>: Comlab head 1 ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2: </strong>NU Laguna Reservation ". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>SAD ".$row['Act3'] ." ".$row['time3'] ."</p>
                    <p><strong>Signatory 4: </strong>Physical Facilities ".$row['Act4'] ." ".$row['time4'] ."</p>
                    
                  </div>";
            break;
        case 'Comlab 2':
            echo "<div>
                    
                    <p><strong>Signatory 1</strong>: Comlab head 2". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2: </strong>NU Laguna Reservation ". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>SAD ". $row['Act3'] ." ".$row['time3']."</p>
                    <p><strong>Signatory 4: </strong>Physical Facilities ".$row['Act4'] ." ".$row['time4'] ."</p>
                    
                  </div>";
            break;
        case 'Comlab 3':
            echo "<div>
                    
                    <p><strong>Signatory 1</strong>: Comlab head 3". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2: </strong>NU Laguna Reservation ". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>SAD ".$row['Act3'] ." ".$row['time3'] ."</p>
                    <p><strong>Signatory 4: </strong>Physical Facilities ".$row['Act4'] ." ".$row['time4'] ."</p>
                    
                  </div>";
            break;
        case 'Comlab 4':
            echo "<div>
                    
                    <p><strong>Signatory 1</strong>: Comlab head 4". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2: </strong>NU Laguna Reservation ". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>SAD ".$row['Act3'] ." ".$row['time3'] ."</p>
                    
                    <p><strong>Signatory 4: </strong>Physical Facilities ".$row['Act4'] ." ".$row['time4'] ."</p>
                    
                  </div>";
            break;
        case 'Comlab 5':
            echo "<div>
                    
                    <p><strong>Signatory 1</strong>: Comlab head 5". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2: </strong>NU Laguna Reservation ". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>SAD ".$row['Act3'] ." ".$row['time3'] ."</p>
                    
                    <p><strong>Signatory 4: </strong>Physical Facilities ".$row['Act4'] ." ".$row['time4'] ."</p>
                    
                    </div>";
            break;
        case 'Chemlab 1':
            echo "<div>
                    
                    <p><strong>Signatory 1</strong>: Chemlab head 1". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2: </strong>NU Laguna Reservation ". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>SAD ".$row['Act3'] ." ".$row['time3'] ."</p>
                    
                    <p><strong>Signatory 4: </strong>Physical Facilities ".$row['Act4'] ." ".$row['time4'] ."</p>
                    
                    </div>";
            break;
            case 'Chemlab 2':
                echo "<div>
                        
                        <p><strong>Signatory 1</strong>: Chemlab head 2". $row['Act1'] ." ".$row['time1']."</p>
                        <p><strong>Signatory 2: </strong>NU Laguna Reservation ". $row['Act2'] ." ".$row['time2']."</p>
                        <p><strong>Signatory 3: </strong>SAD ".$row['Act3'] ." ".$row['time3'] ."</p>
                       
                    <p><strong>Signatory 4: </strong>Physical Facilities ".$row['Act4'] ." ".$row['time4'] ."</p>
                        
                        </div>";
            break;
        case 'Crimlab 1':
        echo "<div>
                
                <p><strong>Signatory 1</strong>: Crimlab head 1". $row['Act1'] ." ".$row['time1']."</p>
                <p><strong>Signatory 2: </strong>NU Laguna Reservation ". $row['Act2'] ." ".$row['time2']."</p>
                <p><strong>Signatory 3: </strong>SAD ".$row['Act3'] ." ".$row['time3'] ."</p>
               
                    <p><strong>Signatory 4: </strong>Physical Facilities ".$row['Act4'] ." ".$row['time4'] ."</p>
                
                </div>";
            break;
        case 'Crimlab 2':
            echo "<div>
                    
                    <p><strong>Signatory 1</strong>: Crimlab head 2". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2: </strong>NU Laguna Reservation ". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>SAD ".$row['Act3'] ." ".$row['time3'] ."</p>
                    
                    <p><strong>Signatory 4: </strong>Physical Facilities ".$row['Act4'] ." ".$row['time4'] ."</p>
                    
                    </div>";
            break;
        case 'TSMJ Lab':
            echo "<div>
                    
                    <p><strong>Signatory 1</strong>: TSMJ Lab head". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2: </strong>NU Laguna Reservation ". $row['Act2'] ." ".$row['time2']."</p>
                    <p><strong>Signatory 3: </strong>SAD ".$row['Act3'] ." ".$row['time3'] ."</p>
                    
                    <p><strong>Signatory 4: </strong>Physical Facilities ".$row['Act4'] ." ".$row['time4'] ."</p>
                    
                    </div>";
            break;
         
        default:
            echo "<div class='hidden'>
            <p><strong>Signatory 1: </strong>NU Laguna Reservation ". $row['Act1'] ." ".$row['time1']."</p>
                    <p><strong>Signatory 2: </strong>SAD ".$row['Act2'] ." ".$row['time2'] ."</p>
                    <p><strong>Signatory 3: </strong>Physical Facilities ".$row['Act3'] ." ".$row['time3'] ."</p>
                  </div>";

                  if ($row['status'] === 'Approved'):

                    echo '<div class="row justify-content-end mt-5">
                    <div class="col-3 me-2 text-center">
                <p class=" border border-0 text-bg-secondary  px-4 ">NUL RSRVN</p>
                <div class=" ">
                    <img src="../img/signature_img/signature.jpg" alt="" style="max-width: 100%; height: 50px;">
                    <h6><strong>Mary Minette D. Robediso</strong></h6>
              
                </div>
                    </div>
                    
                    <div class="col-3 me-2 text-center">
                <p class=" border border-0 text-bg-secondary  px-4 ">SAD</p>
                <div class=" ">
                    <img src="../img/signature_img/sig2.png" alt="" style="max-width: 100%; height: 50px;">
                    <h6><strong>Josefina G. San Miguel</strong></h6>
                    
                </div>
                    </div>
                    <div class="col-3 me-2 text-center">
                <p class=" border border-0 text-bg-secondary  px-4 ">PHY. FACI.</p>
                <div class=" ">
                    <img src="../img/signature_img/sig2.png" alt="" style="max-width: 100%; height: 50px;">
                    <h6><strong>Jane D. Doe</strong></h6>
                    
                </div>
                    </div>
                    
                </div>';
    
                    endif;

                  
            break;
    }

}
?>


        
       

        <div class="buttons hidden">
            <?php if ($row['status'] !== 'Cancelled'): ?>
                <?php if ($row['status'] === 'Pending'): ?>
                    <button class="btn btn-primary reschedule" disabled>Reschedule</button>
                <?php else: ?>
                    <a href="#" class="btn btn-primary reschedule" data-bs-toggle="modal" data-bs-target="#rescheduleModal<?php echo $row['id']; ?>">Reschedule</a>
                <?php endif; ?>
                <button class="btn btn-danger cancel" data-bs-toggle="modal" data-bs-target="#cancelConfirmationModal">Cancel</button>
            <?php endif; ?>
            <?php if ($row['status'] === 'Approved'): ?>
                <button onclick="window.print();" class="btn btn-success">Print</button>
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
                    <form method="POST" action="reschedule_room_reservation.php">

                  

                    <div class="mb-3">
        <label for="datepicker" class="form-label">Date(s) <span class="required">*</span></label>
        <input class="form-control" type="text" id="datepicker" name="reservation-date1" required>
        <small class="text-secondary">Select the same date twice for one day. Select two different dates for multiple days.</small>
    </div>

    <input type="hidden" id="reservation-date" name="reservation-date" />


    <!-- time slot -->
   
    <!-- time slot for single date -->
    
<div id="timeSlot" class="mb-3">
    <label class="form-label" for="timeSlotSingle" id="timeSlotSingleLabel">Select Time:</label>
    <select id="timeSlotSingle" class="form-control custom-select" multiple required></select>
    <small class="text-secondary" id="small-text">Click for one time slot. Click and drag for multiple time slots.</small>
    <br>
    <label class="form-label" for="selectedTimeSingle" id="selectedTimeSingleLabel">Selected Time Range: <span class="required">*</span></label>
    <input type="text" id="selectedTimeSingle" class="form-control" readonly name="time-slot" required>
</div>


<!-- time slots for multiple dates -->
<div id="timeSlotMultiDateContainer" style="display: none;">
    <!-- Dynamically generated time slot inputs will be added here -->
</div>
<input type="hidden" id="hiddenTimeRanges" name="time-slot" />
                        <input type="hidden" name="reservationId" value="<?php echo $row['id']; ?>">

                        <input type="hidden" name="email" value="<?php  echo $userEmail ?>">
                        <input type="hidden" name="category_id" value="<?php  echo $categoryId ?>">

                        <input type="hidden" name="adviserEmail" value="<?php  echo $adviserEmail ?>">

                        <input type="hidden" name="recipientName" value="<?php  echo $recipientName ?>">
                        <input type="hidden" name="selectedItems" value="<?php echo htmlspecialchars(json_encode($selectedItems)); ?>">

                        <input type="hidden" name="date_submitted" value="<?php echo $row['date_submitted']; ?>">

                        <input type="hidden" name="uploadFilePath" value="<?php echo $row['uploadFilePath']; ?>">

                       
                        
                        



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

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- Include flatpickr and Bootstrap at the end of the body -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    <script>
document.addEventListener("DOMContentLoaded", async function () {
    try {
        // Get the room_id from the PHP script
        const roomId = <?php echo $row['room_id']; ?>;

        // Fetch booked dates for the specific room
        const bookedDates = await getBookedDates(roomId);

        // Initialize flatpickr
        flatpickr("#datepicker", {
            mode: "range",
            dateFormat: "Y-m-d",
            minDate: new Date().fp_incr(4),
            disable: [
                {
                    from: "today",
                    to: "today",
                },
                // Include your booked dates
                ...bookedDates,
            ],
            onClose: function (selectedDates, dateStr, instance) {
                if (selectedDates.length >= 1) {
                    // Valid date range selected
                    // Extract individual dates from the range
                    const dateIntervals = getDatesBetween(selectedDates[0], selectedDates[selectedDates.length - 1]);

                    // Update the hidden input with the date intervals
                    document.getElementById('reservation-date').value = dateIntervals.join(',');

                    // Display the appropriate time slot input based on the number of selected dates
                    if (dateIntervals.length === 1) {
                        removeMultiDateInputs ()
                        $('#timeSlotSingle').show();
                        $('#selectedTimeSingle').show();
                        $('#selectedTimeSingleLabel').show();
                        $('#timeSlotSingleLabel').show();
                        $('#small-text').show();
                        $('#timeSlotSingle').attr('required', true);
                        $('#selectedTimeSingle').attr('required', true);
                        $('#timeSlotMultiDateContainer').hide();
                        populateMultiDateTimeSlots('timeSlotSingle', dateIntervals[0]);
                    } else {
                        createMultiDateInputs(dateIntervals);
                        $('#timeSlotSingle').hide();
                        $('#selectedTimeSingle').hide();
                        $('#small-text').hide();
                        $('#timeSlotSingle').removeAttr('required');
                        $('#selectedTimeSingle').removeAttr('required');
                        $('#timeSlotMultiDateContainer').show();
                        dateIntervals.forEach(date => {
                            populateMultiDateTimeSlots(`timeSlot-${date}`, date);
                            addLabelForDate(`timeSlot-${date}`, date);
                            addSelectedTimeRangeAfterContainer(`timeSlot-${date}`);
                        });
                        $('#selectedTimeSingleLabel').hide();
                        $('#timeSlotSingleLabel').hide();
                    }

                    // Use a different method to open the modal
                    $('#exampleModal').modal('show');
                } else {
                    alert('Please select a valid date range.');
                }
            }
        });

    } catch (error) {
        console.error('Error fetching and initializing:', error);
    }
});

function removeMultiDateInputs () {
    const hiddenTimeRangesInput = document.getElementById('hiddenTimeRanges');
const multiDateInputs = document.querySelectorAll('[id^="selectedTime-"]');
multiDateInputs.forEach(input => input.remove())
// Clear the hidden time ranges when the date is changed
hiddenTimeRangesInput.value = '';

}



function addSelectedTimeRangeAfterContainer(containerId) {
    // Create a div element for the selected time range
    const selectedTimeRangeDiv = document.createElement('div');
    selectedTimeRangeDiv.className = 'mb-3';
    selectedTimeRangeDiv.innerHTML = '<label for="selectedTime" class="form-label">Selected Time Range: <span class="required">*</span></label>' +
        '<input type="text" id="selectedTime-' + containerId + '" class="form-control" readonly name="time-slot" required>';

    // Append the div after the container
    document.getElementById(containerId).insertAdjacentElement('afterend', selectedTimeRangeDiv);
}


function getDatesBetween(startDate, endDate) {
    const dates = [];
    let currentDate = new Date(startDate);

    while (currentDate <= endDate) {
        currentDate.setDate(currentDate.getDate() + 1);
        dates.push(currentDate.toISOString().split('T')[0]); // Format as YYYY-MM-DD
    }

    return dates;
}

// Function to get booked dates from get_booked_dates.php
async function getBookedDates(roomId) {
    try {
        const response = await fetch(`controller/get_booked_dates.php?room_id=${roomId}`);
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


function combineTimeRanges(ranges) {
    const startTime = ranges[0].split('-')[0];
    const endTime = ranges[ranges.length - 1].split('-')[1];

    return `${startTime}-${endTime}`;
}




// Function to populate time slots for a specific date
async function populateMultiDateTimeSlots(selectId, date) {
    try {
        const roomId = <?php echo $row['room_id']; ?>;
        const response = await fetch(`controller/get_booked_time_slots.php?room_id=${roomId}&date=${date}`);
        if (!response.ok) {
            throw new Error(`Failed to fetch booked time slots for ${date}`);
        }
        const bookedTimeSlots = await response.json();
        const selectElement = document.getElementById(selectId);
        const selectedTimeInput = document.getElementById(`selectedTime-${selectId}`);
        selectElement.innerHTML = '';
        const startHour = 7;
        const endHour = 21;
        const interval = 30;

        for (let hour = startHour; hour < endHour; hour++) {
            for (let minute = 0; minute < 60; minute += interval) {
                const formattedHour = hour % 12 || 12;
                const ampm = hour < 12 ? 'AM' : 'PM';
                const nextHour = (hour + Math.floor((minute + interval) / 60)) % 12 || 12;
                const nextAmpm = (hour + Math.floor((minute + interval) / 60)) < 12 ? 'AM' : 'PM';
                const option = document.createElement('option');
                const timeSlot = `${formattedHour}:${String(minute).padStart(2, '0')} ${ampm}-${nextHour}:${String((minute + interval) % 60).padStart(2, '0')} ${nextAmpm}`;

                option.value = timeSlot;
                option.text = timeSlot;
                option.disabled = bookedTimeSlots.includes(timeSlot);

                selectElement.add(option);
            }
        }

        // Update the selected time input when a time slot is chosen
        document.getElementById('timeSlotSingle').addEventListener('change', updateHiddenTimeRanges);
        



        selectElement.addEventListener('change', updateMultiSelectedTime);
        

    function updateHiddenTimeRanges() {
    const multiDateInputs = document.querySelectorAll('[id^="selectedTime-"]');
    const hiddenTimeRangesInput = document.getElementById('hiddenTimeRanges');
    const selectedTimeSingle = document.getElementById('selectedTimeSingle');

    

    if (multiDateInputs.length > 0) {
        // Extract selected time ranges from each input for multiple dates
        const selectedTimeRanges = Array.from(multiDateInputs).map(input => input.value);
        hiddenTimeRangesInput.value = selectedTimeRanges.join(',');
    } else {
        
        // Extract selected time range from the single date input
        hiddenTimeRangesInput.value = selectedTimeSingle.value;
    }
    console.log(multiDateInputs.length);
    console.log('Hidden Time Ranges:', hiddenTimeRangesInput.value); 
}

        function updateMultiSelectedTime() {
    const selectedOptions = Array.from(selectElement.selectedOptions);
    const selectedRanges = selectedOptions.map(option => option.value);
    const combinedRange = combineTimeRanges(selectedRanges);
    const selectedTimeSingle = document.getElementById('selectedTimeSingle');

    // Check if the selectedTimeInput element is available
    if (selectedTimeInput) {
        selectedTimeInput.value = combinedRange;
        updateHiddenTimeRanges();
        console.log('Selected Time Range:', combinedRange);
    } else {
        selectedTimeSingle.value = combinedRange;
       console.log('Selected Time Range:', combinedRange);
    }
}
    } catch (error) {
        console.error(`Error populating time slots for ${date}:`, error);
    }
}

function createMultiDateInputs(dateIntervals) {
    const container = document.getElementById('timeSlotMultiDateContainer');
    container.innerHTML = '';
    dateIntervals.forEach(date => {
        const timeSlotId = `timeSlot-${date}`;
        const timeSlotInput = document.createElement('select');
        timeSlotInput.id = timeSlotId;
        timeSlotInput.className = 'form-control custom-select2 mb-3';
        timeSlotInput.multiple = true;
        timeSlotInput.required = true;
        container.appendChild(timeSlotInput);
    });
}

function addLabelForDate(selectId, date) {
    const container = document.getElementById('timeSlotMultiDateContainer');

    // Create label element
    const label = document.createElement('label');
    label.htmlFor = selectId;
    label.className = 'form-label';
    label.textContent = `Select time for ${date}`;

    // Find the select element based on its ID
    const selectElement = document.getElementById(selectId);

    // Insert the label before the select element
    container.insertBefore(label, selectElement);
}
</script>


<!-- time slot -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Call the function to populate time slots on page load
    populateTimeSlots();
});

function populateTimeSlots() {
    const startHour = 7; // 7:00 AM
    const endHour = 21; // 9:00 PM
    const interval = 30; // 30 minutes interval

    const selectElement = document.getElementById('timeSlotSingle');
    const selectedTimeInput = document.getElementById('selectedTimeSingle');
    const roomId = <?php echo isset($row['room_id']) ? $row['room_id'] : 'null'; ?>; // Pass the room ID from PHP
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

        fetch(`controller/get_booked_time_slots.php?room_id=${roomId}&date=${selectedDate}`)
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