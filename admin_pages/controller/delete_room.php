<?php
// delete_room.php

// Include your database connection file
include '../../config/connection.php';

// Check if the room_id parameter is set
if (isset($_GET['room_id'])) {
    $roomId = $_GET['room_id'];

    // Retrieve the room's name
    $getRoomNameQuery = "SELECT room_name FROM rooms WHERE id = ?";
    $stmt = $connection->prepare($getRoomNameQuery);
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    $roomNameResult = $stmt->get_result();

    if ($roomNameResult->num_rows > 0) {
        $roomData = $roomNameResult->fetch_assoc();
        $roomName = $roomData['room_name'];

        // Prepare and execute the DELETE query
        $deleteRoomQuery = "DELETE FROM rooms WHERE id = ?";
        $stmt = $connection->prepare($deleteRoomQuery);
        $stmt->bind_param("i", $roomId);

        if ($stmt->execute()) {
            // Deletion successful, log the audit trail
            session_start();
            $userId = $_SESSION['user_id']; // Assuming you have user authentication 
            $action = "DELETE";
            $timestamp = date("Y-m-d H:i:s");
            $oldValues = ""; // No old values for deletion
            $newValues = "Room Name: $roomName";

            // Insert the audit trail record
            $auditQuery = "INSERT INTO room_audit (room_id, room_name, action, old_value, new_value, timestamp, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($auditQuery);
            $stmt->bind_param("isssssi", $roomId, $roomName, $action, $oldValues, $newValues, $timestamp, $userId);
            $stmt->execute();

            // Delete the corresponding folder 
            $roomImagePath = "../../img/room_img/" . $roomName . "/";
            if (file_exists($roomImagePath)) { 
                // Delete all files in the directory
                $files = glob($roomImagePath . '*');
                foreach ($files as $file) {
                    unlink($file);
                }

                // Remove the directory 
                rmdir($roomImagePath);
            }

            // Redirect to the page where the user came from
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            // Deletion failed, display an error message
            die("Error deleting room: " . $connection->error);
        }
    } else {
        // Unable to fetch room name, display an error message
        die("Error fetching room data: Room not found");
    }
} else {
    // If room_id is not set, redirect to an error page or the home page
    header('Location: ../../error.php');
    exit();
}
?>
