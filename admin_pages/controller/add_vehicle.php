<?php
include '../../config/connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $vehicleName = $_POST['vehicle_name'];
    $vehicleDescription = $_POST['vehicle_description'];

    // Create a folder for room images
    $vehicleImagePath = "../../img/vehicle_img/" . $vehicleName . "/";
    if (!file_exists($vehicleImagePath)) {
        mkdir($vehicleImagePath, 0777, true);
    }

    // Upload room images to the folder
    $uploadedImages = [];
    foreach ($_FILES['vehicle_images']['name'] as $key => $value) {
        $tempName = $_FILES['vehicle_images']['tmp_name'][$key];
        $imageName = basename($_FILES['vehicle_images']['name'][$key]);
        $targetPath = $vehicleImagePath . $imageName;

        if (move_uploaded_file($tempName, $targetPath)) {
            $uploadedImages[] = $targetPath;
        } else {
            // Handle upload failure if needed
        }
    }


    // Insert data into the rooms table
    $insertQuery = "INSERT INTO vehicles (vehicle_name, vehicle_description, vehicle_img_path) VALUES (?, ?, ?)";
    $stmt = $connection->prepare($insertQuery);

    if ($stmt) {
        $vehicleImagePathForDB = "../img/vehicle_img/" . $vehicleName . "/";
        $stmt->bind_param("sss", $vehicleName, $vehicleDescription, $vehicleImagePathForDB);

        if ($stmt->execute()) {
            // Insert successful, do something if needed
        } else {
            // Insert failed, handle the error
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        // Statement preparation failed, handle the error
        echo "Error preparing statement: " . $connection->error;
    }

    // Close the database connection
    $connection->close();
}
?>
