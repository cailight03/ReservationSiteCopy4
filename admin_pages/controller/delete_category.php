<?php
include '../../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['categoryId'])) {
    // Sanitize the input
    $categoryId = filter_var($_POST['categoryId'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch category name before deletion (optional, for logging purposes)
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
        // Insert log entry for the delete action
        session_start();
        $userId = $_SESSION['user_id']; // Assuming you have user authentication 
        $action = "DELETE";
        $timestamp = date("Y-m-d H:i:s");
        
        $auditQuery = "INSERT INTO category_audit (category_id, category_name, action, timestamp, user_id) VALUES (?, ?, ?, ?, ?)";
        $stmtAudit = $connection->prepare($auditQuery);
        $stmtAudit->bind_param("isssi", $categoryId, $categoryName, $action, $timestamp, $userId);
        
        if ($stmtAudit->execute()) {
            // Audit trail insertion successful
            echo json_encode(['success' => true]);
        } else {
            // Error inserting into audit trail
            echo json_encode(['success' => false, 'error' => $stmtAudit->error]);
        }

        $stmtAudit->close();
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
