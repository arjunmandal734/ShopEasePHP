<?php
require_once 'config.php'; // Include database connection and session start

$product = null;
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = sanitize_input($_GET['id']);

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
    $message = "No product ID provided.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEasePHP - Product Details</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <h1>ShopEasePHP</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="cart.php">Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a></li>
                    <li><a href="admin/dashboard.php">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php if (isset($message)): ?>
            <p class="message error"><?php echo $message; ?></p>
        <?php elseif ($product): ?>
            <div class="product-detail">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" onerror="this.onerror=null;this.src='https://placehold.co/400x300/E0E0E0/333333?text=No+Image';">
                <div class="product-info">
                    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                    <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    <p><strong>Availability:</strong> <?php echo $product['stock'] > 0 ? htmlspecialchars($product['stock']) . ' in stock' : '<span style="color:red;">Out of Stock</span>'; ?></p>

                    <?php if ($product['stock'] > 0): ?>
                        <form action="cart.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <input type="hidden" name="action" value="add">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo htmlspecialchars($product['stock']); ?>">
                            <button type="submit" class="btn btn-success">Add to Cart</button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-danger" disabled>Out of Stock</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <p><a href="index.php" class="btn btn-primary">Back to Products</a></p>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> ShopEasePHP. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
<?php
$conn->close(); // Close database connection
?>
