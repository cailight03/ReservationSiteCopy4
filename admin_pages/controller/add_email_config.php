<?php
include '../../config/connection.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../client_pages/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission to add new college
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Store the old values
    $oldValues = json_encode(['name' => $name, 'email' => $email]);

    $sql = "INSERT INTO signatories (name, email) VALUES ('$name', '$email')";

    if (mysqli_query($connection, $sql)) {
        // Retrieve the ID of the last inserted signatory
        $signatoryId = mysqli_insert_id($connection);

        // Store the new values
        $newValues = json_encode(['name' => $name, 'email' => $email]);

        // Log the audit trail
        $userId = $_SESSION['user_id'];
        $action = "insert";
        $auditSql = "INSERT INTO signatories_audit (signatory_id, name, email, action, old_value, new_value, user_id) 
                     VALUES ('$signatoryId', '$name', '$email', '$action', '$oldValues', '$newValues', '$userId')";
        mysqli_query($connection, $auditSql);

        header("Location: ../email_configuration.php");
        exit();
    } else {
        echo "Error adding college: " . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add College</title>
    
    <!-- Custom fonts for this template-->
   <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" >

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/UI_Pages.css">

    <!-- Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Custom CSS styles */
        .btn-blue {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Add College</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" name="name" class="form-control">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="text" name="email" class="form-control">
            </div>
            <button type="submit" class="btn btn-blue"><i class="bi bi-plus"></i> Add College</button>
            <!-- Bootstrap icon for "plus" -->
        </form>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
