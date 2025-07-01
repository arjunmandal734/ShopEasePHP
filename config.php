<?php
// Start the session at the very beginning of every page that uses sessions
session_start();

// Database configuration
define('DB_SERVER', 'localhost');
define('DB_USER', 'root'); // Your MySQL username
define('DB_PASS', '');     // Your MySQL password
define('DB_NAME', 'shopease_db'); // The database name created by database.sql

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input data
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return mysqli_real_escape_string($conn, $data);
}

// Function to check if a user is logged in as admin
function is_admin() {
    // In a real application, you would check session variables for user role
    // For this example, we'll assume a simple check or direct access for admin panel
    // A more robust solution would involve a proper login system.
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

?>
