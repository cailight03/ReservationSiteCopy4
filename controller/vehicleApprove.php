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
 $viewLink .= '&userEmail=' . urlencode($userEmail);


 // Email content for confirmation
 $confirmationSubject = 'Reservation Approved';

 $confirmationContent = "Dear " . $fullName . ",\n\nYour reservation has been Approved.\n\nTrack your reservation here: ".$viewLink.
 // Set the content for the confirmation email
 $confirmationMail->isHTML(false); // Set to true if you want HTML content
 $confirmationMail->Subject = $confirmationSubject;
 $confirmationMail->Body = $confirmationContent;

 // Send the confirmation email to Recipient 2
 $confirmationMail->send();
 $confirmationMail = new PHPMailer(true);

$updateQuery = "UPDATE vehicle_reservations SET status = 'Approved', admin_remarks = ?, Sig2 = ?, Act2 = 'Approved', time2 = NOW() WHERE id = ?";
$stmt = $connection->prepare($updateQuery);
$stmt->bind_param("ssi",$adminRemarks, $recipientName, $reservationId); // Assuming reservation_id is an integer
$stmt->execute();

// Check if the update was successful
if ($stmt->affected_rows > 0) {
    echo "Reservation Approved.";
}else{
    echo"This reservation has already been approved";
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



