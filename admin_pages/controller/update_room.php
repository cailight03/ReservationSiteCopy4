<?php
// update_room.php
include '../../config/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Handle form data (room details, etc.)
    if (isset($_POST['room_id'], $_POST['room_name'], $_POST['room_description'])) {
        // Sanitize input data
        $roomId = mysqli_real_escape_string($connection, $_POST['room_id']);
        $roomName = mysqli_real_escape_string($connection, $_POST['room_name']);
        $roomDescription = mysqli_real_escape_string($connection, $_POST['room_description']);
        $selectedImages = isset($_POST['selected_images']) ? explode(',', $_POST['selected_images']) : [];
        

        // Use prepared statement to prevent SQL injection
        $updateQuery = $connection->prepare("UPDATE rooms SET room_name = ?, room_description = ? WHERE id = ?");
        $updateQuery->bind_param("ssi", $roomName, $roomDescription, $roomId);

        if ($updateQuery->execute()) {
            // Room details updated successfully

             // Delete selected images
    foreach ($selectedImages as $imageName) {
        $imagePath = "../../img/room_img/".$roomName."/" . $imageName;

        // Check if the file exists before attempting to delete
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
            // Move the uploaded files to the room-specific folder
            $targetDirectory = "C:/xampp/htdocs/ReservationSite/img/room_img/$roomName";

            foreach ($_FILES["new_image"]["name"] as $index => $filename) {
                $targetFile = $targetDirectory . DIRECTORY_SEPARATOR . basename($filename);

                if (move_uploaded_file($_FILES["new_image"]["tmp_name"][$index], $targetFile)) {
                    // File uploaded successfully
                } else {
                    // Error uploading file
                }
            }

            // Redirect to the room listing page
            header('Location: ../manage_rooms.php?update=success');
            exit();
        } else {
            // Error updating room details
            die("Error updating room: " . $connection->error);
        }
    } else {
        // Form fields not set
        die("Invalid form submission");
    }
} else {
    // Not a POST request
    die("Invalid request method");
}
?>
