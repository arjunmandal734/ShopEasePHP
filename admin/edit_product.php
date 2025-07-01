<?php
require_once '../config.php'; // Include database connection and session start

// Simple admin authentication check (for demonstration purposes)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // header('Location: login.php'); // Redirect to a login page in a real scenario
    // exit();
}

$product = null;
$message = '';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = sanitize_input($_GET['id']);

    // Fetch product details for editing
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        $message = "Product not found.";
    }
    $stmt->close();
} else {
    $message = "No product ID provided for editing.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $product) {
    $product_id = (int)sanitize_input($_POST['product_id']);
    $name = sanitize_input($_POST['name']);
    $description = sanitize_input($_POST['description']);
    $price = (float)sanitize_input($_POST['price']);
    $stock = (int)sanitize_input($_POST['stock']);
    $image_url = sanitize_input($_POST['image_url']);

    // Basic validation
    if (empty($name) || empty($price) || empty($stock)) {
        $message = "Please fill in all required fields (Name, Price, Stock).";
    } elseif ($price <= 0) {
        $message = "Price must be a positive number.";
    } elseif ($stock < 0) {
        $message = "Stock cannot be negative.";
    } else {
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image_url = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdiii", $name, $description, $price, $stock, $image_url, $product_id);

        if ($stmt->execute()) {
            $message = "Product updated successfully!";
            // Refresh product data after update
            $sql_refresh = "SELECT * FROM products WHERE id = ?";
            $stmt_refresh = $conn->prepare($sql_refresh);
            $stmt_refresh->bind_param("i", $product_id);
            $stmt_refresh->execute();
            $result_refresh = $stmt_refresh->get_result();
            $product = $result_refresh->fetch_assoc();
            $stmt_refresh->close();
        } else {
            $message = "Error updating product: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEasePHP - Edit Product</title>
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
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Edit Product</h2>

        <?php if (!empty($message)): ?>
            <p class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>"><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if ($product): ?>
            <form action="edit_product.php?id=<?php echo htmlspecialchars($product['id']); ?>" method="post">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                <div class="form-group">
                    <label for="name">Product Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock Quantity:</label>
                    <input type="number" id="stock" name="stock" min="0" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="image_url">Image URL:</label>
                    <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>">
                    <small>e.g., https://placehold.co/400x300/E0E0E0/333333?text=Product</small>
                </div>
                <button type="submit" class="btn btn-success">Update Product</button>
                <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            </form>
        <?php elseif (empty($message)): ?>
            <p class="message error">Product not found or invalid ID.</p>
            <p><a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a></p>
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
