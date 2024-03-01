<?php
include '../config/connection.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: ../login.php');
    exit();
}

// Fetch room data based on the selected room ID
if (isset($_GET['vehicle_id'])) {
    $vehicleId = $_GET['vehicle_id'];

    // Fetch room data based on the selected room ID
    $vehicleInfoQuery = "SELECT vehicle_name, vehicle_description, vehicle_img_path FROM rooms WHERE id = $vehicleId";
    $vehicleInfoResult = $connection->query($vehicleInfoQuery);

    if ($vehiceInfoResult) {
        $vehicleInfoData = $vehicleInfoResult->fetch_assoc();
        $vehicleName = $vehicleInfoData['vehicle_name'];
        $vehicleDescription = $vehicleInfoData['vehicle_description'];
        $vehicleImgPath = $vehicleInfoData['vehicle_img_path'];
        $pageTitle = "$vehicleName";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Vehicle Information'; ?></title>
    <link rel="icon" type="image/svg+xml" href="../img/NU_shield.svg">
    <link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


    

    <style>
    .highlight-range {
        background-color: #e0f7fa; /* Set your desired highlight color */
    }
</style>

</head>
<body>
<!-- Navbar -->
<?php include "navbarV2.php"; ?>



<section class="container gallery-container ">

<div class="pb-3">
        <a href="javascript:history.go(-1);" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left"></i> Back
        </a>
</div>

    <h2 class="vehicle-info-header">Vehicle Information</h2>

   


<div id="vehicleCarousel" class="carousel slide" >
    <div class="carousel-inner">
        <?php

         // Define the path to the room images folder
    $vehicleImagesPath = "$vehicleImgPath";

    // Get a list of image files in the folder
    $images = glob($vehicleImagesPath . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        // Display room images in the carousel
        foreach ($images as $index => $image) {
            echo '<div class="carousel-item' . ($index === 0 ? ' active' : '') . '">';
            echo '<img src="' . $image . '" class="d-block w-100" alt="Room Image ' . ($index + 1) . '">';
            echo '</div>';
        }
        ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

    <div class="container room-info-container">
        <div class="row">
            <div class="col-sm-8">
                <h2><?php echo $vehicleName; ?></h2>
                <p class="text-left"><?php echo $vehicleDescription; ?></p>
            </div>
            <div class="col-sm-4 text-end" >
                <!-- Button trigger modal -->



</body>
</html>
