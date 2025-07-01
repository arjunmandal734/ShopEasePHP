<?php
require_once 'config.php'; // Include database connection and session start

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$message = '';

// Handle cart actions (add, update, remove)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? sanitize_input($_POST['action']) : '';
    $product_id = isset($_POST['product_id']) ? (int)sanitize_input($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? (int)sanitize_input($_POST['quantity']) : 1;

    // Fetch product details from DB to validate
    $sql = "SELECT id, name, price, stock, image_url FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if (!$product) {
        $message = "Product not found.";
    } else {
        switch ($action) {
            case 'add':
                if ($quantity <= 0) {
                    $message = "Quantity must be at least 1.";
                } elseif ($quantity > $product['stock']) {
                    $message = "Not enough stock for " . htmlspecialchars($product['name']) . ". Available: " . htmlspecialchars($product['stock']);
                } else {
                    if (isset($_SESSION['cart'][$product_id])) {
                        // Check if adding more exceeds stock
                        if (($_SESSION['cart'][$product_id]['quantity'] + $quantity) > $product['stock']) {
                            $message = "Cannot add more. Exceeds available stock for " . htmlspecialchars($product['name']) . ".";
                        } else {
                            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                            $message = htmlspecialchars($product['name']) . " quantity updated in cart.";
                        }
                    } else {
                        $_SESSION['cart'][$product_id] = [
                            'id' => $product['id'],
                            'name' => $product['name'],
                            'price' => $product['price'],
                            'quantity' => $quantity,
                            'image_url' => $product['image_url']
                        ];
                        $message = htmlspecialchars($product['name']) . " added to cart.";
                    }
                }
                break;

            case 'update':
                if ($quantity <= 0) {
                    unset($_SESSION['cart'][$product_id]); // Remove if quantity is 0 or less
                    $message = htmlspecialchars($product['name']) . " removed from cart.";
                } elseif ($quantity > $product['stock']) {
                    $message = "Cannot update. Not enough stock for " . htmlspecialchars($product['name']) . ". Available: " . htmlspecialchars($product['stock']);
                } else {
                    $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                    $message = htmlspecialchars($product['name']) . " quantity updated.";
                }
                break;

            case 'remove':
                if (isset($_SESSION['cart'][$product_id])) {
                    unset($_SESSION['cart'][$product_id]);
                    $message = htmlspecialchars($product['name']) . " removed from cart.";
                }
                break;

            case 'clear':
                $_SESSION['cart'] = [];
                $message = "Cart cleared.";
                break;
        }
    }
    // Redirect to prevent form resubmission on refresh
    header('Location: cart.php?message=' . urlencode($message));
    exit();
}

// Display messages from redirect
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}

$cart_items = $_SESSION['cart'];
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEasePHP - Your Cart</title>
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
                    <li><a href="cart.php">Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
                    <li><a href="admin/dashboard.php">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Your Shopping Cart</h2>

        <?php if (!empty($message)): ?>
            <p class="message success"><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if (empty($cart_items)): ?>
            <p>Your cart is empty. <a href="index.php">Start shopping!</a></p>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image" onerror="this.onerror=null;this.src='https://placehold.co/80x80/E0E0E0/333333?text=No+Image';"></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <form action="cart.php" method="post" style="display:inline-flex; align-items:center; gap:5px;">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" style="width: 60px;">
                                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 5px 10px;">Update</button>
                                </form>
                            </td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            <td>
                                <form action="cart.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit" class="btn btn-danger btn-sm" style="padding: 5px 10px;">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-total">
                Total: $<?php echo number_format($total_price, 2); ?>
            </div>

            <div class="cart-actions">
                <form action="cart.php" method="post">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit" class="btn btn-danger">Clear Cart</button>
                </form>
                <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
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
