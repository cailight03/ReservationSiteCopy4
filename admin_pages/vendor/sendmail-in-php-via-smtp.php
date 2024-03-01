
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

    $userID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
    $roomId = isset($_POST['room_id']) ? $_POST['room_id'] : '';
    $roomName = isset($_POST['room_name']) ? $_POST['room_name'] : '';
    $fullName = $_POST["fullName"];
    $college = $_POST["college"];
    $activityName = $_POST["activityName"];
    $numOfAttendees = $_POST["numOfAttendees"];
    $date = $_POST["reservation-date"];
    $timeSlot = $_POST["time-slot"];
    $speakerName = $_POST["speakerName"];
    $remarks = $_POST["remarks"];
    $submissionTime = date("Y-m-d H:i:s");
    $uploadDir = "../img/uploads/"; // Specify your upload directory
    $uploadFile = '';
    if (isset($_FILES['fileUpload']) && $_FILES['fileUpload']['error'] == UPLOAD_ERR_OK) {
        $uploadFile = $uploadDir . basename($_FILES['fileUpload']['name']);
    }

    // Save the uploaded file path to the session
    $_SESSION['uploadFilePath'] = $uploadFile;

    
    // Decode the selected items from JSON
    $selectedItems = json_decode($_POST['selectedItems'], true);
    $selectedItemsJSON = json_encode($selectedItems); // Encode the items back to JSON for storage
    
    // Insert query with time_slot instead of start_time and end_time
    $insertQuery = "INSERT INTO reservations (user_id, date_submitted, department, requestor, activity_name, date, time_slot, room_id, room_name, items_needed, remarks, status, num_of_attendees)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)";
    
    $stmt = $connection->prepare($insertQuery);
    $stmt->bind_param("issssssssssi", $userID, $submissionTime, $college, $fullName, $activityName, $date, $timeSlot, $roomId, $roomName, $selectedItemsJSON, $remarks, $numOfAttendees);


if ($stmt->execute()) {
// Insert successful
$stmt->close();

$reservationId = mysqli_insert_id($connection);

// Continue with the rest of your existing code (email sending, confirmation email, etc.)
} else {
// Insert failed
echo "Error: " . $insertQuery . "<br>" . $connection->error;
}

// Close the database connection (if not already closed in your includes file)
$connection->close();

// Fetch colleges from the database
$query = "SELECT name, email FROM org_col_off";
$result = mysqli_query($connection, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

// Build a dynamic switch statement based on fetched colleges
$switchCases = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Convert college_name to uppercase to match your switch cases
    $collegeCode = strtoupper($row['name']);
    $switchCases[$collegeCode] = $row['email'];
}

// Assuming $college contains the selected college

// Check if the selected college exists in the switch cases
if (isset($switchCases[$college])) {
    $recipientEmail = $switchCases[$college];
} else {
    // If the selected college doesn't match any of the cases, set a default email
    $recipientEmail = 'default@example.com';
}

    
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
    $mail->setFrom('noveelanleighc@gmail.com', "School");
    $mail->addAddress($recipientEmail, "department head");


   
    
    //Approve details
    $approveLink = 'http://localhost/ReservationSiteCopy/super_admin_pages/print_page.php';

    
    $approveLink .= '?roomId=' . urlencode($roomId);
    $approveLink .= '&reservationId=' . urlencode($reservationId);
    $approveLink .= '&roomName=' . urlencode($roomName);
    $approveLink .= '&fullName=' . urlencode($fullName);
    $approveLink .= '&college=' . urlencode($college);
    $approveLink .= '&activityName=' . urlencode($activityName);
    $approveLink .= '&numOfAttendees=' . urlencode($numOfAttendees);
    $approveLink .= '&date=' . urlencode($date);
    $approveLink .= '&timeSlot=' . urlencode($timeSlot);
    $approveLink .= '&speakerName=' . urlencode($speakerName);
    $approveLink .= '&remarks=' . urlencode($remarks);
    // Pass submissionTime to print_page.php
$approveLink .= '&submissionTime=' . urlencode($submissionTime);
$approveLink .= '&uploadFilePath=' . urlencode($uploadFile);
$approveLink .= '&selectedItems=' . urlencode(json_encode($selectedItems));






    $rejectLink  = 'http://localhost/ReservationSiteCopy/super_admin_pages/reject_page.php';   // Replace with your actual URL

    $rejectLink .= '?roomId=' . urlencode($roomId);
    $rejectLink .= '&roomName=' . urlencode($roomName);
    $rejectLink .= '&reservationId=' . urlencode($reservationId);
    $rejectLink .= '&fullName=' . urlencode($fullName);
    $rejectLink .= '&college=' . urlencode($college);
    $rejectLink .= '&activityName=' . urlencode($activityName);
    $rejectLink .= '&numOfAttendees=' . urlencode($numOfAttendees);
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
        <p><strong>Reservation #$reservationId</strong></p>
         <p><strong>Form submitted on:</strong> $submissionTime</p>
         <p>Room ID: $roomId</p>
         <p>Room Name: $roomName</p>
         <p>Full Name: $fullName</p>
         <p>Organization/College: $college</p>
         <p>Activity Name: $activityName</p>
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


 $userEmail = $_POST["email"]; // Get the user's email from the form
 // Recipient for the confirmation email (Recipient 2)
 $confirmationMail->setFrom('noveelanleighc@gmail.com', "Your School");
 $confirmationMail->addAddress($userEmail , 'Client'); // Replace with Recipient 2's email and name

 // Email content for confirmation
 $confirmationSubject = 'Reservation Submitted';
 $confirmationContent = "Dear " . $fullName . ",\n\nYour reservation has been submitted.\n\nTrack your reservation here: http://localhost/ReservationSiteCopy/client_pages/view_reservation.php?reservationId=" . $reservationId . "\n\nView Reservation Details";
 // Set the content for the confirmation email
 $confirmationMail->isHTML(false); // Set to true if you want HTML content
 $confirmationMail->Subject = $confirmationSubject;
 $confirmationMail->Body = $confirmationContent;

 // Send the confirmation email to Recipient 2
 $confirmationMail->send();

 echo 'Your request has been submitted. Please check your email for updates.';
 
} catch (Exception $e) {
 echo "Message could not be sent. Please Check E-mail input";
}
} else {
// Redirect to the form page if accessed directly without form submission
header("Location: $redirectURL");
exit();
}
?>

