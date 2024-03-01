<?php
include '../config/connection.php';

session_start();


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
    <link rel="icon" type="image/svg+xml" href="../img/login_img/NU_shield.svg">
    <link rel="stylesheet" href="..\css\UI_Pages.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" ></script>
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></title>
</head>
<body>

<?php include "navbar.php";?>

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
    ?>
</div>

</section>
<?php include "footer.php"; ?>

</body>
</html>
