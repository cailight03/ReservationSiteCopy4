<?php
session_start();

// Include database connection
include '../config/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check in userstbl
    $query = "SELECT id, firstName, lastName, password, category FROM userstbl WHERE email = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $firstName, $lastName, $stored_password, $category);

    if ($stmt->fetch() && $password == $stored_password) {
        // Authentication successful for user
        $_SESSION['user_id'] = $user_id;
        $_SESSION['firstName'] = $firstName;
        $_SESSION['lastName'] = $lastName;

        // Redirect based on user category
        switch ($category) {
            case 'admin':
                header("Location: ../admin_pages/admin_dash.php?user_id=$user_id");
                break;
            case 'client':
                header("Location: ../client_pages/index.php?user_id=$user_id");
                break;
            case 'signatory':
                header("Location: ../gago.html?user_id=$user_id");
                break;
            default:
                // Invalid category, redirect back to login with an error
                header("Location: index.php?error=Invalid category");
                break;
        }

        exit();
    }

    // If the user is not found or the password is incorrect, redirect back to the login page with an error message
    header("Location: ../index.php?error=Invalid credentials");
    exit();

    // Close the result set for the userstbl query
    $stmt->close();
    
    // Close the database connection
    $connection->close();
}
?>
