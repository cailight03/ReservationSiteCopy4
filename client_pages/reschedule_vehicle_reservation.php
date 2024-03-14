
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

    $vehicleId = isset($_POST['vehicle_id']) ? $_POST['vehicle_id'] : '';
    $reservationId = isset($_POST['reservationId']) ? $_POST['reservationId'] : '';
    $vehicleName = isset($_POST['vehicle_name']) ? $_POST['vehicle_name'] : '';
    $fullName = $_POST["fullName"];
    $purpose = $_POST["purpose"];
    $userEmail = $_POST["userEmail"];
    $office = $_POST["office"];
    $numOfPassengers = $_POST["numOfPassengers"];
    $date = $_POST["reservation-date"];
    $timeSlot = $_POST["time-slot"];
    $destination = $_POST["destination"];
    $submissionTime = date("Y-m-d H:i:s");

    // Add the code to check and set $uploadFile
    $uploadFilePath = isset($_POST['uploadFilePath']) ? $_POST['uploadFilePath'] : '';
    
    $query = "SELECT name, email FROM signatories WHERE name = 'NU Laguna Reservation'";
    if (isset($query)) {
        $result = mysqli_query($connection, $query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $recipientEmail = $row['email'];
            $recipientName = $row['name'];
        } else {
            // Handle query error
            $recipientEmail = 'default@email.com'; // Provide a default recipient email
        }}
    
   // Insert query with time_slot instead of start_time and end_time
   $UpdateQuery = "UPDATE vehicle_reservations SET date = ?, time = ?,status = 'pending',
   Act1 = NULL, 
   Act2 = NULL,
   time1 = NULL, 
   time2 = NULL,
   Sig1 = NULL, 
   Sig2 = NULL,

   WHERE id = ?";

// Prepare the query
$stmt = $connection->prepare($UpdateQuery);

// Bind parameters
$stmt->bind_param("ssi", $date, $timeSlot, $reservationId);

// Execute the query
$stmt->execute();


if ($stmt->execute()) {
// Insert successful

$stmt->close();




// Continue with the rest of your existing code (email sending, confirmation email, etc.)
} else {
// Insert failed
echo "Error: " . $insertQuery . "<br>" . $connection->error;
}

// Close the database connection (if not already closed in your includes file)
$connection->close();




    
    // Send email
    $mail = new PHPMailer(true);

    try {
       // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'noveelanleighc@gmail.com';
    $mail->Password   = 'owpk sxiv qpoc odkp';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom("noveelanleighc@gmail.com", "NU Laguna Reservation");
    $mail->addAddress($recipientEmail, "Signatory 1");


   
    
    //Approve details
    $approveLink = 'http://localhost/ReservationSiteCopy4/super_admin_pages/vehicle_approve_page.php';

    
    $approveLink .= '?vehicle_id=' . urlencode($vehicleId);
    $approveLink .= '&userEmail=' . urlencode($userEmail);
    $approveLink .= '&recipientEmail=' . urlencode($recipientEmail);
    $approveLink .= '&recipientName=' . urlencode($recipientName);
    $approveLink .= '&reservationId=' . urlencode($reservationId);
    $approveLink .= '&vehicle_name=' . urlencode($vehicleName);
    $approveLink .= '&fullName=' . urlencode($fullName);
    $approveLink .= '&office=' . urlencode($office);
    $approveLink .= '&destination=' . urlencode($destination);
    $approveLink .= '&purpose=' . urlencode($purpose);
    $approveLink .= '&numOfPassengers=' . urlencode($numOfPassengers);
    $approveLink .= '&date=' . urlencode($date);
    $approveLink .= '&timeSlot=' . urlencode($timeSlot);
    $approveLink .= '&submissionTime=' . urlencode($submissionTime);
    $approveLink .= '&uploadFilePath=' . urlencode($uploadFilePath);






    $rejectLink  = 'http://localhost/ReservationSiteCopy4/super_admin_pages/reject_page.php';   // Replace with your actual URL

    $rejectLink .= '?vehicle_id=' . urlencode($vehicleId);
    $rejectLink .= '&userEmail=' . urlencode($userEmail);
    $rejectLink .= '&recipientEmail=' . urlencode($recipientEmail);
    $rejectLink .= '&recipientName=' . urlencode($recipientName);
    $rejectLink .= '&reservationId=' . urlencode($reservationId);
    $rejectLink .= '&vehicle_name=' . urlencode($vehicleName);
    $rejectLink .= '&fullName=' . urlencode($fullName);
    $rejectLink .= '&office=' . urlencode($office);
    $rejectLink .= '&destination=' . urlencode($destination);
    $rejectLink .= '&purpose=' . urlencode($purpose);
    $rejectLink .= '&numOfPassengers=' . urlencode($numOfPassengers);
    $rejectLink .= '&date=' . urlencode($date);
    $rejectLink .= '&timeSlot=' . urlencode($timeSlot);
    $rejectLink .= '&submissionTime=' . urlencode($submissionTime);
    $rejectLink .= '&uploadFilePath=' . urlencode($uploadFilePath);


     // Email content
     $htmlContent = "
     <!DOCTYPE html>
     <html lang='en'>
     <head>
         <meta charset='UTF-8'>
         <meta name='viewport' content='width=device-width, initial-scale=1.0'>
       
         <title>Email Content</title>

        
     </head>
     <body>
        <p><strong>Reservation #$reservationId</strong></p>
         <p><strong>Form submitted on:</strong> $submissionTime</p>
         <p>Vehicle: $vehicleName</p>
         <p>Full Name: $fullName</p>
         <p>Office: $office</p>
         <p>Purpose: $purpose</p>
         <p>No. of Passengers: $numOfPassengers</p>
         <p>Date: $date</p>
         <p>Time: $timeSlot</p>";

 


 // Attach photo to email
 if (!empty($uploadFilePath)) {
    $mail->addEmbeddedImage($uploadFilePath, 'Uploaded_Photo', 'Uploaded_Photo.jpg');
    $htmlContent .= "<img src='cid:Uploaded_Photo' alt='Uploaded Photo' style='max-width: 100%; height: auto;'>";
} else {
    $htmlContent .= "<p>No uploaded photo.</p>";
}

 $htmlContent .=" <p>
 <a href='$approveLink' style='background-color: green; color: white; padding: 10px; text-decoration: none; display: inline-block; border-radius: 5px;'>
     Approve
 </a>
 <a href='$rejectLink' style='background-color: red; color: white; padding: 10px; text-decoration: none; display: inline-block; border-radius: 5px; margin-left: 10px;'>
     Reject
 </a>
</p>
";

 $htmlContent .= "</body></html>";

 // Content
 $mail->isHTML(true);
 $mail->Subject = 'New Vehicle Form Submission';
 $mail->Body = $htmlContent;

 $mail->send();
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
 $confirmationSubject = 'Reservation Rescheduled';

 $confirmationContent = "Dear " . $fullName . ",\n\nYour reservation has been rescheduled.\n\nTrack your reservation here: ".$viewLink.
 $confirmationMail->isHTML(false); // Set to true if you want HTML content
 $confirmationMail->Subject = $confirmationSubject;
 $confirmationMail->Body = $confirmationContent;

 // Send the confirmation email to Recipient 2
 $confirmationMail->send();

 echo 'Your request has been submitted. Please check your email for updates.'.$recipientEmail;
 
} catch (Exception $e) {
 echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
} else {
// Redirect to the form page if accessed directly without form submission
header("Location: $redirectURL");
exit();
}
?>

