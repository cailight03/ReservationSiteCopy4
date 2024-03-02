<?php 
include '../config/connection.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: ../login.php');
    exit();
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

    <title>NULR Admin | Manage Rooms</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
     <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
     <link rel="stylesheet" href="..\css\UI_Pages.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

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

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                    </ul>

                </nav>
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
                                    // Add an anchor tag around each card

                                    echo '<div class="col">';
                                    echo '<a href="display_rooms.php?category=' . $categoryId . '" style="text-decoration: none; color: inherit;">';
                                    echo '<div class="card shadow position-relative border-0"  >'; // Adjust border-radius as needed
                                    echo '<img src="' . $imagePath . '" class="card-img alt="Category Image" style="object-fit: cover;  filter: brightness(70%);"/>';
                                    echo '<div class="card-body d-flex align-items-center justify-content-center position-absolute top-50 start-50 translate-middle text-center" style="color: white;">';
                                    echo '<h5 class="card-title">' . $categoryName . '</h5>';
                                    echo '</div>';
                                    echo '<div class="dropdown" style="position: absolute; top: 10px; right: 10px; z-index: 1000;">';
                                    echo '<i class="bi bi-three-dots-vertical text-light" id="dropdownMenuButton' . $categoryId . '" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 1.5em;" onclick="toggleOptions(' . $categoryId . ')"></i>';
                                    echo '<ul class="dropdown-menu" id="optionsMenu' . $categoryId . '">' ;
                                    echo '<li><a class="dropdown-item" href="#" onclick="editCategory(' . $categoryId . ', \'' . $categoryName . '\')"><i class="bi bi-pencil"></i> <span class="edit-option">Edit</span></a></li>';
                                    echo '<li><a class="dropdown-item" href="#" onclick="deleteCategory(' . $categoryId . ')"><i class="bi bi-trash"></i> <span class="delete-option">Delete</span></a></li>';
                                    echo '</ul>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</a>';
                                    echo '</div>';
                                    }
                                }

                                // Add an arrow card after the loop without the card class
                                // Add an arrow card after the loop without the card class
                                // echo '<div class="arrow-container d-flex flex-column align-items-center justify-content-center">';
                                // echo '<div class="arrow-card hover-effect" style="color: lightgray; font-size: 4rem;">';
                                // echo '<i class="bi bi-arrow-right-square-fill"></i>';
                                // echo '</div>';
                                // echo '<div class="others-text hover-effect mt-2" style="font-size: 24px;font-weight: bold; color: lightgray;">Others</div>';
                                // echo '</div>';
                                

                                
                                
                                

                            } else {
                                echo "No categories found";
                            }
                        echo '<div class="col-12 d-flex justify-content-center mt-3">';
                        echo '<button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add Category</button>';
                        echo '</div>';
                        ?>
                        </div>

                        <!-- Modal for adding category -->
                        <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Form for adding a category -->
                                    <form id="addCategoryForm" action="controller/add_category.php" method="POST" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="categoryName" class="form-label">Category Name</label>
                                            <input type="text" class="form-control" id="categoryName" name="category_name" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="categoryImage" class="form-label">Category Image</label>
                                            <input type="file" class="form-control" id="categoryImage" name="category_image" accept="image/*" required>
                                            <small class="text-muted">Supported formats: jpg, jpeg, png, gif</small>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        </div>

                        </section>
                             
                        <!-- Modal for editing category -->
                        <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                                    <button type="submit" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                <form id="addCategoryForm" action="controller/update_category_name.php" method="POST" enctype="multipart/form-data" onsubmit="submitAddCategoryForm()">
                                        <div class="mb-3">
                                            <label for="editCategoryName" class="form-label">Category Name:</label>
                                            <input type="text" class="form-control" id="editCategoryName" name="editCategoryName" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="editCategoryImage" class="form-label">Replace New Image:</label>
                                            <input type="file" class="form-control" id="editCategoryImage" name="editCategoryImage">
                                        </div>
                                        <input type="hidden" id="editCategoryId" name="editCategoryId">
                                        <button type="submit" class="btn btn-primary" onclick="saveCategoryChanges()">Save Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        </div>

           
                

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; NU Laguna Reservation Site 2024</span>
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
  // Function to toggle the visibility of edit and delete options
  function toggleOptions(categoryId) {
    var optionsMenu = document.getElementById('optionsMenu' + categoryId);
    if (optionsMenu) {
        optionsMenu.classList.toggle('show');
    }
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
   function editCategory(categoryId, categoryName) {
    var editCategoryNameInput = document.getElementById('editCategoryName');
    var editCategoryIdInput = document.getElementById('editCategoryId');

    editCategoryNameInput.value = categoryName;
    editCategoryIdInput.value = categoryId;

    // Additional code to handle the image if needed

    // Show the modal
    var editCategoryModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
    editCategoryModal.show();
}
function saveCategoryChanges() {
    // Get the form data
    var editCategoryForm = document.getElementById('editCategoryForm');
    var formData = new FormData(editCategoryForm);

    // Send AJAX request to update the category name and image
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                // Category name and image updated successfully, you may want to update the UI accordingly
                location.reload(); // Reload the page for simplicity
            } else {
                console.error('Error updating category name and image:', xhr.statusText);
                // Handle the error appropriately
            }
        }
    };

    xhr.open('POST', 'controller/update_category_name.php', true); // Adjust the URL

    // Check if a new image is provided before sending the request
    var editCategoryImageInput = document.getElementById('editCategoryImage');
    if (editCategoryImageInput.files.length > 0) {
        xhr.send(formData);
    } else {
        // If no new image is provided, remove the 'editCategoryImage' field from the FormData
        formData.delete('editCategoryImage');

        // Send the updated FormData (without the image) to prevent deleting the old image
        xhr.send(formData);
    }
}

</script>
<script>
function deleteCategory(categoryId) {
    if (confirm('Are you sure you want to delete this category?')) {
        // Send AJAX request to delete the category
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Category deleted successfully, you may want to update the UI accordingly
                    location.reload(); // Reload the page for simplicity
                } else {
                    console.error('Error deleting category:', xhr.statusText);
                    // Handle the error appropriately
                }
            }
        };

        xhr.open('POST', 'controller/delete_category.php', true); // Adjust the URL
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('categoryId=' + categoryId);
    }
}
    </script>

<script>
function submitAddCategoryForm() {
    // Optionally, you can add client-side validation here before submitting the form.
    // If validation passes, the form will be submitted to "add_category.php".
    document.getElementById('addCategoryForm').submit();
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>