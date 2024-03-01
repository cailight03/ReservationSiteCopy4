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
    $roomId = isset($_POST['room_id']) ? $_POST['room_id'] : '';
    $roomName = isset($_POST['roomName']) ? $_POST['roomName'] : '';
    $userType = $_POST[ 'userType'];
    $categoryId = $_POST['category_id'];
    $organization = $_POST['org'];
    $recipientName = $_POST['recipientName'];
    $activityType = $_POST['activityType'];
    $fullName = $_POST["fullName"];
    $userEmail = $_POST["userEmail"];
    $college = $_POST["college"];
    $activityName = $_POST["activityName"];
    $numOfAttendees = $_POST["numOfAttendees"];
    $date = $_POST["reservation-date"];
    $timeSlot = $_POST["time-slot"];
    $speakerName = $_POST["speakerName"];
    $remarks = $_POST["remarks"];
    $submissionTime = date("Y-m-d H:i:s");
    $uploadDir = "../img/uploads/";

    // Add the code to check and set $uploadFile
    $uploadFilePath = isset($_POST['uploadFilePath']) ? $_POST['uploadFilePath'] : '';


    
    
   
    $selectedItems = isset($_POST['selectedItems']) ? json_decode($_POST['selectedItems'], true) : null;

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

    // Check if the update was successful
    if ($stmt->affected_rows > 0) {
       
        // Now find the second empty column
        $secondEmptyColumn = null;
        foreach ($columns as $columnName => $placeholder) {
            if ($columnName !== $firstEmptyColumn) {
                $checkEmptyQuery = "SELECT COUNT(*) as count FROM reservations WHERE id = ? AND ($columnName = '' OR $columnName IS NULL)";
                $stmt = $connection->prepare($checkEmptyQuery);
                $stmt->bind_param("i", $reservationId);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                if ($row['count'] > 0) {
                    $secondEmptyColumn = $columnName;
                    break;
                }
            }
        }

        if ($secondEmptyColumn !== null) {
            // Update the second empty column to 'Physical Facilities'
            $updateQuery = "UPDATE reservations SET $secondEmptyColumn = 'Physical Facilities' WHERE id = ?";
            $stmt = $connection->prepare($updateQuery);
            $stmt->bind_param("i", $reservationId);
            $stmt->execute();

            // Check if the update was successful
            if ($stmt->affected_rows > 0) {
                
            } else {
                echo "Failed to update second empty column.";
            }
        } else {
            echo "No second empty column found.";
        }
    } else {
        echo "Failed to update first empty column.";
    }
} else {
    echo "No empty column found.";
}


    
 


    $query = "SELECT name, email FROM signatories WHERE name = 'Physical Facilities'";

$result = mysqli_query($connection, $query);
if ($result) {
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $recipientEmail = $row['email'];
        $recipientName = $row['name'];
    } else {
        // Handle if no records found
        $recipientEmail = 'default@email.com'; // Provide a default recipient email
        $recipientName = 'Default Name'; // Provide a default recipient name or handle it according to your logic
    }
    mysqli_free_result($result);
} else {
    // Handle query error
    $recipientEmail = 'default@email.com'; // Provide a default recipient email
    $recipientName = 'Default Name'; // Provide a default recipient name or handle it according to your logic
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
    $mail->setFrom('noveelanleighc@gmail.com', "NU Laguna Reservation");
    $mail->addAddress($recipientEmail,'signatory 2');


   
    
    //Approve details
    $approveLink = 'http://localhost/ReservationSiteCopy4/super_admin_pages/physical_facilities_page.php';

    
    $approveLink .= '?roomId=' . urlencode($roomId);
    $approveLink .= '&roomName=' . urlencode($roomName);
    $approveLink .= '&reservationId=' . urlencode($reservationId);
    $approveLink .= '&category_id=' . urlencode($categoryId);
    $approveLink .= '&fullName=' . urlencode($fullName);
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
    $htmlContentSignatory1 .= "<p>No uploaded photo.</p>";
}

 $htmlContent .=" <p>
 <a href='$approveLink' style='background-color: green; color: white; padding: 10px; text-decoration: none; display: inline-block; border-radius: 5px;'>
     View Reservation Details
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

$updateQuery = "UPDATE reservations SET status = 'Approved' WHERE id = ?";
$stmt = $connection->prepare($updateQuery);
$stmt->bind_param("i", $reservationId); // Assuming reservation_id is an integer
$stmt->execute();

// Check if the update was successful
if ($stmt->affected_rows > 0) {
   
}else{
    echo"updated na";
}

  

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


 // Email content for confirmation
 $confirmationSubject = 'Reservation Submitted';

 $confirmationContent = "Dear " . $fullName . ",\n\nYour reservation has been Approved.\n\nTrack your reservation here: ".$viewLink.
 // Set the content for the confirmation email
 $confirmationMail->isHTML(false); // Set to true if you want HTML content
 $confirmationMail->Subject = $confirmationSubject;
 $confirmationMail->Body = $confirmationContent;

 // Send the confirmation email to Recipient 2
 $confirmationMail->send();

 echo 'Approved Successfully.';
 
 

} catch (Exception $e) {
 echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
} else {
// Redirect to the form page if accessed directly without form submission
header("Location: $redirectURL");
exit();
}
?>



