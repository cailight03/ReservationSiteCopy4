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
    
   
    $roomId = isset($_POST['room_id']) ? $_POST['room_id'] : '';
    $categoryId = isset($_POST['category_id']) ? $_POST['category_id'] : '';
    $roomName = isset($_POST['room_name']) ? $_POST['room_name'] : '';
    $userType = $_POST[ 'userType'];
    $organization = $_POST['org'];
    $adviserEmail =$_POST[ 'adviserEmail'] ? $_POST['adviserEmail'] : '';
    $activityType = $_POST['activityType'];
    $fullName = $_POST["fullName"];
    $userEmail = $_POST["email"];
    $college = $_POST["college"];
    $activityName = $_POST["activityName"];
    $numOfAttendees = $_POST["numOfAttendees"];
    $date = $_POST["reservation-date"];
    $timeSlot = $_POST["time-slot"];
    $speakerName = $_POST["speakerName"];
    $remarks = $_POST["remarks"];
    $submissionTime = date("Y-m-d H:i:s");
    $uploadDir = "../img/uploads/";
    $uploadFile = $uploadDir . basename($_FILES['fileUpload']['name']);
    $_SESSION['uploadFilePath'] = $uploadFile;


    
    
    // Decode the selected items from JSON
    $selectedItems = json_decode($_POST['selectedItems'], true);
    $selectedItemsJSON = json_encode($selectedItems); // Encode the items back to JSON for storage
    



    if ($userType == 'student') {
        // If user type is student, recipient is adviser's email
        $recipientEmail = $adviserEmail;
        $recipientName = $adviserEmail;
    } elseif ($userType == 'employee') {
        // If user type is employee, fetch the email from the database based on college
        $query = "SELECT name, email FROM signatories WHERE name = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $college);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $recipientEmail = $row['email'];
            $recipientName = $row['name'];
        } else {
            // Handle if organization email is not found
            $recipientEmail = 'default@email.com'; // Provide a default email or handle it according to your logic
        }
    } elseif ($userType == 'admin') {
        // If user type is admin, check activity type
        if ($activityType == 'Event' ||  $activityType == 'Org Activity') {
            // If activity type is event, use specific admin email
            $query = "SELECT name, email FROM signatories WHERE name = 'SDAO'";
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
        } else {
            // If activity type is not event, use general admin email
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
                }
            }
        }
    } else {
        // Handle other user types if needed
        $recipientEmail = 'default@email.com';
    }



// Insert query with time_slot instead of start_time and end_time
$insertQuery = "INSERT INTO reservations (date_submitted, department, requestor,speakerName, organization, userType, activityType, activity_name, date, time_slot, room_id, room_name, items_needed, remarks, status, num_of_attendees,uploadFilePath)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?,?)";

$stmt = $connection->prepare($insertQuery);
$stmt->bind_param("ssssssssssssssis",$submissionTime,$college, $fullName, $speakerName, $organization, $userType, $activityType, $activityName, $date, $timeSlot, $roomId, $roomName, $selectedItemsJSON, $remarks, $numOfAttendees,$uploadFile);


if ($stmt->execute()) {
    // Close the statement
$stmt->close();
$reservationId = mysqli_insert_id($connection);
} 


else {
    echo "Error: " . $insertQuery . "<br>" . $connection->error;
}
    

// Close the database connection
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
    $mail->setFrom('noveelanleighc@gmail.com', "NU Laguna Reservation");
    $mail->addAddress($recipientEmail,'signatory 1');


   
    
    //Approve details
    $approveLink = 'http://localhost/ReservationSiteCopy4/super_admin_pages/print_page.php';

    
    $approveLink .= '?roomId=' . urlencode($roomId);
    $approveLink .= '&reservationId=' . urlencode($reservationId);
    $approveLink .= '&category_id=' . urlencode($categoryId);
    $approveLink .= '&adviserEmail=' . urlencode($adviserEmail);
    $approveLink .= '&recipientName=' . urlencode($recipientName);
    $approveLink .= '&roomName=' . urlencode($roomName);
    $approveLink .= '&fullName=' . urlencode($fullName);
    $approveLink .= '&userEmail=' . urlencode($userEmail);
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
$approveLink .= '&uploadFilePath=' . urlencode($uploadFile);
$approveLink .= '&selectedItems=' . urlencode(json_encode($selectedItems));






    $rejectLink  = 'http://localhost/ReservationSiteCopy4/super_admin_pages/reject_page.php';   // Replace with your actual URL

    $rejectLink .= '?roomId=' . urlencode($roomId);
    $rejectLink .= '&roomName=' . urlencode($roomName);
    $rejectLink .= '&reservationId=' . urlencode($reservationId);
    $rejectLink .= '&adviserEmail=' . urlencode($adviserEmail);
    $rejectLink .= '&category_id=' . urlencode($categoryId);
    $rejectLink .= '&recipientName=' . urlencode($recipientName);
    $rejectLink .= '&fullName=' . urlencode($fullName);
    $rejectLink .= '&userEmail=' . urlencode($userEmail);
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
    $rejectLink  .= '&uploadFilePath=' . urlencode($uploadFile);
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
         // Ensure $item is an array before using it
         if (is_array($item)) {
             $itemName = $item['item'];
             $itemQuantity = $item['quantity'];
             $htmlContent .= "<li>$itemName $itemQuantity</li>";
         }
     }
 }
 $htmlContent .= "</ul>";

 $htmlContent .= "<p>Remarks: $remarks</p>";
// Attach photo to email
if (move_uploaded_file($_FILES['fileUpload']['tmp_name'], $uploadFile)) {
    $mail->addEmbeddedImage($uploadFile, 'Uploaded_Photo', 'Uploaded_Photo.jpg');
    $htmlContent .= "<p>ID Picture:</p>";
    $htmlContent .= "<img src='cid:Uploaded_Photo' alt='Uploaded Photo' style='max-width: 100%; height: auto;'>";
} else {
    $htmlContent .= "<p>Upload failed.</p>";
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
 $viewLink .= '&adviserEmail=' . urlencode($adviserEmail);
 $viewLink .= '&userEmail=' . urlencode($userEmail);
 $viewLink .= '&category_id=' . urlencode($categoryId);
 $viewLink .= '&selectedItems=' . urlencode(json_encode($selectedItems));


 // Email content for confirmation
 $confirmationSubject = 'Reservation Submitted';

 $confirmationContent = "Dear " . $fullName . ",\n\nYour reservation has been submitted.\n\nTrack your reservation here: ".$viewLink.

 $confirmationMail->isHTML(false); // Set to true if you want HTML content
 $confirmationMail->Subject = $confirmationSubject;
 $confirmationMail->Body = $confirmationContent;

 // Send the confirmation email to Recipient 2
 $confirmationMail->send();

 echo 'Your request has been submitted. Please check your email for updates.';
 echo $recipientEmail;
 
 
} catch (Exception $e) {
 echo "Please input a valid email address. {$mail->ErrorInfo}";
 
}
} else {
// Redirect to the form page if accessed directly without form submission
header("Location: $redirectURL");
exit();
}
?>



