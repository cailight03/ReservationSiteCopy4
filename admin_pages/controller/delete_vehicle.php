<?php
// delete_room.php

// Include your database connection file
include '../../config/connection.php';

// Check if the room_id parameter is set
if (isset($_GET['vehicle_id'])) {
    $vehicleId = $_GET['vehicle_id'];

    // Retrieve the room's name
    $getVehicleNameQuery = "SELECT vehicle_name FROM vehicles WHERE id = $vehicleId";
    $vehicleNameResult = $connection->query($getVehicleNameQuery);

    if ($vehicleNameResult) {
        $vehicleData = $vehicleNameResult->fetch_assoc();
        $vehicleName = $vehicleData['vehicle_name'];

        // Prepare and execute the DELETE query
        $deleteVehicleQuery = "DELETE FROM vehicles WHERE id = $vehicleId";
        $result = $connection->query($deleteVehicleQuery);

        if ($result) {
            // Deletion successful, delete the corresponding folder
            $vehicleImagePath = "../../img/vehicle_img/" . $vehicleName . "/";

            if (file_exists($vehicleImagePath)) {
                // Delete all files in the directory
                $files = glob($vehicleImagePath . '*');
                foreach ($files as $file) {
                    unlink($file);
                }

                // Remove the directory
                if (rmdir($vehicleImagePath)) {
                    // Redirect to the page where the user came from
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                    exit();
                } else {
                    // Directory removal failed, display an error message
                    die("Error deleting vehicle folder: Unable to remove directory");
                }
            } else {
                // Directory does not exist, no need to remove
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
        } else {
            // Deletion failed, display an error message
            die("Error deleting vehicle: " . $connection->error);
        }
    } else {
        // Unable to fetch room name, display an error message
        die("Error fetching vehicle data: " . $connection->error);
    }
} else {
    // If room_id is not set, redirect to an error page or the home page
    header('Location: ../../error.php');
    exit();
}
?>
