<?php
include '../../config/connection.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../client_pages/index.php');
    exit();
}

if (isset($_GET['id'])) {
    $college_id = $_GET['id'];

    // Fetch college data before deletion
    $fetchCollegeQuery = "SELECT * FROM signatories WHERE id = $college_id";
    $fetchCollegeResult = mysqli_query($connection, $fetchCollegeQuery);

    if ($fetchCollegeResult && mysqli_num_rows($fetchCollegeResult) > 0) {
        $collegeData = mysqli_fetch_assoc($fetchCollegeResult);
    } else {
        echo "College not found.";
        exit();
    }

    // Delete college from the database
    $deleteCollegeQuery = "DELETE FROM signatories WHERE id = $college_id";

    if (mysqli_query($connection, $deleteCollegeQuery)) {
        // Deletion successful, log the action in the audit trail table
        $userId = $_SESSION['user_id'];
        $action = "delete";
        $oldValue = "Name: {$collegeData['name']}, Email: {$collegeData['email']}";
        $newValue = "";
        $timestamp = date('Y-m-d H:i:s');

        $logSql = "INSERT INTO signatories_audit (signatory_id, name, email, action, old_value, new_value, timestamp, user_id)
                   VALUES ($college_id, '{$collegeData['name']}', '{$collegeData['email']}', '$action', '$oldValue', '$newValue', '$timestamp', $userId)";

        if (mysqli_query($connection, $logSql)) {
            header("Location: email_configuration.php");
            exit();
        } else {
            echo "Error logging audit trail: " . mysqli_error($connection);
            exit();
        }
    } else {
        echo "Error deleting college: " . mysqli_error($connection);
        exit();
    }
} else {
    echo "College ID not provided.";
    exit();
}
?>
