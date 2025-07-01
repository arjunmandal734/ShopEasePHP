-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `shopease_db`;

-- Use the created database
USE `shopease_db`;

-- Table for products
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(10, 2) NOT NULL,
    `image_url` VARCHAR(255),
    `stock` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for a simple admin user (for demonstration)
-- In a real application, you'd want more robust user management and password hashing.
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL, -- Store hashed passwords!
    `role` VARCHAR(50) DEFAULT 'user',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert a sample admin user (Password: admin123 - HASH THIS IN PRODUCTION!)
-- For production, replace 'admin123' with a strong, hashed password.
-- You can generate a hash using password_hash('admin123', PASSWORD_DEFAULT) in PHP.
INSERT INTO `users` (`username`, `password`, `role`) VALUES
('admin', 'admin123', 'admin') ON DUPLICATE KEY UPDATE `password` = 'admin123';

-- Insert some sample products
INSERT INTO `products` (`name`, `description`, `price`, `image_url`, `stock`) VALUES
('Laptop Pro X', 'Powerful laptop with 16GB RAM and 512GB SSD.', 1200.00, 'https://placehold.co/400x300/E0E0E0/333333?text=Laptop', 10),
('Wireless Mouse', 'Ergonomic wireless mouse with adjustable DPI.', 25.00, 'https://placehold.co/400x300/E0E0E0/333333?text=Mouse', 50),
('Mechanical Keyboard', 'RGB mechanical keyboard with blue switches.', 75.00, 'https://placehold.co/400x300/E0E0E0/333333?text=Keyboard', 30),
('USB-C Hub', '7-in-1 USB-C hub with HDMI, USB 3.0, and SD card reader.', 40.00, 'https://placehold.co/400x300/E0E0E0/333333?text=USB+Hub', 20),
('External SSD 1TB', 'High-speed portable SSD for all your data.', 150.00, 'https://placehold.co/400x300/E0E0E0/333333?text=SSD', 15);

