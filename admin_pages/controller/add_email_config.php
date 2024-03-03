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
</head>
<body>
    <h2>Add College</h2>
    <form method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name"><br>
        <label for="email">Email:</label>
        <input type="text" name="email"><br>
        <button type="submit">Add College</button>
    </form>
</body>
</html>
