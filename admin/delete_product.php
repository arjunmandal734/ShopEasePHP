<?php
require_once '../config.php'; // Include database connection and session start

// Simple admin authentication check (for demonstration purposes)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // header('Location: login.php'); // Redirect to a login page in a real scenario
    // exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
        $product_id = (int)sanitize_input($_POST['product_id']);

        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);

        if ($stmt->execute()) {
            $message = "Product deleted successfully!";
        } else {
            $message = "Error deleting product: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "No product ID provided for deletion.";
    }
} else {
    $message = "Invalid request method.";
}

$conn->close(); // Close database connection

// Redirect back to the dashboard with a message
header('Location: dashboard.php?message=' . urlencode($message));
exit();
?>
