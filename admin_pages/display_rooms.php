<?php
include '../config/connection.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: ../login.php');
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
        $roomsQuery = "SELECT id, room_name, room_description, room_img_path FROM rooms WHERE category_id = $categoryId";

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

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>NULR Admin | Display Rooms</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../css/UI_Pages.css">

    <!-- <link rel="stylesheet" href="style.css"> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" ></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
    <!-- Top Navbar Back Button Styles -->
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
                    <ul class="navbar-nav ml-auto">
                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid"> </div>

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
                    echo '<div class="dropdown" style="position: absolute; top: 10px; right: 10px; z-index: 2000;">';
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
                <form id="addRoomForm" action="controller/add_room.php" method="POST" enctype="multipart/form-data">
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

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; NU Laguna Reservation Site 2024 </span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="#">Logout</a>
                </div>
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
    window.location.href = 'controller/delete_room.php?room_id=' + roomId;
  }
}
</script>

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