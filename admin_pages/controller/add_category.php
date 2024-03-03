<?php
session_start(); // Start the session

include '../../config/connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get category name from the form
    $categoryName = $_POST['category_name'];

    // Check if the category name is not empty
    if (!empty($categoryName)) {
        // Create a folder for the category images
        $imageFolder = '../../img/category_img/' . $categoryName . '/';

        // Check if the folder does not exist, then create it
        if (!file_exists($imageFolder)) {
            mkdir($imageFolder, 0777, true); // Change the permission as needed
        }

        // Get the uploaded image details
        $imageFile = $_FILES['category_image'];
        $imageName = $imageFile['name'];
        $imageTmpName = $imageFile['tmp_name'];

        // Move the uploaded image to the category folder
        $destination = $imageFolder . $imageName;
        move_uploaded_file($imageTmpName, $destination);

        // Insert category data into the database
        $insertQuery = "INSERT INTO categories (category_name, category_img_path) VALUES ('$categoryName', '$destination')";
        $insertResult = $connection->query($insertQuery);

        if ($insertResult) {
            // Get the currently logged user ID
            $user_id = $_SESSION['user_id'];

            // Log the insert action into the audit trail
            $auditQuery = "INSERT INTO category_audit (category_id, category_name, action, user_id) VALUES ('$connection->insert_id', '$categoryName', 'INSERT', '$user_id')";
            $connection->query($auditQuery);

            // Category added successfully, you may want to redirect or show a success message
            header('Location: ../manage_rooms.php'); // Adjust the URL
            exit();
        } else {
            // Error inserting category into the database
            echo 'Error adding category to the database: ' . $connection->error;
        }
    } else {
        // Category name is empty
        echo 'Category name cannot be empty.';
    }
} else {
    // Redirect if the form is not submitted
    header('Location: ../manage_rooms.php'); // Adjust the URL
    exit();
}
?>
