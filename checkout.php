<?php
require_once 'config.php'; // Include database connection and session start

$message = '';
$order_placed = false;

// Check if cart is empty before allowing checkout
if (empty($_SESSION['cart'])) {
    header('Location: cart.php?message=' . urlencode('Your cart is empty. Please add items before checking out.'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // In a real application, you would:
    // 1. Validate user input (shipping address, payment info - not implemented here).
    // 2. Process payment (integrate with a payment gateway).
    // 3. Save order details to a 'orders' table in the database.
    // 4. Update product stock levels.

    // For this example, we'll just simulate order placement.
    // You would fetch customer details from form submission here:
    // $customer_name = sanitize_input($_POST['name']);
    // $customer_email = sanitize_input($_POST['email']);
    // $customer_address = sanitize_input($_POST['address']);

    // Simulate stock update (important for a real store)
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $sql_update_stock = "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?";
        $stmt_update = $conn->prepare($sql_update_stock);
        $stmt_update->bind_param("iii", $item['quantity'], $product_id, $item['quantity']);
        $stmt_update->execute();
        $stmt_update->close();
    }

    // Clear the cart after "successful" checkout
    $_SESSION['cart'] = [];
    $order_placed = true;
    $message = "Your order has been placed successfully! Thank you for your purchase.";

} else {
    // Display current cart items for review before checkout
    $cart_items = $_SESSION['cart'];
    $total_price = 0;
    foreach ($cart_items as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEasePHP - Checkout</title>
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
        <h2>Checkout</h2>

        <?php if ($order_placed): ?>
            <p class="message success"><?php echo $message; ?></p>
            <p><a href="index.php" class="btn btn-primary">Continue Shopping</a></p>
        <?php else: ?>
            <?php if (!empty($message)): ?>
                <p class="message error"><?php echo $message; ?></p>
            <?php endif; ?>

            <h3>Order Summary</h3>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="cart-total">
                Total: $<?php echo number_format($total_price, 2); ?>
            </div>

            <h3>Shipping Information (Placeholder)</h3>
            <form action="checkout.php" method="post">
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="address">Shipping Address:</label>
                    <textarea id="address" name="address" rows="4" required></textarea>
                </div>
                <!-- In a real app, add payment method selection here -->

                <button type="submit" class="btn btn-success">Place Order</button>
            </form>
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
