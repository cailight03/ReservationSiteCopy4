<?php
include '../../config/connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $roomName = $_POST['room_name'];
    $roomDescription = $_POST['room_description'];
    $categoryId = $_POST['category_id'];  // Assuming you have a hidden input field for category_id

    // Create a folder for room images
    $roomImagePath = "../../img/room_img/" . $roomName . "/";
    if (!file_exists($roomImagePath)) {
        mkdir($roomImagePath, 0777, true);
    }

    // Upload room images to the folder
    $uploadedImages = [];
    foreach ($_FILES['room_images']['name'] as $key => $value) {
        $tempName = $_FILES['room_images']['tmp_name'][$key];
        $imageName = basename($_FILES['room_images']['name'][$key]);
        $targetPath = $roomImagePath . $imageName;

        if (move_uploaded_file($tempName, $targetPath)) {
            $uploadedImages[] = $targetPath;
        } else {
            // Handle upload failure if needed
        }
    }

    // Insert data into the rooms table
    $insertQuery = "INSERT INTO rooms (room_name, room_description, room_img_path, category_id) VALUES (?, ?, ?, ?)";
    $stmt = $connection->prepare($insertQuery);

    if ($stmt) {
        $roomImagePathForDB = "../../img/room_img/" . $roomName . "/";
        $stmt->bind_param("sssi", $roomName, $roomDescription, $roomImagePathForDB, $categoryId);

        if ($stmt->execute()) {
            // Insert successful, log the audit trail
            session_start();
            $userId = $_SESSION['user_id']; // Assuming you have user authentication 
            $action = "INSERT";
            $timestamp = date("Y-m-d H:i:s");
            $oldValues = ""; // No old values for insertion
            $newValues = "Name: $roomName, Description: $roomDescription, Image Path: $roomImagePathForDB, Category ID: $categoryId";

            // Insert the audit trail record
            $auditQuery = "INSERT INTO room_audit (room_id, room_name, room_description, room_img_path, category_id, action, old_value, new_value, timestamp, user_id) VALUES (LAST_INSERT_ID(), ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $auditStmt = $connection->prepare($auditQuery);
            $auditStmt->bind_param("ssssssssi", $roomName, $roomDescription, $roomImagePathForDB, $categoryId, $action, $oldValues, $newValues, $timestamp, $userId);
            $auditStmt->execute();
            $auditStmt->close();

            // Redirect after successful insertion
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            // Insert failed, handle the error
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        // Statement preparation failed, handle the error
        echo "Error preparing statement: " . $connection->error;
    }

    // Close the database connection
    $connection->close();
}
?>
