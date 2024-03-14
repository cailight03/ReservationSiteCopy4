<?php

include '../config/connection.php';

session_start();



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/vendor/src/PHPMailer.php';
require '../vendor/vendor/src/Exception.php';
require '../vendor/vendor/src/SMTP.php'; // Make sure to adjust the path based on your actual file structure

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    date_default_timezone_set('Asia/Manila');


    // Retrieve form data
    // Fetch the user ID from the session
    $reservationId = isset($_POST['reservationId']) ? $_POST['reservationId'] : '';
    $adminRemarks = isset($_POST['additionalRemarks']) ? $_POST['additionalRemarks'] : '';
    $userEmail = isset($_POST['userEmail']) ? $_POST['userEmail'] : '';
    $recipientName = isset($_POST['recipientName']) ? $_POST['recipientName'] : '';
    $fullName = isset($_POST['fullName']) ? $_POST['fullName'] : '';

  // Array to hold the column names for sig columns
$sigColumns = array(
    'sig1' => 'Sig1',
    'sig2' => 'Sig2'
);

// Find the first empty column for sig columns
$firstEmptySigColumn = null;
foreach ($sigColumns as $columnName => $placeholder) {
    $checkEmptyQuery = "SELECT COUNT(*) as count FROM vehicle_reservations WHERE id = ? AND ($columnName = '' OR $columnName IS NULL)";
    $stmtSig = $connection->prepare($checkEmptyQuery);
    $stmtSig->bind_param("i", $reservationId);
    $stmtSig->execute();
    $result = $stmtSig->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $firstEmptySigColumn = $columnName;
        break;
    }
}

if ($firstEmptySigColumn !== null) {
    // Update the first empty column to $recipientName
    $updateQuery = "UPDATE vehicle_reservations SET $firstEmptySigColumn = ? WHERE id = ? AND ($firstEmptySigColumn = '' OR $firstEmptySigColumn IS NULL)";
    
    $stmtSig = $connection->prepare($updateQuery);
    $stmtSig->bind_param("si", $recipientName, $reservationId);
    $stmtSig->execute();
    
} else {
    echo "No empty column found for reservation ID: $reservationId";
}


  // Array to hold the column names for time columns
$timeColumns = array(
    'time1' => 'time1',
    'time2' => 'time2'
);

// Find the first empty column for time columns
$firstEmptyTimeColumn = null;
foreach ($timeColumns as $columnName => $placeholder) {
    $checkEmptyQuery = "SELECT COUNT(*) as count FROM vehicle_reservations WHERE id = ? AND ($columnName = '' OR $columnName IS NULL)";
    $stmtTime = $connection->prepare($checkEmptyQuery);
    $stmtTime->bind_param("i", $reservationId);
    $stmtTime->execute();
    $result = $stmtTime->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $firstEmptyTimeColumn = $columnName;
        break;
    }
}

if ($firstEmptyTimeColumn !== null) {
    // Update the first empty column to current date and time
    $currentDateTime = date('Y-m-d H:i:s');
    $updateQuery = "UPDATE vehicle_reservations SET $firstEmptyTimeColumn = ? WHERE id = ? AND ($firstEmptyTimeColumn = '' OR $firstEmptyTimeColumn IS NULL)";
    
    $stmtTime = $connection->prepare($updateQuery);
    $stmtTime->bind_param("si", $currentDateTime, $reservationId);
    $stmtTime->execute();
   
} else {
    echo "No empty column found for reservation ID: $reservationId";
}

    // Array to hold the column names for act columns
// Array to hold the column names for act columns
$actColumns = array(
    'act1' => 'Act1',
    'act2' => 'Act2'
);

// Find the first empty column for act columns
$firstEmptyActColumn = null;
foreach ($actColumns as $columnName => $placeholder) {
    $checkEmptyQuery = "SELECT COUNT(*) as count FROM vehicle_reservations WHERE id = ? AND ($columnName = '' OR $columnName IS NULL)";
    $stmtAct = $connection->prepare($checkEmptyQuery);
    $stmtAct->bind_param("i", $reservationId);
    $stmtAct->execute();
    $result = $stmtAct->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $firstEmptyActColumn = $columnName;
        break;
    }
}

if ($firstEmptyActColumn !== null) {
    // Update the first empty column to 'Approved'
    $updateQuery = "UPDATE vehicle_reservations SET $firstEmptyActColumn = 'Rejected' WHERE id = ? AND ($firstEmptyActColumn = '' OR $firstEmptyActColumn IS NULL)";
    
    $stmtAct = $connection->prepare($updateQuery);
    $stmtAct->bind_param("i", $reservationId);
    $stmtAct->execute();
    
} else {
    echo "No empty column found for reservation ID: $reservationId";
}


    
    

    
try {
    $confirmationMail = new PHPMailer(true);
 // Server settings for the confirmation email
 $confirmationMail->isSMTP();
 $confirmationMail->Host       = 'smtp.gmail.com';
 $confirmationMail->SMTPAuth   = true;
 $confirmationMail->Username   = 'noveelanleighc@gmail.com'; // Replace with your email
 $confirmationMail->Password   = 'owpk sxiv qpoc odkp'; // Replace with your email password
 $confirmationMail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
 $confirmationMail->Port       = 587;



 // Recipient for the confirmation email (Recipient 2)
 $confirmationMail->setFrom('noveelanleighc@gmail.com', "NU Laguna Reservation");
 $confirmationMail->addAddress($userEmail , 'Client'); // Replace with Recipient 2's email and name

 $viewLink  = 'http://localhost/ReservationSiteCopy4/client_pages/vehicle_reservation.php';

 $viewLink  .= '?reservationId=' . urlencode($reservationId);
 $viewLink .= '&recipientName=' . urlencode($recipientName);


 // Email content for confirmation
 $confirmationSubject = 'Reservation Rejected';

 $confirmationContent = "Dear " . $fullName . ",\n\nYour reservation has been rejected.\n\nTrack your reservation here: ".$viewLink.
 // Set the content for the confirmation email
 $confirmationMail->isHTML(false); // Set to true if you want HTML content
 $confirmationMail->Subject = $confirmationSubject;
 $confirmationMail->Body = $confirmationContent;

 // Send the confirmation email to Recipient 2
 $confirmationMail->send();
 $confirmationMail = new PHPMailer(true);

$updateQuery = "UPDATE vehicle_reservations SET status = 'Rejected', admin_remarks = ? WHERE id = ?";
$stmt = $connection->prepare($updateQuery);
$stmt->bind_param("si",$adminRemarks, $reservationId); // Assuming reservation_id is an integer
$stmt->execute();

// Check if the update was successful
if ($stmt->affected_rows > 0) {
    echo "Reservation rejected.";
}else{
    echo"This reservation has already been rejected";
}
 
 

} catch (Exception $e) {
 echo "Message could not be sent. Mailer Error: {$confirmationMail->ErrorInfo}";
}
} else {
// Redirect to the form page if accessed directly without form submission
header("Location: $redirectURL");
exit();
}
?>



