<?php
include '../../config/connection.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../client_pages/index.php');
    exit();
}

if (isset($_GET['id'])) {
    $college_id = $_GET['id'];

    // Fetch college data from the database
    $sql = "SELECT * FROM signatories WHERE id = $college_id";
    $result = mysqli_query($connection, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $college = mysqli_fetch_assoc($result);
    } else {
        echo "College not found.";
        exit();
    }
} else {
    echo "College ID not provided.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission to update college data
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Update college information in the database
    $sql = "UPDATE signatories SET name = '$name', email = '$email' WHERE id = $college_id";

    if (mysqli_query($connection, $sql)) {
        // Update successful, log the action in the audit trail table
        $userId = $_SESSION['user_id'];
        $action = "update";
        $oldValue = "Name: {$college['name']}, Email: {$college['email']}";
        $newValue = "Name: $name, Email: $email";
        $timestamp = date("Y-m-d H:i:s");

        $logSql = "INSERT INTO signatories_audit (signatory_id, name, email, action, old_value, new_value, timestamp, user_id)
                   VALUES ($college_id, '$name', '$email', '$action', '$oldValue', '$newValue', '$timestamp', $userId)";

        if (mysqli_query($connection, $logSql)) {
            header("Location: ../email_configuration.php");
            exit();
        } else {
            echo "Error logging audit trail: " . mysqli_error($connection);
        }
    } else {
        echo "Error updating college: " . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit College</title>
</head>
<body>
    <h2>Edit College</h2>
    <form method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" value="<?php echo $college['name']; ?>"><br>
        <label for="email">Email:</label>
        <input type="text" name="email" value="<?php echo $college['email']; ?>"><br>
        <button type="submit">Update College</button>
    </form>
</body>
</html>
