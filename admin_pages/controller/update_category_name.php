<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = $_POST['editCategoryId'];
    $newCategoryName = $_POST['editCategoryName'];

    // Fetch existing category data
    $categoryQuery = "SELECT category_name, category_img_path FROM categories WHERE id = ?";
    $categoryStatement = $connection->prepare($categoryQuery);
    $categoryStatement->bind_param('i', $categoryId);
    $categoryStatement->execute();
    $categoryResult = $categoryStatement->get_result();

    if ($categoryResult->num_rows > 0) {
        $categoryData = $categoryResult->fetch_assoc();
        $oldCategoryName = $categoryData['category_name'];
        $oldImagePath = $categoryData['category_img_path'];

        // Rename the category folder
        $oldFolderPath = '../../img/category_img/' . $oldCategoryName;
        $newFolderPath = '../../img/category_img/' . $newCategoryName;

        if ($oldFolderPath !== $newFolderPath && !file_exists($newFolderPath)) {
            rename($oldFolderPath, $newFolderPath);
        }

        // Check if a new image is uploaded
        if ($_FILES['editCategoryImage']['error'] == UPLOAD_ERR_OK) {
            // Delete the old image if it exists
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }

            // Upload the new image if it is an image file
            $newImageName = $_FILES['editCategoryImage']['name'];
            $newImageTmp = $_FILES['editCategoryImage']['tmp_name'];
            $newImagePath = "$newFolderPath/$newImageName";

            if (move_uploaded_file($newImageTmp, $newImagePath)) {
                // Update the category name in the database
                $updateQuery = "UPDATE categories SET category_name = ? WHERE id = ?";
                $updateStatement = $connection->prepare($updateQuery);
                $updateStatement->bind_param('si', $newCategoryName, $categoryId);
                $updateStatement->execute();
            }
        }

        // You can handle the response or redirect as needed
        // For simplicity, just redirect back to the main page
        header('Location: index.php');
        exit();
    } else {
        // Handle the case where category data is not found
        // You might want to show an error message or redirect
    }
}
?>
