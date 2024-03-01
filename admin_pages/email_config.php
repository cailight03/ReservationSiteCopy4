<?php 
include '../config/connection.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header('Location: ../index.php');
    exit();
}

// Fetch colleges data from the database
$sql = "SELECT * FROM colleges";
$result = mysqli_query($connection, $sql);

// Check if there are any colleges
if (mysqli_num_rows($result) > 0) {
    $colleges = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $colleges = []; // Empty array if no colleges found
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
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></title>
</head>
<body>
  <!-- Include the navbar -->
  <?php include 'navbarV2.php'; ?>

<section class="container">
    <h2 class="grid-header" id="header">Email Configuration</h2>

    <!-- Display colleges data -->
    <table class="table">
        <thead>
            <tr>
              
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($colleges as $college): ?>
                <tr>
                   
                    <td><?php echo $college['name']; ?></td>
                    <td><?php echo $college['email']; ?></td>
                    <td>
                        <a href="controller/editcollege.php?id=<?php echo $college['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="controller/deletecollege.php?id=<?php echo $college['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Add new college button -->
    <a href="controller/addcollege.php" class="btn btn-success">Add New College</a>
</section>
</body>
</html>
