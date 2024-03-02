<?php
include '../../config/connection.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: ../../index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form data when form is submitted
    $roomId = $_POST['room_id'];
    $roomName = $_POST['room_name'];
    $roomDescription = $_POST['room_description'];

    // Check if a new image is uploaded
    if ($_FILES['room_image']['size'] > 0) {
        $uploadDir = '../../img/rooms/';
        $uploadFile = $uploadDir . basename($_FILES['room_image']['name']);

        if (move_uploaded_file($_FILES['room_image']['tmp_name'], $uploadFile)) {
            $roomImgPath = $uploadFile;
        } else {
            // Handle file upload error
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    } else {
        // Keep the existing image path if no new image is uploaded
        $roomImgPath = $_POST['existing_image_path'];
    }

    // Update room information in the database
    $updateQuery = "UPDATE rooms SET room_name = '$roomName', room_description = '$roomDescription', room_img_path = '$roomImgPath' WHERE id = $roomId";
    if ($connection->query($updateQuery) === TRUE) {
        // Redirect to the room information page after successful update
        header("Location: room_info.php?room_id=$roomId");
        exit();
    } else {
        // Handle update error
        echo "Error updating record: " . $connection->error;
    }
}
?>