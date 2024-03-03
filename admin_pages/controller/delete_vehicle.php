<?php
// delete_vehicle.php

// Include your database connection file
include '../../config/connection.php';

// Check if the vehicle_id parameter is set
if (isset($_GET['vehicle_id'])) {
    $vehicleId = $_GET['vehicle_id'];

    // Retrieve the vehicle's data
    $getVehicleDataQuery = "SELECT vehicle_name, vehicle_description, vehicle_img_path FROM vehicles WHERE id = ?";
    $stmt = $connection->prepare($getVehicleDataQuery);
    $stmt->bind_param("i", $vehicleId);
    $stmt->execute();
    $vehicleDataResult = $stmt->get_result();

    if ($vehicleDataResult->num_rows > 0) {
        $vehicleData = $vehicleDataResult->fetch_assoc();
        $vehicleName = $vehicleData['vehicle_name'];
        $vehicleDescription = $vehicleData['vehicle_description'];
        $vehicleImgPath = $vehicleData['vehicle_img_path'];

        // Prepare and execute the DELETE query
        $deleteVehicleQuery = "DELETE FROM vehicles WHERE id = ?";
        $stmt = $connection->prepare($deleteVehicleQuery);
        $stmt->bind_param("i", $vehicleId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
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
                    // Log the deletion action into the vehicle audit trail
                    session_start();
                    $userId = $_SESSION['user_id']; // Assuming you have user authentication 
                    $action = "DELETE";
                    $timestamp = date("Y-m-d H:i:s");

                    $auditQuery = "INSERT INTO vehicle_audit (vehicle_id, vehicle_name, vehicle_description, vehicle_img_path, action, old_value, new_value, timestamp, user_id) VALUES (?, ?, ?, ?, ?, '', '', ?, ?)";
                    $stmt = $connection->prepare($auditQuery);
                    $stmt->bind_param("isssssi", $vehicleId, $vehicleName, $vehicleDescription, $vehicleImgPath, $action, $timestamp, $userId);
                    $stmt->execute();

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
        // Unable to fetch vehicle data, display an error message
        die("Error fetching vehicle data: Vehicle not found");
    }
} else {
    // If vehicle_id is not set, redirect to an error page or the home page
    header('Location: ../../error.php');
    exit();
}
?>
