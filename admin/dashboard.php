<?php
require_once '../config.php'; // Include database connection and session start

// Simple admin authentication check (for demonstration purposes)
// In a real application, implement a proper login system.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // For this example, we'll just set it to true if not set, simulating a "logged in" state.
    // REMOVE THIS LINE IN PRODUCTION AND IMPLEMENT A REAL LOGIN!
    $_SESSION['admin_logged_in'] = true;
    // header('Location: login.php'); // Redirect to a login page in a real scenario
    // exit();
}

// Fetch all products for the dashboard
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);

$message = '';
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEasePHP - Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <h1>Admin Dashboard</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">View Store</a></li>
                    <li><a href="dashboard.php">Products</a></li>
                    <li><a href="add_product.php">Add New Product</a></li>
                    <!-- Add a logout link in a real app -->
                    <!-- <li><a href="logout.php">Logout</a></li> -->
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Product Management</h2>

        <?php if (!empty($message)): ?>
            <p class="message success"><?php echo $message; ?></p>
        <?php endif; ?>

        <p><a href="add_product.php" class="btn btn-primary">Add New Product</a></p>

        <?php if ($result->num_rows > 0): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['stock']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" onerror="this.onerror=null;this.src='https://placehold.co/50x50/E0E0E0/333333?text=Img';"></td>
                            <td class="actions">
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">Edit</a>
                                <form action="delete_product.php" method="post" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No products found. Add some new products!</p>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> ShopEasePHP. Admin Panel.</p>
        </div>
    </footer>
</body>
</html>
<?php
$conn->close(); // Close database connection
?>
