<?php
// update_vehicle.php
include '../../config/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Handle form data (vehicle details, etc.)
    if (isset($_POST['vehicle_id'], $_POST['vehicle_name'], $_POST['vehicle_description'])) {
        // Sanitize input data
        $vehicleId = mysqli_real_escape_string($connection, $_POST['vehicle_id']);
        $vehicleName = mysqli_real_escape_string($connection, $_POST['vehicle_name']);
        $vehicleDescription = mysqli_real_escape_string($connection, $_POST['vehicle_description']);
        $selectedImages = isset($_POST['selected_images']) ? explode(',', $_POST['selected_images']) : [];

        // Fetch existing vehicle data
        $getVehicleDataQuery = "SELECT vehicle_name, vehicle_description FROM vehicles WHERE id = ?";
        $stmt = $connection->prepare($getVehicleDataQuery);
        $stmt->bind_param("i", $vehicleId);
        $stmt->execute();
        $vehicleDataResult = $stmt->get_result();

        if ($vehicleDataResult->num_rows > 0) {
            $vehicleData = $vehicleDataResult->fetch_assoc();
            $oldVehicleName = $vehicleData['vehicle_name'];
            $oldVehicleDescription = $vehicleData['vehicle_description'];

            // Use prepared statement to update vehicle details
            $updateQuery = $connection->prepare("UPDATE vehicles SET vehicle_name = ?, vehicle_description = ? WHERE id = ?");
            $updateQuery->bind_param("ssi", $vehicleName, $vehicleDescription, $vehicleId);

            if ($updateQuery->execute()) {
                // Vehicle details updated successfully

                // Log the update action into the vehicle audit trail
                session_start();
                $userId = $_SESSION['user_id']; // Assuming you have user authentication 
                $action = "UPDATE";
                $timestamp = date("Y-m-d H:i:s");

                $auditQuery = "INSERT INTO vehicle_audit (vehicle_id, vehicle_name, vehicle_description, action, old_value, new_value, timestamp, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $connection->prepare($auditQuery);
                $stmt->bind_param("issssssi", $vehicleId, $vehicleName, $vehicleDescription, $action, $oldVehicleName, $oldVehicleDescription, $timestamp, $userId);
                $stmt->execute();

                // Delete selected images
                foreach ($selectedImages as $imageName) {
                    $imagePath = "../../img/vehicle_img/" . $oldVehicleName . "/" . $imageName;

                    // Check if the file exists before attempting to delete
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }

                // Move the uploaded files to the vehicle-specific folder
                $targetDirectory = "C:/xampp/htdocs/ReservationSite/img/vehicle_img/$vehicleName";

                foreach ($_FILES["new_image"]["name"] as $index => $filename) {
                    $targetFile = $targetDirectory . DIRECTORY_SEPARATOR . basename($filename);

                    if (move_uploaded_file($_FILES["new_image"]["tmp_name"][$index], $targetFile)) {
                        // File uploaded successfully
                    } else {
                        // Error uploading file
                    }
                }

                // Redirect to the vehicle listing page
                header('Location: ../manage_vehicle.php');
                exit();
            } else {
                // Error updating vehicle details
                die("Error updating vehicle: " . $connection->error);
            }
        } else {
            // Vehicle not found, handle the error
        }
    }
}
?>
