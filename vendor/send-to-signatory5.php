<?php

include '../config/connection.php';

session_start();



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/src/PHPMailer.php';
require 'vendor/src/Exception.php';
require 'vendor/src/SMTP.php'; // Make sure to adjust the path based on your actual file structure

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    date_default_timezone_set('Asia/Manila');


    // Retrieve form data
    // Fetch the user ID from the session
    $reservationId = isset($_POST['reservationId']) ? $_POST['reservationId'] : '';
    $roomId = isset($_POST['room_id']) ? $_POST['room_id'] : '';
    $categoryId = isset($_POST['category_id']) ? $_POST['category_id'] : '';
    $roomName = isset($_POST['roomName']) ? $_POST['roomName'] : '';
    $adviserEmail =$_POST[ 'adviserEmail'] ? $_POST['adviserEmail'] : '';
    $userType = $_POST[ 'userType'];
    $organization = $_POST['org'];
    $activityType = $_POST['activityType'];
    $fullName = $_POST["fullName"];
    $recipientName = $_POST['recipientName'];
    $userEmail = $_POST["userEmail"];
    $college = $_POST["college"];
    $activityName = $_POST["activityName"];
    $numOfAttendees = $_POST["numOfAttendees"];
    $date = $_POST["reservation-date"];
    $timeSlot = $_POST["time-slot"];
    $speakerName = $_POST["speakerName"];
    $remarks = $_POST["remarks"];
    $submissionTime = $_POST["submissionTime"];
    $uploadDir = "../img/uploads/";

    // Add the code to check and set $uploadFile
    $uploadFilePath = isset($_POST['uploadFilePath']) ? $_POST['uploadFilePath'] : '';


    
    
   
    $selectedItems = isset($_POST['selectedItems']) ? json_decode($_POST['selectedItems'], true) : null;
    
    $updateQuery = "UPDATE reservations SET Sig4 = ?, Act4 = 'Approved', time4 = NOW()  WHERE id = ?";
    

    $stmt = $connection->prepare($updateQuery);
    $stmt->bind_param("si", $recipientName, $reservationId);
    
    
    
    if ($stmt->execute()) {
        // Close the statement
        $stmt->close();
        // Handle successful update
      
    } else {
        echo "Error: " . $updateQuery . "<br>" . $connection->error;
    }


// Assign recipient email based on conditions
if ($categoryId == 2) {
    if (($userType == 'student' || $userType == 'employee') && ($activityType == 'Academic Activity' || $activityType == 'Others Academic' )) {
        // Fetch email from signatories where name = 'NU Laguna Reservation'
        $query = "SELECT name, email FROM signatories WHERE name = 'Academic Director'";
    } elseif (($userType == 'student' ) && ($activityType == 'Non-Academic Activity')) {
        // Fetch email from signatories where name = 'SDAO'
        $query = "SELECT name, email FROM signatories WHERE name = 'Academic Director'";
    } elseif ($userType == 'admin' && $activityType == 'Academic Activity') {
        // Fetch email from signatories where name = 'Sir Rich'
        $query = "SELECT name, email FROM signatories WHERE name = 'Physical Facilities'";
    } elseif ($userType == 'admin' && ($activityType == 'Non-Academic Activity' || $activityType == 'Event')) {
        // Fetch email from signatories where name = 'NU Laguna Reservation'
        $query = "SELECT name, email FROM signatories WHERE name = 'SAD'";
    } else {
        echo "Please select valid Activity Type for you user type";
}
} else {
    if (($userType == 'student' || $userType == 'employee') && $activityType == 'Academic Activity') {
        // Fetch email from signatories where name = 'NU Laguna Reservation'
        $query = "SELECT name, email FROM signatories WHERE name = 'Physical Facilities'";
    } elseif (($userType == 'student' || $userType == 'employee') && ($activityType == 'Non-Academic Activity' || $activityType == 'Event')) {
        // Fetch email from signatories where name = 'SDAO'
        $query = "SELECT name, email FROM signatories WHERE name = 'Academic Director'";
    } elseif ($userType == 'admin' && $activityType == 'Academic Activity') {
        // Fetch email from signatories where name = 'Sir Rich'
        $query = "SELECT name, email FROM signatories WHERE name = 'Physical Facilities'";
    } elseif ($userType == 'admin' && ($activityType == 'Non-Academic Activity' || $activityType == 'Event')) {
        // Fetch email from signatories where name = 'NU Laguna Reservation'
        $query = "SELECT name, email FROM signatories WHERE name = 'Physical Facilities'";
    } else {
        echo "Please select valid Activity Type for you user type";
}}

if (isset($query)) {
    $result = mysqli_query($connection, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $recipientEmail = $row['email'];
        $recipientName = $row['name'];
    } else {
        // Handle query error
        $recipientEmail = 'default@email.com'; // Provide a default recipient email
    }
}


    
  
   
    try {
         // Send email
    $mail = new PHPMailer(true);

       // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'noveelanleighc@gmail.com';
    $mail->Password   = 'owpk sxiv qpoc odkp';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('noveelanleighc@gmail.com', "NU Laguna Reservation");
    $mail->addAddress($recipientEmail,'signatory 2');


   
    
    //Approve details
    $approveLink = 'http://localhost/ReservationSiteCopy4/super_admin_pages/approve_page2.php';

    
    $approveLink .= '?roomId=' . urlencode($roomId);
    $approveLink .= '&roomName=' . urlencode($roomName);
    $approveLink .= '&reservationId=' . urlencode($reservationId);
    $approveLink .= '&category_id=' . urlencode($categoryId);
    $approveLink .= '&fullName=' . urlencode($fullName);
    $approveLink .= '&adviserEmail=' . urlencode($adviserEmail);

    $approveLink .= '&userEmail=' . urlencode($userEmail);
    $approveLink .= '&recipientName=' . urlencode($recipientName);
    $approveLink .= '&college=' . urlencode($college);
    $approveLink .= '&activityName=' . urlencode($activityName);
    $approveLink .= '&activityType=' . urlencode($activityType);
    $approveLink .= '&numOfAttendees=' . urlencode($numOfAttendees);
    $approveLink .= '&userType=' . urlencode($userType);
    $approveLink .= '&org=' . urlencode($organization);
    $approveLink .= '&date=' . urlencode($date);
    $approveLink .= '&timeSlot=' . urlencode($timeSlot);
    $approveLink .= '&speakerName=' . urlencode($speakerName);
    $approveLink .= '&remarks=' . urlencode($remarks);
    // Pass submissionTime to print_page.php
$approveLink .= '&submissionTime=' . urlencode($submissionTime);
$approveLink .= '&uploadFilePath=' . urlencode($uploadFilePath);
$approveLink .= '&selectedItems=' . urlencode(json_encode($selectedItems));






    $rejectLink  = 'http://localhost/ReservationSiteCopy4/super_admin_pages/reject_page.php';   // Replace with your actual URL

    $rejectLink .= '?roomId=' . urlencode($roomId);
    $rejectLink .= '&roomName=' . urlencode($roomName);
    $rejectLink .= '&reservationId=' . urlencode($reservationId);
    $rejectLink .= '&category_id=' . urlencode($categoryId);
    $rejectLink .= '&adviserEmail=' . urlencode($adviserEmail);

    $rejectLink .= '&userEmail=' . urlencode($userEmail);
    $rejectLink .= '&fullName=' . urlencode($fullName);
    $rejectLink .= '&recipientName=' . urlencode($recipientName);
    $rejectLink .= '&college=' . urlencode($college);
    $rejectLink .= '&activityName=' . urlencode($activityName);
    $rejectLink .= '&activityType=' . urlencode($activityType);
    $rejectLink .= '&numOfAttendees=' . urlencode($numOfAttendees);
    $rejectLink .= '&userType=' . urlencode($userType);
    $rejectLink .= '&org=' . urlencode($organization);
    $rejectLink .= '&date=' . urlencode($date);
    $rejectLink .= '&timeSlot=' . urlencode($timeSlot);
    $rejectLink .= '&speakerName=' . urlencode($speakerName);
    $rejectLink .= '&remarks=' . urlencode($remarks);
    // Pass submissionTime to print_page.php
    $rejectLink .= '&submissionTime=' . urlencode($submissionTime);
    $rejectLink  .= '&uploadFilePath=' . urlencode($uploadFilePath);
    $rejectLink  .= '&selectedItems=' . urlencode(json_encode($selectedItems));

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
         <p><strong>Form submitted on:</strong> $submissionTime</p>
         <p><strong>Reservation #$reservationId</strong></p>
        
         <h3>Room Name: $roomName</h3>

        <!-- Requestor Details -->
         <h3>Requestor Details</h3>
         <p>Full Name: $fullName</p>
         <p>Email: $userEmail</p>
         <p>User Type: $userType</p>
         <p>College: $college</p>
         <p>Org: $organization</p>


         <!-- Activity Details -->
         <h3>Activity Details</h3>
         <p>Activity Name: $activityName</p>
         <p>Activity Type: $activityType</p>
         <p>No. of Attendees: $numOfAttendees</p>
         <p>Date: $date</p>
         <p>Time: $timeSlot</p>

         <p>Speaker's Name: $speakerName</p>";

         $htmlContent .= "<h2>Items Needed:</h2><ul>";
            if ($selectedItems !== null) {
                foreach ($selectedItems as $item) {
                    $htmlContent .= "<li>{$item['item']} {$item['quantity']}</li>";
                }
            }
           
            else{
                $htmlContent .= "<p>No items needed</p>";
            }
           


 $htmlContent .= "<p>Remarks: $remarks</p>";
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
 $mail->Subject = 'New Form Submission';
 $mail->Body = $htmlContent;

 $mail->send();

 echo 'Approved. Sent to: ';
 echo $recipientName;
 

} catch (Exception $e) {
 echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}". $e->getMessage();
}
} else {
// Redirect to the form page if accessed directly without form submission
header("Location: $redirectURL");
exit();
}
?>



