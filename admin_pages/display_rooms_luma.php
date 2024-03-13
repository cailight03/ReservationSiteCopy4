<?php
include '../config/connection.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: ../client_pages/index.php');
    exit();
}

$categoryName = "Unknown Category";
$roomsResult = null;

// Retrieve the category parameter from the URL
if (isset($_GET['category'])) {
    $categoryId = $_GET['category'];

    // Fetch category name based on the selected category ID
    $categoryNameQuery = "SELECT category_name FROM categories WHERE id = $categoryId";
    $categoryNameResult = $connection->query($categoryNameQuery);

    if ($categoryNameResult) {
        $categoryNameData = $categoryNameResult->fetch_assoc();
        $categoryName = $categoryNameData['category_name'];
        $pageTitle = "$categoryName";
    }

    // Check if the form is submitted and the search term is set
    if (isset($_GET['search'])) {
        $searchTerm = $_GET['search'];

        // Modify the SQL query to include the search condition
        $roomsQuery = "SELECT id, room_name, room_description, room_img_path FROM rooms WHERE category_id = $categoryId AND room_name LIKE '%$searchTerm%'";

        $roomsResult = $connection->query($roomsQuery);

        if (!$roomsResult) {
            die("Error fetching rooms: " . $connection->error);
        }
    } else {
        // Fetch rooms based on the selected category
        // Use proper SQL query to fetch rooms where category_id = $categoryId
        $roomsQuery = "SELECT id, room_name, room_description, room_img_path FROM rooms WHERE category_id = $categoryId ORDER BY room_name";


        $roomsResult = $connection->query($roomsQuery);

        if (!$roomsResult) {
            die("Error fetching rooms: " . $connection->error);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="../img/NU_shield.svg">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" ></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></title>
</head>
<body>








<section class="container rooms-container">
<div class="pb-3">
    <a href="index.php" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

    
        <div class="row">
            <div class="col">
                <h2 class="grid-header text-start" id="header"><?php echo $categoryName; ?></h2>
            </div>
            <div class="col">
            <form class="d-flex" role="search" method="GET" autocomplete="off">
    <input type="hidden" name="category" value="<?php echo $categoryId; ?>">
    <input class="form-control me-2" type="text" name="search" placeholder="Search" aria-label="Search"  autocomplete="false">
    <button class="btn btn-outline-success" type="submit">Search</button>
</form>
            </div>
        </div>
  
    
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php
    // Check if $roomsResult is not null before attempting to fetch data
    if ($roomsResult !== null) {
        // Check if there are rows returned
        if ($roomsResult->num_rows > 0) {
            // Display the fetched rooms
            while ($roomData = $roomsResult->fetch_assoc()) {
                $roomId = $roomData['id'];
                $roomName = $roomData['room_name'];
                $roomDescription = $roomData['room_description'];
                $roomFolderPath = $roomData['room_img_path'];

                // Construct the path to the room images folder
                $roomImagesPath = $roomFolderPath;

                // Get a list of image files in the folder
                $images = glob($roomImagesPath . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

                if (count($images) > 0) {
                    // Generate a unique carousel ID based on the room ID
                    $carouselId = 'carouselExampleControls-' . $roomId;

                    echo '<div class="col">';
                    
                    echo '<a href="room_info.php?room_id=' . $roomId . '" style="text-decoration: none; color: inherit;">';
                    echo '<div class="card shadow">';
                    echo '<div id="' . $carouselId . '" class="carousel slide">';
                    echo '<div class="carousel-inner">';
                    // Display carousel items
                    foreach ($images as $index => $image) {
                        echo '<div class="carousel-item' . ($index === 0 ? ' active' : '') . '">';
                        echo '<img src="' . $image . '" class="d-block w-100" alt="Room Image">';
                        echo '</div>';
                    }
                    

                    echo '</div>';
                    echo '<button class="carousel-control-prev" type="button" data-bs-target="#' . $carouselId . '" data-bs-slide="prev">';
                    echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
                    echo '<span class="visually-hidden">Previous</span>';
                    echo '</button>';
                    echo '<button class="carousel-control-next" type="button" data-bs-target="#' . $carouselId . '" data-bs-slide="next">';
                    echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
                    echo '<span class="visually-hidden">Next</span>';
                    echo '</button>';
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . $roomName . '</h5>';
                    echo '<p class="card-text">' . $roomDescription . '</p>';
                    echo '</div>';
                    echo '<div class="dropdown" style="position: absolute; top: 10px; right: 10px; z-index: 1000;">';
                    echo '<i class="bi bi-three-dots-vertical text-light" id="dropdownMenuButton' . $roomId . '" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1.5em;" onclick="toggleOptions(' . $roomId . ')"></i>';
                    echo '<ul class="dropdown-menu" id="optionsMenu' . $roomId . '">' ;
                    echo '<li><a class="dropdown-item" href="#" onclick="openEditModal(' . $roomId . ', \'' . $roomName . '\', \'' . $roomDescription . '\', \'' . implode(',', $images) . '\')"><i class="bi bi-pencil"></i> <span class="edit-option">Edit</span></a></li>';

                    echo '<li><a class="dropdown-item" href="#" onclick="deleteRoom(' . $roomId . ')"><i class="bi bi-trash"></i> <span class="delete-option">Delete</span></a></li>';
                    echo '</ul>';
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';
                    echo '</div>';
                }
                
            }
        } else {
            echo '<p>No rooms found.</p>';
        }
    } else {
        echo '<p>Error fetching rooms.</p>';
    }
 // Display the "Add Room" button outside the loop
 echo '<div class="col-12 d-flex justify-content-center mt-3">';
echo ' <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addRoomModal">Add Room</button>';
echo '</div>';
?>









<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1" role="dialog" aria-labelledby="addRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRoomModalLabel">Add Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Form for adding a room -->
                <form id="addRoomForm" action="add_room.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="roomName" class="form-label">Room Name</label>
                        <input type="text" class="form-control" id="roomName" name="room_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="roomDescription" class="form-label">Room Description</label>
                        <textarea class="form-control" id="roomDescription" name="room_description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <!-- Assuming you have a table 'categories' in your database -->
                        <select class="form-select" id="category" name="category_id" required>
                            <!-- Fetch and display categories dynamically -->
                            <?php
                            $categoriesQuery = "SELECT id, category_name FROM categories";
                            $categoriesResult = $connection->query($categoriesQuery);

                            if ($categoriesResult->num_rows > 0) {
                                while ($categoryData = $categoriesResult->fetch_assoc()) {
                                    echo '<option value="' . $categoryData['id'] . '">' . $categoryData['category_name'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="roomImage" class="form-label">Room Images</label>
                        <input type="file" class="form-control" id="roomImage" name="room_images[]" accept="image/*" multiple required>
                        <small class="text-muted">Supported formats: jpg, jpeg, png, gif</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

</section>












<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Room Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
    <form action="update_room.php" method="POST" enctype="multipart/form-data">
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
                    }?>
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



<script>
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






<script>
  // Function to toggle the visibility of edit and delete options
  function toggleOptions(roomId) {
    var optionsMenu = document.getElementById('optionsMenu' + roomId);
    optionsMenu.classList.toggle('show');
  }

  // Close the options if the user clicks outside of it
  window.onclick = function (event) {
    if (!event.target.matches('.options-container') && !event.target.matches('.options-menu')) {
      var optionsMenus = document.getElementsByClassName('options-menu');
      for (var i = 0; i < optionsMenus.length; i++) {
        var openOptions = optionsMenus[i];
        if (openOptions.classList.contains('show')) {
          openOptions.classList.remove('show');
        }
      }
    }
  };
</script>


<script>
 function deleteRoom(roomId) {
  console.log("Delete room function called for room ID: " + roomId);

  var confirmDelete = confirm("Are you sure you want to delete this room?");
  
  if (confirmDelete) {
    // Redirect to the delete room script or perform the deletion logic here
    window.location.href = 'delete_room.php?room_id=' + roomId;
  }
}
</script>



</body>
</html>