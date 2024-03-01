<?php
include '../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['categoryId'])) {
    // Sanitize the input
    $categoryId = filter_var($_POST['categoryId'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch category name before deletion (optional, for logging purposes)---------------------> added for logs
    $getCategoryQuery = "SELECT category_name FROM categories WHERE id = ?";
    $stmt = $connection->prepare($getCategoryQuery);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $stmt->bind_result($categoryName);
    $stmt->fetch();
    $stmt->close();

    // Prepare and execute the DELETE query
    $deleteQuery = "DELETE FROM categories WHERE id = ?";
    $stmt = $connection->prepare($deleteQuery);
    $stmt->bind_param("i", $categoryId);

    if ($stmt->execute()) {
        // Insert log entry for the delete action ---------------------> added for logs
        session_start();
        $userId = $_SESSION['user_id']; // Assuming you have user authentication 
        $action = "delete";
        $log_query = "INSERT INTO audit_logs (user_id, category_id, action) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($log_query);
        $stmt->bind_param("iis", $userId, $categoryId, $action);
        $stmt->execute();

        // Deletion successful
        echo json_encode(['success' => true]);
    } else {
        // Deletion failed
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    // Invalid request method or missing categoryId
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}

$connection->close();
?>
