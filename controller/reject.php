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
    $adviserEmail = isset($_POST['adviserEmail']) ? $_POST['adviserEmail'] : '';

    $fullName = isset($_POST['fullName']) ? $_POST['fullName'] : '';
    $selectedItems = isset($_GET['selectedItems']) ? json_decode($_GET['selectedItems'], true) : null;

    // Define your SQL query to update the next available column
$updateQuery = "UPDATE reservations SET ";

// Array to hold the column names and their corresponding placeholders for binding parameters
$columns = array(
    'sig1' => 'Sig1',
    'sig2' => 'Sig2',
    'sig3' => 'Sig3',
    'sig4' => 'Sig4',
    'sig5' => 'Sig5'
);

// Find the first empty column
$firstEmptyColumn = null;
foreach ($columns as $columnName => $placeholder) {
    $checkEmptyQuery = "SELECT COUNT(*) as count FROM reservations WHERE id = ? AND ($columnName = '' OR $columnName IS NULL)";
    $stmt = $connection->prepare($checkEmptyQuery);
    $stmt->bind_param("i", $reservationId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $firstEmptyColumn = $columnName;
        break;
    }
}

if ($firstEmptyColumn !== null) {
    // Update the first empty column to $recipientName
    $updateQuery = "UPDATE reservations SET $firstEmptyColumn = ? WHERE id = ?";
    $stmt = $connection->prepare($updateQuery);
    $stmt->bind_param("si", $recipientName, $reservationId);
    $stmt->execute();
} else {
    echo "No empty column found.";
}

// Array to hold the column names for act columns
$actColumns = array(
    'act1' => 'Act1',
    'act2' => 'Act2',
    'act3' => 'Act3',
    'act4' => 'Act4',
    'act5' => 'Act5',
    'act6' => 'Act6'
);

// Find the first empty column for act columns
$firstEmptyActColumn = null;
foreach ($actColumns as $columnName => $placeholder) {
    $checkEmptyQuery = "SELECT COUNT(*) as count FROM reservations WHERE id = ? AND ($columnName = '' OR $columnName IS NULL)";
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
    $updateQuery = "UPDATE reservations SET $firstEmptyActColumn = 'Approved' WHERE id = ?";
    
    $stmtAct = $connection->prepare($updateQuery);
    $stmtAct->bind_param("i", $reservationId);
    $stmtAct->execute();
}else{
    echo "No empty column found.";
}

$timeColumns = array(
    'time1' => 'time1',
    'time2' => 'time2',
    'time3' => 'time3',
    'time4' => 'time4',
    'time5' => 'time5',
    'time6' => 'time6'
);

// Find the first empty column for time columns
$firstEmptyTimeColumn = null;
foreach ($timeColumns as $columnName => $placeholder) {
    $checkEmptyQuery = "SELECT COUNT(*) as count FROM reservations WHERE id = ? AND ($columnName = '' OR $columnName IS NULL)";
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
    $updateQuery = "UPDATE reservations SET $firstEmptyTimeColumn = ? WHERE id = ?";
    
    $stmtTime = $connection->prepare($updateQuery);
    $stmtTime->bind_param("si", $currentDateTime, $reservationId);
    $stmtTime->execute();
}else{
    echo "No empty column found.";
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

 $viewLink  = 'http://localhost/ReservationSiteCopy4/client_pages/view_reservation.php';

 $viewLink  .= '?reservationId=' . urlencode($reservationId);
 $viewLink .= '&recipientName=' . urlencode($recipientName);
 $viewLink .= '&adviserEmail=' . urlencode($adviserEmail);
 $viewLink .= '&userEmail=' . urlencode($userEmail);



 // Email content for confirmation
 $confirmationSubject = 'Reservation Submitted';

 $confirmationContent = "Dear " . $fullName . ",\n\nYour reservation has been rejected.\n\nTrack your reservation here: ".$viewLink.
 // Set the content for the confirmation email
 $confirmationMail->isHTML(false); // Set to true if you want HTML content
 $confirmationMail->Subject = $confirmationSubject;
 $confirmationMail->Body = $confirmationContent;

 // Send the confirmation email to Recipient 2
 $confirmationMail->send();
 $confirmationMail = new PHPMailer(true);

$updateQuery = "UPDATE reservations SET status = 'Rejected', admin_remarks = ? WHERE id = ?";
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



