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
if (isset($_GET['room_id'])) {
    $roomId = $_GET['room_id'];

    // Fetch room data based on the selected room ID
    $roomInfoQuery = "SELECT room_name, room_description, room_img_path FROM rooms WHERE id = $roomId";
    $roomInfoResult = $connection->query($roomInfoQuery);

    if ($roomInfoResult) {
        $roomInfoData = $roomInfoResult->fetch_assoc();
        $roomName = $roomInfoData['room_name'];
        $roomDescription = $roomInfoData['room_description'];
        $roomImgPath = $roomInfoData['room_img_path'];
        $pageTitle = "$roomName";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Room Information'; ?></title>
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

    <h2 class="room-info-header">Room Information</h2>

   


<div id="roomCarousel" class="carousel slide" >
    <div class="carousel-inner">
        <?php

         // Define the path to the room images folder
    $roomImagesPath = "$roomImgPath";

    // Get a list of image files in the folder
    $images = glob($roomImagesPath . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
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
                <h2><?php echo $roomName; ?></h2>
                <p class="text-left"><?php echo $roomDescription; ?></p>
            </div>
            <div class="col-sm-4 text-end" >
                <!-- Button trigger modal -->
                
<!-- Add an Edit button -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
    <i class="bi bi-pencil"></i>
</button>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h5 class="modal-title text-center" id="editModalLabel">Room Details</h5>
                <button type="button" class="btn-primary btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form action="controller/update_room.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="room_id" value="<?php echo $roomId; ?>">

                    <div class="mb-3">
                        <label for="roomName" class="form-label">Room Name</label>
                        <input type="text" class="form-control" id="roomName" name="room_name" value="<?php echo $roomName; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="roomDescription" class="form-label">Room Description</label>
                        <textarea class="form-control" id="roomDescription" name="room_description"><?php echo $roomDescription; ?></textarea>
                    </div>

                    <div id="carouselCurrentImages" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php foreach ($images as $index => $image) {
                                echo '<div class="carousel-item' . ($index === 0 ? ' active' : '') . '">';
                                echo '<img src="' . $image . '" class="d-block w-100" alt="Room Image">';
                                echo '</div>';
                            } ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselCurrentImages" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselCurrentImages" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>

                    <div class="mb-3">
                        <label for="selectedImages" class="form-label">Select Images to Delete</label>
                        <div id="imageCheckboxes">
                            <!-- JavaScript will dynamically populate this section -->
                        </div>
                    </div>

                    <!-- Hidden input field to store selected image filenames -->
                    <input type="hidden" id="selectedImages" name="selected_images">

                    <div class="mb-3">
                        <label for="newImage" class="form-label">Upload New Image</label>
                        <input type="file" class="form-control" id="newImage" name="new_image[]" multiple>
                        <small class="text-muted">Supported formats: jpg, jpeg, png, gif</small>
                    </div>

                    <!-- Additional Save Changes button if needed -->
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    
// Function to open the edit modal
function openEditModal(roomId, roomName, roomDescription, roomImages) {
    // Set the room ID, room name, and room description in the modal form
    $('#editModal').modal('show');
    $('#editModal').find('input[name="room_id"]').val(roomId);
    $('#editModal').find('input[name="room_name"]').val(roomName);
    $('#editModal').find('textarea[name="room_description"]').val(roomDescription);

    // Convert the comma-separated image paths to an array
    var imagesArray = roomImages.split(',');

    // Insert the fetched images into the modal carousel
    var carouselInner = $('#carouselCurrentImages').find('.carousel-inner');
    carouselInner.empty(); // Clear existing carousel items

    // Display carousel items
    for (var index = 0; index < imagesArray.length; index++) {
        carouselInner.append('<div class="carousel-item' + (index === 0 ? ' active' : '') + '">' +
            '<img src="' + imagesArray[index] + '" class="d-block w-100" alt="Room Image">' +
            '</div>');
    }

    var imageCheckboxes = $('#imageCheckboxes');
    imageCheckboxes.empty(); // Clear existing checkboxes

    // Set the selected image filenames in the hidden input field
    $('#selectedImages').val('');

    for (var index = 0; index < imagesArray.length; index++) {
        var imageName = imagesArray[index].split('/').pop(); // Extract filename

        imageCheckboxes.append('<div class="form-check">' +
            '<input class="form-check-input" type="checkbox" name="selected_images[]" value="' + imageName + '" id="checkImage' + index + '">' +
            '<label class="form-check-label" for="checkImage' + index + '">' + imageName + '</label>' +
            '</div>');
    }

    // Set up event listener to update hidden input when checkboxes are changed
    imageCheckboxes.find('input[type="checkbox"]').on('change', function () {
        var selectedValues = imageCheckboxes.find('input[type="checkbox"]:checked').map(function () {
            return this.value;
        }).get().join(',');
        $('#selectedImages').val(selectedValues);
    });

    // Check if the number of images is equal to 1, then hide the section
    if (imagesArray.length === 1) {
        $('#imageCheckboxes').hide();
        $('#selectedImages').hide();
    } else {
        $('#imageCheckboxes').show();
        $('#selectedImages').show();
    }
}
</script>
</body>
</html>
