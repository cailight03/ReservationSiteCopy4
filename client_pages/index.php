<?php 
include '../config/connection.php';

session_start();

if (isset($_GET['admin_access']) && $_GET['admin_access'] === 'nulagunareservation') {
    // Redirect to the login page
    header('Location: ../login.php');
    exit();}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="../img/login_img/NU_shield.svg">
    <link rel="stylesheet" href="..\css\UI_Pages.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" ></script>
    <title>Home</title>
</head>
<body>
   <?php include "navbar.php";?>


   <div class="container header-container">
    <div class="image-container" id="header-container">
        <div class="header-content">
            <h1>Reserve a venue for your event needs</h1>
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Expedita eos voluptatem autem, doloremque optio modi a fuga fugit. Delectus mollitia illo facilis maiores nesciunt voluptatem architecto molestias enim culpa illum!</p>
        </div>
    </div>
</div>



<section class="container">
    <h2 class="grid-header" id="header">Venues</h2>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php
        // Fetch category data from the database (replace this with your actual database fetch code)
        $categoryQuery = "SELECT id, category_name, category_img_path FROM categories";
        $categoryResult = $connection->query($categoryQuery);

        if ($categoryResult->num_rows > 0) {
            while ($categoryData = $categoryResult->fetch_assoc()) {
                $categoryId = $categoryData['id'];
                $categoryName = $categoryData['category_name'];
                $imageFolder = '../img/category_img/' . $categoryName . '/';
        
                // Use glob to get an array of image files in the folder
                $imageFiles = glob($imageFolder . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        
                if (!empty($imageFiles)) {
                    // Get the first image from the array
                    $imagePath = reset($imageFiles);
        
                    echo '<div class="col">';
                    echo '<a href="display_rooms.php?category=' . $categoryId . '" style="text-decoration: none; color: inherit;">';
                    echo '<div class="card shadow position-relative border-0">';
                    echo '<img src="' . $imagePath . '" class="card-img" alt="Category Image" style="object-fit: cover; filter: brightness(70%);" />';
                    echo '<div class="card-body d-flex align-items-center justify-content-center position-absolute top-50 start-50 translate-middle text-center" style="color: white;">';
                    echo '<h5 class="card-title">' . $categoryName . '</h5>';
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';
                    echo '</div>';
                } else {
                    echo 'No images found for category ' . $categoryName . ' <br>';
                }
            }
        } else {
            echo "No categories found";
        }
        ?>
    </div>
</section>

<section class="container">
    <h2 class="grid-header" id="header">Vehicles</h2>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php
        // Fetch vehicle data from the database
        $vehicleQuery = "SELECT id, vehicle_name, vehicle_description, vehicle_img_path FROM vehicles";
        $vehicleResult = $connection->query($vehicleQuery);

        if ($vehicleResult->num_rows > 0) {
            while ($vehicleData = $vehicleResult->fetch_assoc()) {
                $vehicleId = $vehicleData['id'];
                $vehicleName = $vehicleData['vehicle_name'];
                $vehicleDescription = $vehicleData['vehicle_description'];
                $imagePath = $vehicleData['vehicle_img_path'];

                // Construct the path to the vehicle images folder
                $vehicleImagesPath = $imagePath;

                // Get a list of image files in the folder
                $images = glob($vehicleImagesPath . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

                echo '<div class="col">';
                echo '<a href="vehicle_info.php?vehicle_id=' . $vehicleId . '" style="text-decoration: none; color: inherit;">';
                echo '<div class="card shadow">';
                echo '<div id="carousel-' . $vehicleId . '" class="carousel slide">';
                echo '<div class="carousel-inner" style="height: 276px; overflow: hidden;">';
                
                // Display carousel items
                foreach ($images as $index => $image) {
                    echo '<div class="carousel-item' . ($index === 0 ? ' active' : '') . '">';
                    echo '<img src="' . $image . '" class="d-block w-100" alt="Vehicle Image ' . ($index + 1) . '">';
                    echo '</div>';
                }

                echo '</div>';
                echo '<button class="carousel-control-prev" type="button" data-bs-target="#carousel-' . $vehicleId . '" data-bs-slide="prev">';
                echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
                echo '<span class="visually-hidden">Previous</span>';
                echo '</button>';
                echo '<button class="carousel-control-next" type="button" data-bs-target="#carousel-' . $vehicleId . '" data-bs-slide="next">';
                echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
                echo '<span class="visually-hidden">Next</span>';
                echo '</button>';
                echo '</div>';
                
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $vehicleName . '</h5>';
                echo '<p class="card-text">' . $vehicleDescription . '</p>';
                echo '</div>';
                echo '</div>';
                echo '</a>';
                echo '</div>';
            }
        } else {
            echo "No vehicles found";
        }
        ?>
    </div>
</section>



<?php include "footer.php"; ?>


<script>
document.addEventListener("DOMContentLoaded", function () {
    var headerImages = [
        "../img/header_img/cafeteria2.jpg",
        "../img/header_img/Chemistry-Laboratory.jpg",
        "../img/header_img/Drawing-Room.jpg",
        // Add more image paths as needed
    ];

    var headerContainer = document.getElementById("header-container");
    var currentImageIndex = 0;

    function changeHeaderImage() {
        headerContainer.style.backgroundImage = "linear-gradient(to right, rgba(44, 56, 85, 0.9), rgba(168, 187, 100, 0.671)), url('" + headerImages[currentImageIndex] + "')";
        currentImageIndex = (currentImageIndex + 1) % headerImages.length;
    }

    // Trigger the changeHeaderImage function immediately when the page loads
    changeHeaderImage();

    // Change the header image every 3000 milliseconds (3 seconds)
    setInterval(changeHeaderImage, 3000);

    // Add your modal trigger code here
    var arrowCard = document.querySelector('.arrow-card');
    arrowCard.addEventListener('click', function () {
        var myModal = new bootstrap.Modal(document.getElementById('myModal'));
        myModal.show();
    });
});


</script>



</body>
</html>






