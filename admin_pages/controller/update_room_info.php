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
    $oldRoomData = [];

    // Retrieve old room data for audit trail
    $getOldRoomDataQuery = "SELECT room_name, room_description, room_img_path FROM rooms WHERE id = ?";
    $stmt = $connection->prepare($getOldRoomDataQuery);
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    $stmt->bind_result($oldRoomName, $oldRoomDescription, $oldRoomImgPath);

    if ($stmt->fetch()) {
        $oldRoomData['room_name'] = $oldRoomName;
        $oldRoomData['room_description'] = $oldRoomDescription;
        $oldRoomData['room_img_path'] = $oldRoomImgPath;
    }

    $stmt->close();

    // Prepare new room data
    $roomName = $_POST['room_name'];
    $roomDescription = $_POST['room_description'];
    $newRoomData = [
        'room_name' => $roomName,
        'room_description' => $roomDescription
    ];

    // Check if a new image is uploaded
    if ($_FILES['room_image']['size'] > 0) {
        $uploadDir = '../../img/rooms/';
        $uploadFile = $uploadDir . basename($_FILES['room_image']['name']);

        if (move_uploaded_file($_FILES['room_image']['tmp_name'], $uploadFile)) {
            $newRoomData['room_img_path'] = $uploadFile;
        } else {
            // Handle file upload error
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    } else {
        // Keep the existing image path if no new image is uploaded
        $newRoomData['room_img_path'] = $_POST['existing_image_path'];
    }

    // Update room information in the database
    $updateQuery = "UPDATE rooms SET room_name = ?, room_description = ?, room_img_path = ? WHERE id = ?";
    $stmt = $connection->prepare($updateQuery);
    $stmt->bind_param("sssi", $roomName, $roomDescription, $newRoomData['room_img_path'], $roomId);

    if ($stmt->execute()) {
        // Room information updated successfully

        // Log the audit trail
        $userId = $_SESSION['user_id'];
        $action = "UPDATE";
        $timestamp = date("Y-m-d H:i:s");
        $oldValues = json_encode($oldRoomData);
        $newValues = json_encode($newRoomData);

        $insertAuditQuery = "INSERT INTO room_audit (room_id, action, old_value, new_value, timestamp, user_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($insertAuditQuery);
        $stmt->bind_param("issssi", $roomId, $action, $oldValues, $newValues, $timestamp, $userId);
        $stmt->execute();

        // Redirect to the room information page after successful update
        header("Location: room_info.php?room_id=$roomId");
        exit();
    } else {
        // Handle update error
        echo "Error updating record: " . $connection->error;
    }
}
?>
