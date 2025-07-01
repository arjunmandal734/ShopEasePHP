<?php
require_once 'config.php'; // Include database connection and session start

// Fetch all products from the database
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEasePHP - Home</title>
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
        <h2>Our Products</h2>
        <div class="product-grid">
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
            ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" onerror="this.onerror=null;this.src='https://placehold.co/400x300/E0E0E0/333333?text=No+Image';">
                        <div class="product-card-content">
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="price">$<?php echo number_format($row['price'], 2); ?></p>
                            <div class="actions">
                                <a href="product_details.php?id=<?php echo $row['id']; ?>" class="btn btn-info">View Details</a>
                                <form action="cart.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="action" value="add">
                                    <button type="submit" class="btn btn-success">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p>No products found.</p>";
            }
            ?>
        </div>
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
