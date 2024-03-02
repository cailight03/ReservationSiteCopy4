<?php
// delete_room.php

// Include your database connection file
include '../../config/connection.php';

// Check if the room_id parameter is set
if (isset($_GET['room_id'])) {
    $roomId = $_GET['room_id'];

    // Retrieve the room's name
    $getRoomNameQuery = "SELECT room_name FROM rooms WHERE id = $roomId";
    $roomNameResult = $connection->query($getRoomNameQuery);

    if ($roomNameResult) {
        $roomData = $roomNameResult->fetch_assoc();
        $roomName = $roomData['room_name'];

        // Prepare and execute the DELETE query
        $deleteRoomQuery = "DELETE FROM rooms WHERE id = $roomId";
        $result = $connection->query($deleteRoomQuery);

        if ($result) {
            // Insert log entry for the delete action ---------------------> added for logs
            $userId = $_SESSION['user_id'];
            $action = "delete";
            $log_query = "INSERT INTO audit_logs (user_id, reservation_id, action) VALUES ('$userId', '$roomId', '$action')";
            mysqli_query($connection, $log_query);


            // Deletion successful, delete the corresponding folder 
            $roomImagePath = "../../img/room_img/" . $roomName . "/";

            if (file_exists($roomImagePath)) { 
                // Delete all files in the directory
                $files = glob($roomImagePath . '*');
                foreach ($files as $file) {
                    unlink($file);
                }

                // Remove the directory 
                if (rmdir($roomImagePath)) {
                    // Redirect to the page where the user came from
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                    exit();
                } else {
                    // Directory removal failed, display an error message
                    die("Error deleting room folder: Unable to remove directory");
                }
            } else {
                // Directory does not exist, no need to remove
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
        } else {
            // Deletion failed, display an error message
            die("Error deleting room: " . $connection->error);
        }
    } else {
        // Unable to fetch room name, display an error message
        die("Error fetching room data: " . $connection->error);
    }
} else {
    // If room_id is not set, redirect to an error page or the home page
    header('Location: ../../error.php');
    exit();
}
?>
