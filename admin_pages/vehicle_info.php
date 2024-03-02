<?php
include '../config/connection.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: ../login.php');
    exit();
}
// Check if 'vehicle_id' is set in the URL
if (isset($_GET['vehicle_id'])) {
    // Retrieve the value of 'vehicle_id'
    $vehicleId = $_GET['vehicle_id'];

    // Fetch vehicle data based on the selected vehicle ID
    // Construct the SQL query
    $vehicleInfoQuery = "SELECT vehicle_name, vehicle_description, vehicle_img_path FROM vehicles WHERE id = $vehicleId";
    
    // Execute the query
    $vehicleInfoResult = $connection->query($vehicleInfoQuery);

    // Check if the query was executed successfully
    if ($vehicleInfoResult) {
        // Fetch the result as an associative array
        $vehicleInfoData = $vehicleInfoResult->fetch_assoc();
        
        // Extract vehicle information from the fetched data
        $vehicleName = $vehicleInfoData['vehicle_name'];
        $vehicleDescription = $vehicleInfoData['vehicle_description'];
        $vehicleImgPath = $vehicleInfoData['vehicle_img_path'];
        
        // Set the page title using the vehicle name
        $pageTitle = "$vehicleName";
    } else {
        // Handle the case when the query execution fails
        echo "Error: " . $connection->error;
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
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    

    <!-- <link rel="stylesheet" href="style.css"> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" ></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Custom styles for back button -->
<style>
  .backArrow {
    background-color: #708090;
    height: 70px; /* Reduced height for fitting into the top navbar */ 
    display: flex;
    align-items: center;
    padding-right: 10px; /* Adjust the padding as needed */
  }

  .back-button {
    color: white;
    border: none !important;
    padding: 5px; /* Reduced padding */
    transition: background-color 0.5s, transform 0.5s;
  }

.back-button:hover {
    background-color: rgba(255, 255, 255, 0.8);
    color: black;
    transform: translateX(-3px);
    box-shadow: 3px 0px 10px 0px rgba(105,105,105,0.8);
}

  /* Added CSS for positioning the back button */
  .backArrow button {
    margin-left: 10px; /* Adjust margin as needed */
}

.highlight-range {
     background-color: #e0f7fa; /* Set your desired highlight color */
}
</style>

</head>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include 'navitems.php'; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <div class="backArrow">
                        <div>
                            <a href="manage_rooms.php">
                                <button class="btn btn-xs back-button">
                                    <span><i class="fa fa-arrow-left fa-2x"></i></span>
                                </button>
                            </a>
                        </div>
                    </div>

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto"></ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
    <h2 class="vehicle-info-header" style="text-align: center;">Vehicle Information</h2>
   
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

<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>
</html>
